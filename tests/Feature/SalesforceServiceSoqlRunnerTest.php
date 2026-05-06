<?php

namespace Tests\Feature;

use App\Services\Salesforce\SalesforceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Omniphx\Forrest\Providers\Laravel\Facades\Forrest;
use RuntimeException;
use Tests\TestCase;

class SalesforceServiceSoqlRunnerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_select_and_limit_in_manual_soql_execution(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('debe incluir LIMIT');

        app(SalesforceService::class)->runSoqlWithoutCache('SELECT Id FROM Lead');
    }

    public function test_it_runs_manual_soql_without_cache_on_every_execution(): void
    {
        Forrest::shouldReceive('query')
            ->twice()
            ->with('SELECT Id FROM Lead LIMIT 1')
            ->andReturn([
                'records' => [['Id' => '00Q000000000001AAA']],
                'totalSize' => 1,
                'done' => true,
            ]);

        $firstResponse = app(SalesforceService::class)->runSoqlWithoutCache('SELECT Id FROM Lead LIMIT 1');
        $secondResponse = app(SalesforceService::class)->runSoqlWithoutCache('SELECT Id FROM Lead LIMIT 1');

        $this->assertSame(1, $firstResponse['total_size']);
        $this->assertSame(1, $secondResponse['total_size']);
        $this->assertSame(1, $firstResponse['limit']);
        $this->assertSame(1, $secondResponse['limit']);
    }

    public function test_it_reauthenticates_when_manual_soql_execution_fails_initially(): void
    {
        Forrest::shouldReceive('query')
            ->once()
            ->with('SELECT Id FROM Lead LIMIT 1')
            ->andThrow(new RuntimeException('token expired'));

        Forrest::shouldReceive('authenticate')->once();

        Forrest::shouldReceive('query')
            ->once()
            ->with('SELECT Id FROM Lead LIMIT 1')
            ->andReturn([
                'records' => [['Id' => '00Q000000000001AAA']],
                'totalSize' => 1,
                'done' => true,
            ]);

        $response = app(SalesforceService::class)->runSoqlWithoutCache('SELECT Id FROM Lead LIMIT 1');

        $this->assertSame(1, $response['total_size']);
        $this->assertCount(1, $response['records']);
    }
}
