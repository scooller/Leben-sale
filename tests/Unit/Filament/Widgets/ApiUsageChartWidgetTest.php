<?php

namespace Tests\Unit\Filament\Widgets;

use App\Filament\Widgets\ApiUsageChartWidget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiUsageChartWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_last_7_days_dataset(): void
    {
        $user = User::factory()->create();

        $recentToken = $user->createToken('recent-usage');
        $recentToken->accessToken->forceFill([
            'last_used_at' => now()->subDay(),
        ])->save();

        $oldToken = $user->createToken('old-usage');
        $oldToken->accessToken->forceFill([
            'last_used_at' => now()->subDays(20),
        ])->save();

        $widget = new class extends ApiUsageChartWidget
        {
            public function exposeData(): array
            {
                return $this->getData();
            }
        };

        $data = $widget->exposeData();

        $this->assertCount(7, $data['labels']);
        $this->assertCount(7, $data['datasets'][0]['data']);
        $this->assertSame('Uso API', $data['datasets'][0]['label']);
        $this->assertContains(1, $data['datasets'][0]['data']);
    }
}
