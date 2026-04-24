<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Filament\Resources\Payments\PaymentResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\ManualPaymentProofRequest;
use App\Models\Payment;
use App\Models\User;
use App\Services\FinMail\FinMailNotificationService;
use Exception;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
     * Store payment proof for an existing payment.
     */
    public function uploadManualProof(ManualPaymentProofRequest $request, string $id): JsonResponse
    {
        $authenticatedUser = $request->user();

        /** @var Payment|null $payment */
        $payment = $authenticatedUser
            ->payments()
            ->find($id);

        if (! $payment instanceof Payment) {
            Log::warning('Fallo al subir comprobante manual: pago no encontrado para el usuario autenticado.', [
                'payment_id' => $id,
                'authenticated_user_id' => $authenticatedUser?->id,
                'authenticated_user_email' => $authenticatedUser?->email,
                'authenticated_user_roles' => $authenticatedUser?->getRoleNames()?->values()?->all() ?? [],
                'authenticated_user_type' => $authenticatedUser?->user_type,
                'is_admin' => $authenticatedUser?->isAdmin() ?? false,
                'request_path' => $request->path(),
                'request_ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => sprintf('No query results for model [%s] %s', Payment::class, $id),
            ], Response::HTTP_NOT_FOUND);
        }

        $isManualPayment = $payment->requiresManualApproval();

        $metadata = $payment->metadata ?? [];
        $expiresAt = filled($metadata['manual_payment_expires_at'] ?? null)
            ? Carbon::parse($metadata['manual_payment_expires_at'])
            : null;

        if ($isManualPayment && $expiresAt !== null && $expiresAt->isPast()) {
            Log::warning('Fallo al subir comprobante manual: el plazo del pago ya expiro.', [
                'payment_id' => $payment->id,
                'authenticated_user_id' => $authenticatedUser?->id,
                'manual_payment_expires_at' => $expiresAt->toISOString(),
            ]);

            return response()->json([
                'message' => 'La fecha limite para enviar el comprobante ya expiro.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $previousProofPath = (string) ($metadata['manual_payment_proof_path'] ?? $metadata['payment_proof_path'] ?? '');

        /** @var UploadedFile $proof */
        $proof = $request->file('proof');
        $path = $proof->store('payment-proofs');

        $metadata['manual_payment_proof_path'] = $path;
        $metadata['manual_payment_proof_name'] = $proof->getClientOriginalName();
        $metadata['manual_payment_proof_mime_type'] = $proof->getClientMimeType();
        $metadata['manual_payment_proof_uploaded_at'] = now()->toISOString();
        $metadata['manual_payment_proof_notes'] = $request->validated('notes');
        $metadata['manual_payment_proof_submitted'] = true;

        if (! $isManualPayment) {
            $metadata['payment_proof_path'] = $path;
            $metadata['payment_proof_name'] = $proof->getClientOriginalName();
            $metadata['payment_proof_mime_type'] = $proof->getClientMimeType();
            $metadata['payment_proof_uploaded_at'] = now()->toISOString();
            $metadata['payment_proof_notes'] = $request->validated('notes');
            $metadata['payment_proof_submitted'] = true;
        }

        try {
            DB::beginTransaction();

            $payment->update([
                'metadata' => $metadata,
            ]);

            $freshPayment = $payment->fresh();

            if (! $freshPayment instanceof Payment) {
                DB::rollBack();
                Storage::delete($path);

                Log::warning('No se pudo refrescar el pago manual tras subir comprobante.', [
                    'payment_id' => $payment->id,
                    'authenticated_user_id' => $authenticatedUser?->id,
                ]);

                return response()->json([
                    'message' => 'No se pudo registrar el comprobante. Intenta nuevamente.',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $this->notifyAdminsProofSubmitted($freshPayment, $isManualPayment);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            Storage::delete($path);

            Log::error('No se pudo procesar el comprobante manual de forma atomica.', [
                'payment_id' => $payment->id,
                'authenticated_user_id' => $authenticatedUser?->id,
                'error' => $exception->getMessage(),
                'exception' => get_class($exception),
            ]);

            return response()->json([
                'message' => 'No se pudo registrar el comprobante. Intenta nuevamente.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($previousProofPath !== '' && $previousProofPath !== $path) {
            Storage::delete($previousProofPath);
        }

        return response()->json([
            'message' => 'Comprobante recibido correctamente.',
            'payment' => $payment->fresh(),
            'proof' => [
                'name' => $metadata['manual_payment_proof_name'],
                'uploaded_at' => $metadata['manual_payment_proof_uploaded_at'],
            ],
        ]);
    }

    private function notifyAdminsProofSubmitted(Payment $payment, bool $isManualPayment): void
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

        $paymentReviewUrl = PaymentResource::getUrl(
            'view',
            ['record' => $payment],
            isAbsolute: true,
        );

        FilamentNotification::make()
            ->title('Comprobante de pago recibido')
            ->body("Se recibio un comprobante para el pago {$reference}. Revisar en: {$paymentReviewUrl}")
            ->info()
            ->icon('heroicon-o-document-check')
            ->sendToDatabase($admins);

        $status = $payment->status instanceof PaymentStatus
            ? $payment->status
            : PaymentStatus::fromValue((string) $payment->status);

        if ($isManualPayment) {
            if ($status !== PaymentStatus::PENDING_APPROVAL) {
                return;
            }

            app(FinMailNotificationService::class)
                ->sendManualPaymentProofSubmittedToAdmins($payment, $paymentReviewUrl);

            return;
        }

        app(FinMailNotificationService::class)
            ->sendNonManualPaymentProofSubmittedToAdmins($payment, $paymentReviewUrl);
    }
}
