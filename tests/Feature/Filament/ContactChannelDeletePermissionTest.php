<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\ContactChannels\ContactChannelResource;
use App\Models\ContactChannel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactChannelDeletePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_delete_default_channel(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);
        $this->actingAs($admin);

        $defaultChannel = ContactChannel::query()->where('slug', 'default')->firstOrFail();

        $this->assertFalse(ContactChannelResource::canDelete($defaultChannel));
    }

    public function test_admin_can_delete_non_default_channel(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);
        $this->actingAs($admin);

        $saleChannel = ContactChannel::query()->where('slug', 'sale')->firstOrFail();

        $this->assertTrue(ContactChannelResource::canDelete($saleChannel));
    }
}
