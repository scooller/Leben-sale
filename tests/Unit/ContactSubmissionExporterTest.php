<?php

namespace Tests\Unit;

use App\Filament\Exports\ContactSubmissionExporter;
use App\Models\ContactSubmission;
use App\Models\SiteSetting;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactSubmissionExporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_exports_dynamic_fields_and_project_location_aliases(): void
    {
        SiteSetting::current()->update([
            'contact_form_fields' => [
                ['key' => 'name', 'label' => 'Nombre completo', 'type' => 'text', 'required' => true],
                ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                [
                    'key' => 'reason',
                    'label' => 'Motivo',
                    'type' => 'select',
                    'required' => false,
                    'options' => [
                        ['value' => 'cotizacion', 'label' => 'Cotizacion'],
                        ['value' => 'visita', 'label' => 'Agendar visita'],
                    ],
                ],
                ['key' => 'budget', 'label' => 'Presupuesto', 'type' => 'text', 'required' => false],
                ['key' => 'mensaje', 'label' => 'Mensaje', 'type' => 'textarea', 'required' => false],
                ['key' => 'custom_extra', 'label' => 'Custom Extra', 'type' => 'text', 'required' => false],
            ],
        ]);

        $submission = ContactSubmission::query()->create([
            'name' => 'Juan Perez',
            'email' => 'juan@example.com',
            'phone' => '+56 9 1234 5678',
            'rut' => '12.345.678-5',
            'fields' => [
                'reason' => 'cotizacion',
                'budget' => 'UF 4.500',
                'commune' => 'Providencia',
                'project_name' => 'Parque Central',
                'mensaje' => 'Necesito mas informacion.',
                'custom_extra' => 'valor historico',
            ],
            'recipient_email' => 'leads@ileben.cl',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'submitted_at' => now(),
            'salesforce_case_id' => '00QU1000002abcDIAQ',
            'salesforce_case_error' => 'Error de validacion en Salesforce',
        ]);

        $columns = ContactSubmissionExporter::getColumns();
        $columnNames = array_map(fn ($column): string => $column->getName(), $columns);

        $this->assertContains('field_reason', $columnNames);
        $this->assertContains('field_budget', $columnNames);
        $this->assertContains('field_mensaje', $columnNames);
        $this->assertContains('field_custom_extra', $columnNames);
        $this->assertContains('contact_comuna', $columnNames);
        $this->assertContains('contact_proyecto', $columnNames);
        $this->assertNotContains('dynamic_fields_json', $columnNames);
        $this->assertContains('salesforce_case_id', $columnNames);
        $this->assertContains('salesforce_case_error', $columnNames);

        $exporter = new ContactSubmissionExporter(
            new Export(['exporter' => ContactSubmissionExporter::class]),
            array_fill_keys($columnNames, true),
            [],
        );

        $row = $exporter($submission);

        $this->assertIsArray($row);

        $exportedData = array_combine($columnNames, $row);

        $this->assertIsArray($exportedData);
        $this->assertSame('Cotizacion', $exportedData['field_reason']);
        $this->assertSame('UF 4.500', $exportedData['field_budget']);
        $this->assertSame('Necesito mas informacion.', $exportedData['field_mensaje']);
        $this->assertSame('valor historico', $exportedData['field_custom_extra']);
        $this->assertSame('Providencia', $exportedData['contact_comuna']);
        $this->assertSame('Parque Central', $exportedData['contact_proyecto']);
        $this->assertSame('00QU1000002abcDIAQ', $exportedData['salesforce_case_id']);
        $this->assertSame('Error de validacion en Salesforce', $exportedData['salesforce_case_error']);
    }

    public function test_it_always_includes_standard_utm_fields_in_exports(): void
    {
        SiteSetting::current()->update([
            'contact_form_fields' => [
                ['key' => 'name', 'label' => 'Nombre completo', 'type' => 'text', 'required' => true],
                ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
            ],
        ]);

        $submission = ContactSubmission::query()->create([
            'name' => 'Sole Nunez',
            'email' => 'sole@example.com',
            'phone' => '+56 9 9915 5186',
            'rut' => '8687032-0',
            'fields' => [
                'utm_source' => 'direct',
                'utm_medium' => 'organic',
                'utm_campaign' => 'auto-tagging',
                'utm_term' => 'none',
                'utm_content' => 'none',
                'utm_site' => 'ileben.cl',
            ],
            'recipient_email' => 'leads@ileben.cl',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'submitted_at' => now(),
        ]);

        $columns = ContactSubmissionExporter::getColumns();
        $columnNames = array_map(fn ($column): string => $column->getName(), $columns);

        $this->assertContains('field_utm_source', $columnNames);
        $this->assertContains('field_utm_medium', $columnNames);
        $this->assertContains('field_utm_campaign', $columnNames);
        $this->assertContains('field_utm_term', $columnNames);
        $this->assertContains('field_utm_content', $columnNames);
        $this->assertContains('field_utm_site', $columnNames);

        $exporter = new ContactSubmissionExporter(
            new Export(['exporter' => ContactSubmissionExporter::class]),
            array_fill_keys($columnNames, true),
            [],
        );

        $row = $exporter($submission);

        $this->assertIsArray($row);

        $exportedData = array_combine($columnNames, $row);

        $this->assertIsArray($exportedData);
        $this->assertSame('direct', $exportedData['field_utm_source']);
        $this->assertSame('organic', $exportedData['field_utm_medium']);
        $this->assertSame('auto-tagging', $exportedData['field_utm_campaign']);
        $this->assertSame('none', $exportedData['field_utm_term']);
        $this->assertSame('none', $exportedData['field_utm_content']);
        $this->assertSame('ileben.cl', $exportedData['field_utm_site']);
    }
}
