<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PlantReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlantReservationController extends Controller
{
    public function __construct(
        private readonly PlantReservationService $reservationService,
    ) {}

    /**
     * Reserve a plant. Called when PaymentGatewayDialog opens.
     */
    public function reserve(Request $request): JsonResponse
    {
        $request->validate([
            'plant_id' => ['required', 'integer', 'exists:plants,id'],
        ]);

        try {
            $reservation = $this->reservationService->reserve(
                plantId: (int) $request->input('plant_id'),
                userId: $request->user()->id,
                metadata: [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
            );

            return response()->json([
                'reservation' => [
                    'id' => $reservation->id,
                    'session_token' => $reservation->session_token,
                    'plant_id' => $reservation->plant_id,
                    'status' => $reservation->status->value,
                    'expires_at' => $reservation->expires_at->toISOString(),
                    'remaining_seconds' => $reservation->remainingSeconds(),
                ],
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => 'plant_unavailable',
            ], Response::HTTP_NOT_FOUND);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => 'already_reserved',
            ], Response::HTTP_CONFLICT);
        }
    }

    /**
     * Release a reservation. Called when PaymentGatewayDialog closes without purchase.
     */
    public function release(Request $request, string $sessionToken): JsonResponse
    {
        $released = $this->reservationService->releaseByToken($sessionToken, 'user');

        if (! $released) {
            return response()->json([
                'message' => 'Reserva no encontrada o ya expirada.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Reserva liberada exitosamente.',
        ]);
    }

    /**
     * Check reservation status for a plant (public endpoint for badges).
     */
    public function status(int $plantId): JsonResponse
    {
        $reservation = $this->reservationService->checkPlantStatus($plantId);

        if (! $reservation) {
            return response()->json([
                'reserved' => false,
            ]);
        }

        return response()->json([
            'reserved' => true,
            'expires_at' => $reservation->expires_at->toISOString(),
            'remaining_seconds' => $reservation->remainingSeconds(),
        ]);
    }
}
