<?php

namespace Tests\Unit\Filament\Widgets;

use App\Filament\Widgets\ApiMonitoringWidget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiMonitoringWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_api_stats_without_errors_when_no_tokens_exist(): void
    {
        $widget = new class extends ApiMonitoringWidget
        {
            public function exposeStats(): array
            {
                return $this->getStats();
            }
        };

        $stats = $widget->exposeStats();

        $this->assertCount(3, $stats);
        $this->assertSame('0', $stats[0]->getValue());
        $this->assertSame('0', $stats[1]->getValue());
        $this->assertSame('0', $stats[2]->getValue());
    }

    public function test_it_counts_active_recent_and_expiring_tokens(): void
    {
        $user = User::factory()->create();

        $activeRecent = $user->createToken('active-recent');
        $activeRecent->accessToken->forceFill([
            'last_used_at' => now()->subHour(),
            'expires_at' => now()->addDays(3),
        ])->save();

        $expiredToken = $user->createToken('expired-token');
        $expiredToken->accessToken->forceFill([
            'last_used_at' => now()->subDays(2),
            'expires_at' => now()->subDay(),
        ])->save();

        $widget = new class extends ApiMonitoringWidget
        {
            public function exposeStats(): array
            {
                return $this->getStats();
            }
        };

        $stats = $widget->exposeStats();

        $this->assertSame('1', $stats[0]->getValue()); // active tokens
        $this->assertSame('1', $stats[1]->getValue()); // used in 24h
        $this->assertSame('1', $stats[2]->getValue()); // expiring in 7 days
    }
}
