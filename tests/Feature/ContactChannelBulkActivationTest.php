<?php

namespace Tests\Feature;

use App\Models\ContactChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactChannelBulkActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_set_active_can_activate_multiple_channels(): void
    {
        $inactiveA = ContactChannel::factory()->inactive()->create();
        $inactiveB = ContactChannel::factory()->inactive()->create();

        $result = ContactChannel::bulkSetActive([$inactiveA->id, $inactiveB->id], true);

        $this->assertSame(2, $result['updated']);
        $this->assertSame(0, $result['skipped']);

        $this->assertDatabaseHas('contact_channels', [
            'id' => $inactiveA->id,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('contact_channels', [
            'id' => $inactiveB->id,
            'is_active' => true,
        ]);
    }

    public function test_bulk_set_active_skips_default_channel_when_deactivating(): void
    {
        $defaultChannel = ContactChannel::query()->where('slug', 'default')->firstOrFail();
        $activeChannel = ContactChannel::factory()->create([
            'is_active' => true,
            'is_default' => false,
        ]);

        $result = ContactChannel::bulkSetActive([$defaultChannel->id, $activeChannel->id], false);

        $this->assertSame(1, $result['updated']);
        $this->assertSame(1, $result['skipped']);

        $this->assertDatabaseHas('contact_channels', [
            'id' => $defaultChannel->id,
            'is_active' => true,
            'is_default' => true,
        ]);

        $this->assertDatabaseHas('contact_channels', [
            'id' => $activeChannel->id,
            'is_active' => false,
            'is_default' => false,
        ]);
    }
}
