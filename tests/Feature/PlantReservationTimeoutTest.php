<?php

namespace Tests\Feature;

use App\Models\Plant;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\PlantReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlantReservationTimeoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_uses_configurable_timeout_from_site_settings(): void
    {
        SiteSetting::current()->update([
            'gateway_reservation_timeout_minutes' => 30,
        ]);

        $user = User::factory()->create();
        $plant = Plant::factory()->create([
            'is_active' => true,
        ]);

        $reservation = app(PlantReservationService::class)->reserve($plant->id, $user->id);

        $expectedMin = now()->addMinutes(29);
        $expectedMax = now()->addMinutes(31);

        $this->assertTrue(
            $reservation->expires_at->between($expectedMin, $expectedMax),
            sprintf(
                'Expected expiration between %s and %s, got %s',
                $expectedMin->toDateTimeString(),
                $expectedMax->toDateTimeString(),
                $reservation->expires_at->toDateTimeString(),
            )
        );
    }

    public function test_it_enforces_minimum_timeout_when_setting_is_invalid(): void
    {
        SiteSetting::current()->update([
            'gateway_reservation_timeout_minutes' => 0,
        ]);

        $user = User::factory()->create();
        $plant = Plant::factory()->create([
            'is_active' => true,
        ]);

        $reservation = app(PlantReservationService::class)->reserve($plant->id, $user->id);

        $remainingSeconds = now()->diffInSeconds($reservation->expires_at, false);

        $this->assertGreaterThanOrEqual(50, $remainingSeconds);
        $this->assertLessThanOrEqual(70, $remainingSeconds);
    }
}
