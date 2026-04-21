<?php

namespace Tests\Feature;

use App\Enums\ShortLinkStatus;
use App\Filament\Resources\ShortLinks\ShortLinkResource;
use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortLinkFilamentResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'user_type' => 'admin',
        ]);

        $this->actingAs($this->admin);
    }

    public function test_navigation_badge_shows_short_link_count(): void
    {
        ShortLink::factory()->count(2)->create([
            'created_by' => $this->admin->id,
        ]);

        $this->assertSame('2', ShortLinkResource::getNavigationBadge());
    }

    public function test_short_link_resource_allows_creating_model_data(): void
    {
        $shortLink = ShortLink::query()->create([
            'created_by' => $this->admin->id,
            'slug' => 'lanzamiento',
            'title' => 'Landing lanzamiento',
            'destination_url' => 'https://example.com/lanzamiento',
            'status' => ShortLinkStatus::ACTIVE,
            'tag_manager_id' => 'GTM-TEST123',
            'metadata' => ['source' => 'admin'],
        ]);

        $this->assertDatabaseHas('short_links', [
            'id' => $shortLink->id,
            'slug' => 'lanzamiento',
            'status' => ShortLinkStatus::ACTIVE->value,
        ]);
    }
}
