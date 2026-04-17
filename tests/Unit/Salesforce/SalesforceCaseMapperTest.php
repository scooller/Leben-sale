<?php

namespace Tests\Unit\Salesforce;

use App\Models\ContactSubmission;
use App\Models\Proyecto;
use App\Models\SiteSetting;
use App\Services\Salesforce\SalesforceCaseMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesforceCaseMapperTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_maps_contact_submission_into_salesforce_lead_payload(): void
    {
        config()->set('services.salesforce.lead_owner_id', '005U100000CAG4bIAH');
        config()->set('services.salesforce.lead_status', 'En Contacto');

        SiteSetting::current()->update([
            'site_name' => 'iLeben',
            'contact_email' => 'inscripciones@ileben.cl',
            'contact_form_fields' => [
                ['key' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
                ['key' => 'project_name', 'label' => 'Proyecto', 'type' => 'text', 'required' => false],
                ['key' => 'arrival_channel', 'label' => 'Medio de llegada', 'type' => 'text', 'required' => false],
                ['key' => 'rango', 'label' => 'Rango de renta', 'type' => 'select', 'required' => false],
                ['key' => 'codeudor', 'label' => 'Complementa renta', 'type' => 'select', 'required' => false],
                ['key' => 'validacion_renta', 'label' => 'Validación renta', 'type' => 'select', 'required' => false],
                ['key' => 'buscas', 'label' => 'Uso departamento', 'type' => 'select', 'required' => false],
                ['key' => 'elaboral', 'label' => 'Estado laboral', 'type' => 'select', 'required' => false],
                ['key' => 'comuna_inversion', 'label' => 'Comuna inversión', 'type' => 'text', 'required' => false],
            ],
        ]);

        Proyecto::query()->create([
            'salesforce_id' => 'a0J8c00000sdxCXEAY',
            'name' => 'Edificio Indigo',
            'slug' => 'edificio-indigo',
            'is_active' => true,
        ]);

        $submission = ContactSubmission::query()->create([
            'name' => 'Alejandro',
            'email' => 'alejandro@example.com',
            'phone' => '992285134',
            'rut' => '11.455.798-6',
            'fields' => [
                'name' => 'Alejandro',
                'lastname' => 'Reveco',
                'project_name' => 'Edificio Indigo',
                'comuna' => 'Puerto Varas',
                'arrival_channel' => 'BlackInmobiliario',
                'medio' => 'meta',
                'rango' => 'Entre $2.500.000 y $3.500.000',
                'codeudor' => 'Si',
                'validacion_renta' => 'Aprobada',
                'buscas' => 'Inversión',
                'elaboral' => 'Dependiente',
                'comuna_inversion' => 'Ñuñoa',
                'utm_source' => 'direct',
                'utm_medium' => 'organic',
                'utm_campaign' => 'BlackFriday',
                'utm_content' => 'AON_Mood_anuncio_5',
                'utm_term' => 'clientes-potenciales',
            ],
            'submitted_at' => now(),
        ]);

        $payload = app(SalesforceCaseMapper::class)->mapLead($submission);

        $this->assertSame('Alejandro', $payload['FirstName'] ?? null);
        $this->assertSame('Reveco', $payload['LastName'] ?? null);
        $this->assertSame('iLeben', $payload['Company'] ?? null);
        $this->assertSame('992285134', $payload['Phone'] ?? null);
        $this->assertSame('992285134', $payload['MobilePhone'] ?? null);
        $this->assertSame('alejandro@example.com', $payload['Email'] ?? null);
        $this->assertSame('alejandro@example.com', $payload['Email__c'] ?? null);
        $this->assertSame('11.455.798-6', $payload['RUT__c'] ?? null);
        $this->assertSame('Meta', $payload['LeadSource'] ?? null);
        $this->assertSame('En Contacto', $payload['Status'] ?? null);
        $this->assertSame('005U100000CAG4bIAH', $payload['OwnerId'] ?? null);
        $this->assertSame('Online', $payload['Tipo_Ingreso__c'] ?? null);
        $this->assertSame('a0J8c00000sdxCXEAY', $payload['Proyecto__c'] ?? null);
        $this->assertSame('a0J8c00000sdxCXEAY', $payload['ID_Proyecto__c'] ?? null);
        $this->assertSame('Edificio Indigo', $payload['Informacion_Cotizacion__c'] ?? null);
        $this->assertSame('Edificio Indigo', $payload['Proyect_ID__c'] ?? null);
        $this->assertSame('Puerto Varas', $payload['Comuna__c'] ?? null);
        $this->assertSame('Entre $2.500.000 y $3.500.000', $payload['Rango_de_renta_liquida__c'] ?? null);
        $this->assertSame('Si', $payload['complementaRenta__c'] ?? null);
        $this->assertSame('Aprobada', $payload['Validaci_n_Renta__c'] ?? null);
        $this->assertSame('Inversión', $payload['usoDepartamento__c'] ?? null);
        $this->assertSame('Dependiente', $payload['estadoLaboral__c'] ?? null);
        $this->assertSame('Ñuñoa', $payload['comunaInversion__c'] ?? null);
        $this->assertSame('Meta', $payload['Medio_de_Llegada__c'] ?? null);
        $this->assertSame('BlackFriday', $payload['Nombre_de_la_Campa_a__c'] ?? null);
        $this->assertSame('organic', $payload['Audiencia__c'] ?? null);
        $this->assertSame('AON_Mood_anuncio_5', $payload['Pieza_Grafica__c'] ?? null);
        $this->assertSame('direct', $payload['utm_source__c'] ?? null);
        $this->assertSame('organic', $payload['utm_medium__c'] ?? null);
        $this->assertSame('BlackFriday', $payload['utm_campaign__c'] ?? null);
        $this->assertSame('AON_Mood_anuncio_5', $payload['utm_content__c'] ?? null);
        $this->assertSame('clientes-potenciales', $payload['utm_term__c'] ?? null);
        $this->assertStringContainsString('Nombre: Alejandro', $payload['Description'] ?? '');
        $this->assertStringContainsString('Proyecto: Edificio Indigo', $payload['Description'] ?? '');
        $this->assertStringContainsString('UTM Source: direct', $payload['Description'] ?? '');
    }
}
