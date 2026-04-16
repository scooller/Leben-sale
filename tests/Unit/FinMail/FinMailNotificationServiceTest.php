<?php

namespace Tests\Unit\FinMail;

use App\Services\FinMail\FinMailNotificationService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class FinMailNotificationServiceTest extends TestCase
{
    public function test_it_extracts_commune_aliases_for_contact_fields(): void
    {
        $service = new FinMailNotificationService;
        $method = new ReflectionMethod(FinMailNotificationService::class, 'extractFieldValue');
        $method->setAccessible(true);

        $value = $method->invoke($service, [
            'commune' => 'Providencia',
        ], ['comuna', 'commune', 'district', 'project_commune']);

        $this->assertSame('Providencia', $value);
    }

    public function test_it_extracts_project_aliases_for_contact_fields(): void
    {
        $service = new FinMailNotificationService;
        $method = new ReflectionMethod(FinMailNotificationService::class, 'extractFieldValue');
        $method->setAccessible(true);

        $value = $method->invoke($service, [
            'project_name' => 'Edificio Andes',
        ], ['proyecto', 'project', 'project_name', 'nombre_proyecto']);

        $this->assertSame('Edificio Andes', $value);
    }
}
