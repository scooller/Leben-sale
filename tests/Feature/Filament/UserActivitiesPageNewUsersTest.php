<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\UserActivitiesPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class UserActivitiesPageNewUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_created_user_event_in_user_activities_page(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'user_type' => 'admin',
        ]);

        $this->actingAs($admin);

        Activity::query()->delete();

        $newUser = User::factory()->create([
            'name' => 'Usuario Evento',
            'email' => 'usuario-evento@example.com',
        ]);

        $createdUserActivity = Activity::query()
            ->where('subject_type', User::class)
            ->where('subject_id', $newUser->getKey())
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($createdUserActivity);
        $this->assertSame($admin->getKey(), $createdUserActivity->causer_id);

        Livewire::test(UserActivitiesPage::class)
            ->assertSee('created User')
            ->assertSee('User')
            ->assertSee($admin->name);
    }

    public function test_it_shows_updated_user_event_in_user_activities_page(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'user_type' => 'admin',
        ]);

        $this->actingAs($admin);

        $userToUpdate = User::factory()->create([
            'name' => 'Usuario Antes',
            'email' => 'usuario-antes@example.com',
        ]);

        Activity::query()->delete();

        $userToUpdate->update([
            'name' => 'Usuario Despues',
        ]);

        $updatedUserActivity = Activity::query()
            ->where('subject_type', User::class)
            ->where('subject_id', $userToUpdate->getKey())
            ->where('event', 'updated')
            ->first();

        $this->assertNotNull($updatedUserActivity);
        $this->assertSame($admin->getKey(), $updatedUserActivity->causer_id);
        $this->assertSame('Usuario Antes', $updatedUserActivity->properties['old']['name']);
        $this->assertSame('Usuario Despues', $updatedUserActivity->properties['attributes']['name']);

        Livewire::test(UserActivitiesPage::class)
            ->assertSee('updated User')
            ->assertSee('User')
            ->assertSee($admin->name);
    }
}
