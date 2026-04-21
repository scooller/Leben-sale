<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ManualPaymentProofRequest;
use App\Models\Payment;
use App\Models\User;
use App\Services\FinMail\FinMailNotificationService;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $payments = $request->user()
            ->payments()
            ->latest()
            ->paginate(15);

        return response()->json($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'gateway' => 'required|string|in:transbank,mercadopago',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|in:CLP,USD',
            'metadata' => 'nullable|array',
        ]);

        $payment = $request->user()->payments()->create([
            'gateway' => $validated['gateway'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'status' => 'pending',
            'metadata' => $validated['metadata'] ?? [],
        ]);

        // TODO: Integrar con servicio de pago correspondiente
        // $paymentService = $validated['gateway'] === 'transbank'
        //     ? app(TransbankService::class)
        //     : app(MercadoPagoService::class);

        return response()->json($payment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $payment = $request->user()
            ->payments()
            ->findOrFail($id);

        return response()->json($payment);
    }

    /**
     * Display a public-safe payment status using payment id + status token.
     */
    public function publicStatus(Request $request, string $id): JsonResponse
    {
        $statusToken = (string) $request->query('token', '');

        if ($statusToken === '') {
            return response()->json([
                'message' => 'Token de estado no proporcionado.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $payment = Payment::query()->findOrFail($id);
        $expectedToken = (string) data_get($payment->metadata, 'public_status_token', '');

        if ($expectedToken === '' || ! Str::isUuid($expectedToken) || ! \hash_equals($expectedToken, $statusToken)) {
            return response()->json([
                'message' => 'Token de estado inválido.',
            ], Response::HTTP_NOT_FOUND);
        }

        $status = $payment->status instanceof PaymentStatus
            ? $payment->status
            : PaymentStatus::fromValue((string) $payment->status);

        return response()->json([
            'id' => $payment->id,
            'gateway' => $payment->gateway instanceof \BackedEnum ? $payment->gateway->value : (string) $payment->gateway,
            'gateway_tx_id' => $payment->gateway_tx_id,
            'amount' => (float) $payment->amount,
            'currency' => $payment->currency,
            'status' => $status?->value,
            'status_label' => $status?->label(),
            'updated_at' => $payment->updated_at?->toISOString(),
        ]);
    }

    /**
     * Store manual payment proof for an existing manual payment.
     */
    public function uploadManualProof(ManualPaymentProofRequest $request, string $id): JsonResponse
    {
        /** @var Payment $payment */
        $payment = $request->user()
            ->payments()
            ->findOrFail($id);

        if (! $payment->requiresManualApproval()) {
            return response()->json([
                'message' => 'Este pago no admite comprobante manual.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $metadata = $payment->metadata ?? [];
        $expiresAt = filled($metadata['manual_payment_expires_at'] ?? null)
            ? Carbon::parse($metadata['manual_payment_expires_at'])
            : null;

        if ($expiresAt !== null && $expiresAt->isPast()) {
            return response()->json([
                'message' => 'La fecha limite para enviar el comprobante ya expiro.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (filled($metadata['manual_payment_proof_path'] ?? null)) {
            Storage::delete($metadata['manual_payment_proof_path']);
        }

        /** @var UploadedFile $proof */
        $proof = $request->file('proof');
        $path = $proof->store('payment-proofs');

        $metadata['manual_payment_proof_path'] = $path;
        $metadata['manual_payment_proof_name'] = $proof->getClientOriginalName();
        $metadata['manual_payment_proof_mime_type'] = $proof->getClientMimeType();
        $metadata['manual_payment_proof_uploaded_at'] = now()->toISOString();
        $metadata['manual_payment_proof_notes'] = $request->validated('notes');
        $metadata['manual_payment_proof_submitted'] = true;

        $payment->update([
            'metadata' => $metadata,
        ]);

        $this->notifyAdminsManualProofSubmitted($payment->fresh());

        return response()->json([
            'message' => 'Comprobante recibido correctamente.',
            'payment' => $payment->fresh(),
            'proof' => [
                'name' => $metadata['manual_payment_proof_name'],
                'uploaded_at' => $metadata['manual_payment_proof_uploaded_at'],
            ],
        ]);
    }

    private function notifyAdminsManualProofSubmitted(Payment $payment): void
    {
        $admins = User::query()
            ->get()
            ->filter(static fn (User $user): bool => $user->isAdmin())
            ->values();

        if ($admins->isEmpty()) {
            return;
        }

        $admins = $admins
            ->filter(static fn (User $user): bool => filled($user->email))
            ->values();

        if ($admins->isEmpty()) {
            return;
        }

        $reference = filled($payment->gateway_tx_id)
            ? (string) $payment->gateway_tx_id
            : '#'.$payment->getKey();

        FilamentNotification::make()
            ->title('Comprobante de pago recibido')
            ->body("Se recibio un comprobante para el pago {$reference}.")
            ->info()
            ->icon('heroicon-o-document-check')
            ->sendToDatabase($admins);

        $status = $payment->status instanceof PaymentStatus
            ? $payment->status
            : PaymentStatus::fromValue((string) $payment->status);

        if ($status !== PaymentStatus::PENDING_APPROVAL) {
            return;
        }

        app(FinMailNotificationService::class)
            ->sendManualPaymentProofSubmittedToAdmins($payment);
    }
}
