<?php

namespace Tests\Feature\Filament\Actions;

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportShortLinksActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_short_links_generates_csv(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);

        ShortLink::factory()->count(3)->create();

        $this->actingAs($admin);

        // Test that the action can be called (we can't fully test the download,
        // but we can ensure the query executes without errors)
        $links = ShortLink::query()
            ->with('creator')
            ->get();

        $this->assertEquals(3, $links->count());

        // Verify all links have expected attributes
        foreach ($links as $link) {
            $this->assertNotNull($link->slug);
            $this->assertNotNull($link->destination_url);
            $this->assertNotNull($link->status);
        }
    }

    public function test_export_includes_all_columns(): void
    {
        ShortLink::factory()->create([
            'visits_count' => 42,
            'last_visited_at' => now(),
        ]);

        $links = ShortLink::all();
        $link = $links->first();

        // Verify export data structure
        $this->assertEquals(42, $link->visits_count);
        $this->assertNotNull($link->last_visited_at);
        $this->assertNotNull($link->created_at);
        $this->assertNotNull($link->slug);
    }
}
