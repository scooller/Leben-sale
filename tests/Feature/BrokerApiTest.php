<?php

namespace Tests\Feature;

use App\Models\Broker;
use App\Models\BrokerAlliance;
use App\Models\BrokerBenefit;
use App\Models\BrokerEvent;
use App\Models\BrokerGallery;
use App\Models\BrokerGalleryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrokerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_broker_index_returns_category_and_benefits(): void
    {
        $broker = Broker::factory()->create();

        BrokerBenefit::factory()->create([
            'broker_category_id' => $broker->broker_category_id,
            'title' => 'Contacto semanal KAM',
            'status' => 'included',
            'section' => 'comunicacion',
        ]);

        $response = $this->getJson('/api/v1/brokers');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $broker->id)
            ->assertJsonPath('data.0.category.id', $broker->broker_category_id)
            ->assertJsonPath('data.0.category.benefits.0.title', 'Contacto semanal KAM');
    }

    public function test_broker_related_endpoints_return_active_and_published_records(): void
    {
        $broker = Broker::factory()->create();

        BrokerAlliance::factory()->create([
            'broker_id' => $broker->id,
            'name' => 'Marca Activa',
            'is_active' => true,
        ]);

        BrokerAlliance::factory()->create([
            'broker_id' => $broker->id,
            'name' => 'Marca Inactiva',
            'is_active' => false,
        ]);

        BrokerEvent::factory()->create([
            'broker_id' => $broker->id,
            'title' => 'Evento Publicado',
            'is_published' => true,
        ]);

        BrokerEvent::factory()->create([
            'broker_id' => $broker->id,
            'title' => 'Evento Oculto',
            'is_published' => false,
        ]);

        $gallery = BrokerGallery::factory()->create([
            'broker_id' => $broker->id,
            'title' => 'Galeria Mayo',
            'year' => 2026,
            'month' => 5,
            'is_published' => true,
        ]);

        BrokerGalleryItem::factory()->create([
            'broker_gallery_id' => $gallery->id,
            'caption' => 'Imagen principal',
            'is_active' => true,
        ]);

        BrokerGalleryItem::factory()->create([
            'broker_gallery_id' => $gallery->id,
            'caption' => 'Imagen oculta',
            'is_active' => false,
        ]);

        $this->getJson("/api/v1/brokers/{$broker->id}/alliances")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Marca Activa');

        $this->getJson("/api/v1/brokers/{$broker->id}/events")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Evento Publicado');

        $this->getJson("/api/v1/brokers/{$broker->id}/galleries")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Galeria Mayo');

        $this->getJson("/api/v1/brokers/{$broker->id}/galleries/{$gallery->id}")
            ->assertOk()
            ->assertJsonPath('data.items.0.caption', 'Imagen principal')
            ->assertJsonCount(1, 'data.items');
    }
}
