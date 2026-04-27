<?php

namespace Tests\Feature;

use App\Services\Salesforce\SalesforceService;
use Mockery;
use Tests\TestCase;

class SalesforceServiceFindSalesforceUserByEmailTest extends TestCase
{
    public function test_it_returns_mapped_salesforce_user_when_email_exists(): void
    {
        $service = Mockery::mock(SalesforceService::class)->makePartial();

        $service->shouldReceive('query')
            ->once()
            ->withArgs(function (string $soql): bool {
                return str_contains($soql, 'FROM User')
                    && str_contains($soql, "WHERE Email = 'advisor@example.com'")
                    && str_contains($soql, 'LIMIT 1');
            })
            ->andReturn([
                [
                    'Id' => '005XX0000001AAA',
                    'FirstName' => 'Ana',
                    'LastName' => 'Perez',
                    'Email' => 'advisor@example.com',
                    'Whatsapp_owner__c' => '+56911111111',
                    'MediumPhotoUrl' => 'https://example.com/avatar.jpg',
                    'IsActive' => true,
                ],
            ]);

        $user = $service->findSalesforceUserByEmail('advisor@example.com');

        $this->assertNotNull($user);
        $this->assertSame('005XX0000001AAA', $user['id']);
        $this->assertSame('Ana', $user['first_name']);
        $this->assertSame('Perez', $user['last_name']);
        $this->assertSame('advisor@example.com', $user['email']);
        $this->assertSame('+56911111111', $user['whatsapp_owner']);
        $this->assertSame('https://example.com/avatar.jpg', $user['avatar_url']);
        $this->assertTrue($user['is_active']);
    }

    public function test_it_returns_null_when_email_is_not_found(): void
    {
        $service = Mockery::mock(SalesforceService::class)->makePartial();

        $service->shouldReceive('query')
            ->once()
            ->andReturn([]);

        $this->assertNull($service->findSalesforceUserByEmail('missing@example.com'));
    }

    public function test_it_returns_null_without_query_when_email_is_blank(): void
    {
        $service = Mockery::mock(SalesforceService::class)->makePartial();

        $service->shouldReceive('query')->never();

        $this->assertNull($service->findSalesforceUserByEmail('   '));
    }
}
