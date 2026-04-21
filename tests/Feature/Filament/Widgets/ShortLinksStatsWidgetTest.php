<?php

namespace Tests\Feature\Filament\Widgets;

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ShortLinksStatsWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_displays_short_links_statistics(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);

        // Create test short links
        ShortLink::factory()->count(3)->create(['status' => 'active']);
        ShortLink::factory()->disabled()->create();
        ShortLink::factory()->expired()->create();

        $this->actingAs($admin);

        Livewire::test(\App\Filament\Widgets\ShortLinksStatsWidget::class)
            ->assertSet('totalLinks', 5)
            ->assertSet('activeLinks', 3);
    }

    public function test_widget_counts_visits(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);

        ShortLink::factory()->create(['visits_count' => 42]);

        $this->actingAs($admin);

        Livewire::test(\App\Filament\Widgets\ShortLinksStatsWidget::class)
            ->assertSet('totalVisits', 42);
    }
}
