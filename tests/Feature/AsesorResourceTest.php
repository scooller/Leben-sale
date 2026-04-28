<?php

namespace Tests\Feature;

use App\Filament\Resources\Asesores\AsesorResource;
use App\Filament\Resources\Asesores\Pages\CreateAsesor;
use App\Filament\Resources\Asesores\Pages\EditAsesor;
use App\Filament\Resources\Asesores\RelationManagers\ProyectosRelationManager;
use App\Filament\Resources\Asesores\Tables\AsesoresTable;
use App\Models\Asesor;
use App\Models\Plant;
use App\Models\Proyecto;
use App\Models\ShortLink;
use App\Models\User;
use Filament\Support\Enums\Width;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AsesorResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'user_type' => 'admin',
        ]);
        $this->actingAs($this->user);
    }

    public function test_asesor_resource_registers_proyectos_relation_manager(): void
    {
        $relations = AsesorResource::getRelations();

        $this->assertContains(ProyectosRelationManager::class, $relations);
    }

    public function test_asesor_pages_use_full_content_width(): void
    {
        $createPage = app(CreateAsesor::class);
        $editPage = app(EditAsesor::class);

        $this->assertSame(Width::Full, $createPage->getMaxContentWidth());
        $this->assertSame(Width::Full, $editPage->getMaxContentWidth());
    }

    public function test_asesor_can_have_multiple_proyectos_with_their_plantas(): void
    {
        $asesor = Asesor::factory()->create();
        $proyectoA = Proyecto::factory()->create([
            'name' => 'Edificio Inn',
            'salesforce_id' => 'a00xx0000000001AAA',
        ]);
        $proyectoB = Proyecto::factory()->create([
            'name' => 'Edificio Sky',
            'salesforce_id' => 'a00xx0000000002AAA',
        ]);

        $asesor->proyectos()->attach([$proyectoA->id, $proyectoB->id]);

        Plant::factory()->create([
            'name' => '202',
            'salesforce_proyecto_id' => $proyectoA->salesforce_id,
        ]);
        Plant::factory()->create([
            'name' => '303',
            'salesforce_proyecto_id' => $proyectoA->salesforce_id,
        ]);
        Plant::factory()->create([
            'name' => '1201',
            'salesforce_proyecto_id' => $proyectoB->salesforce_id,
        ]);

        $asesor->load('proyectos.plantas');

        $this->assertCount(2, $asesor->proyectos);
        $this->assertSame(2, $asesor->proyectos->firstWhere('id', $proyectoA->id)?->plantas->count());
        $this->assertSame(1, $asesor->proyectos->firstWhere('id', $proyectoB->id)?->plantas->count());
    }

    public function test_proyectos_relation_manager_renders_related_proyectos_and_plantas_without_errors(): void
    {
        $asesor = Asesor::factory()->create();
        $proyecto = Proyecto::factory()->create([
            'name' => 'Edificio Inn',
            'salesforce_id' => 'a00xx0000000001AAA',
        ]);

        $asesor->proyectos()->attach($proyecto);

        Plant::factory()->create([
            'name' => '202',
            'salesforce_proyecto_id' => $proyecto->salesforce_id,
        ]);
        Plant::factory()->create([
            'name' => '303',
            'salesforce_proyecto_id' => $proyecto->salesforce_id,
        ]);

        Livewire::test(ProyectosRelationManager::class, [
            'ownerRecord' => $asesor,
            'pageClass' => EditAsesor::class,
        ])
            ->assertSee('Proyectos a cargo')
            ->assertSee('Edificio Inn')
            ->assertSee('202')
            ->assertSee('303');
    }

    public function test_asesor_relationship_supports_attaching_and_detaching_proyectos(): void
    {
        $asesor = Asesor::factory()->create();
        $proyectoA = Proyecto::factory()->create();
        $proyectoB = Proyecto::factory()->create();

        $asesor->proyectos()->attach([$proyectoA->id, $proyectoB->id]);

        $this->assertEqualsCanonicalizing(
            [$proyectoA->id, $proyectoB->id],
            $asesor->proyectos()->pluck('proyectos.id')->all(),
        );

        $asesor->proyectos()->detach($proyectoA->id);

        $this->assertSame([$proyectoB->id], $asesor->proyectos()->pluck('proyectos.id')->all());
    }

    public function test_create_qr_action_creates_short_link_for_advisor_whatsapp_redirect(): void
    {
        $asesor = Asesor::factory()->create([
            'first_name' => 'Camila',
            'last_name' => 'Diaz',
            'whatsapp_owner' => '+56 9 8765 4321',
            'is_active' => true,
        ]);

        $shortUrl = AsesoresTable::resolveOrCreateWhatsappShortLinkUrl($asesor);

        $shortLink = ShortLink::query()
            ->where('metadata->origin', 'advisor_whatsapp_qr')
            ->where('metadata->advisor_id', $asesor->id)
            ->first();

        $this->assertNotNull($shortLink);
        $this->assertSame(route('advisors.whatsapp.redirect', ['asesor' => $asesor]), $shortLink?->destination_url);
        $this->assertSame($shortLink?->shortUrl(), $shortUrl);
    }

    public function test_create_qr_action_reuses_existing_short_link_for_same_advisor(): void
    {
        $asesor = Asesor::factory()->create([
            'first_name' => 'Camila',
            'last_name' => 'Diaz',
            'whatsapp_owner' => '+56 9 8765 4321',
            'is_active' => true,
        ]);

        $firstShortUrl = AsesoresTable::resolveOrCreateWhatsappShortLinkUrl($asesor);
        $secondShortUrl = AsesoresTable::resolveOrCreateWhatsappShortLinkUrl($asesor);

        $this->assertSame($firstShortUrl, $secondShortUrl);
        $this->assertSame(1, ShortLink::query()
            ->where('metadata->origin', 'advisor_whatsapp_qr')
            ->where('metadata->advisor_id', $asesor->id)
            ->count());
    }

    public function test_existing_qr_short_link_url_returns_null_when_not_created_yet(): void
    {
        $asesor = Asesor::factory()->create([
            'whatsapp_owner' => '+56 9 8765 4321',
            'is_active' => true,
        ]);

        $existingShortUrl = AsesoresTable::resolveExistingWhatsappShortLinkUrl($asesor);

        $this->assertNull($existingShortUrl);
    }

    public function test_existing_qr_short_link_url_returns_created_short_link(): void
    {
        $asesor = Asesor::factory()->create([
            'whatsapp_owner' => '+56 9 8765 4321',
            'is_active' => true,
        ]);

        $createdShortUrl = AsesoresTable::resolveOrCreateWhatsappShortLinkUrl($asesor);
        $existingShortUrl = AsesoresTable::resolveExistingWhatsappShortLinkUrl($asesor);

        $this->assertSame($createdShortUrl, $existingShortUrl);
    }
}
