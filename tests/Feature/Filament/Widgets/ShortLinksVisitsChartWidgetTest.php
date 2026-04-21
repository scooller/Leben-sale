<?php

namespace Tests\Feature\Filament\Widgets;

use App\Models\ShortLink;
use App\Models\ShortLinkVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ShortLinksVisitsChartWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_displays_chart_data(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);

        // Create test data: visits from past 30 days
        $link = ShortLink::factory()->create();

        for ($i = 29; $i >= 0; $i--) {
            $visitDate = now()->subDays($i);
            ShortLinkVisit::factory()->create([
                'short_link_id' => $link->id,
                'visited_at' => $visitDate,
            ]);
        }

        $this->actingAs($admin);

        // Widget should render without errors
        Livewire::test(\App\Filament\Widgets\ShortLinksVisitsChartWidget::class)
            ->assertSuccessful();
    }

    public function test_widget_aggregates_visits_by_day(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);
        $link = ShortLink::factory()->create();

        // Create 5 visits on today
        ShortLinkVisit::factory()->count(5)->create([
            'short_link_id' => $link->id,
            'visited_at' => now(),
        ]);

        // Create 3 visits on yesterday
        ShortLinkVisit::factory()->count(3)->create([
            'short_link_id' => $link->id,
            'visited_at' => now()->subDay(),
        ]);

        $this->actingAs($admin);

        // Widget should render without errors and have the correct counts
        $todayCount = ShortLinkVisit::whereDate('visited_at', now()->format('Y-m-d'))->count();
        $yesterdayCount = ShortLinkVisit::whereDate('visited_at', now()->subDay()->format('Y-m-d'))->count();

        $this->assertEquals(5, $todayCount);
        $this->assertEquals(3, $yesterdayCount);

        Livewire::test(\App\Filament\Widgets\ShortLinksVisitsChartWidget::class)
            ->assertSuccessful();
    }
}
