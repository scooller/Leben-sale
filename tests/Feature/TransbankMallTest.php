<?php

namespace Tests\Feature;

use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Proyecto;
use App\Models\User;
use Tests\TestCase;

class TransbankMallTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test 1: Proyecto slug auto-generation
     */
    public function test_proyecto_slug_auto_generation(): void
    {
        $proyecto = Proyecto::factory()->create(['name' => 'Test Project My Unique '.uniqid()]);

        $this->assertStringContainsString('test-project', $proyecto->slug);
        $this->assertNotEmpty($proyecto->slug);
    }

    /**
     * Test 2: Payment project relationship
     */
    public function test_payment_project_relationship(): void
    {
        $proyecto = Proyecto::factory()->create(['name' => 'Payment Test '.uniqid()]);
        $payment = Payment::create([
            'user_id' => $this->user->id,
            'project_id' => $proyecto->id,
            'gateway' => PaymentGateway::TRANSBANK,
            'amount' => 50000,
            'currency' => 'CLP',
            'status' => PaymentStatus::PENDING,
        ]);

        $this->assertEquals($proyecto->id, $payment->project->id);
        $this->assertEquals($proyecto->name, $payment->project->name);
    }

    /**
     * Test 3: TransbankService instantiation with mall mode
     */
    public function test_transbank_service_mall_mode(): void
    {
        $config = config('payments');

        $this->assertIsArray($config);
        $this->assertArrayHasKey('gateways', $config);

        // Get the Transbank specific config
        $transbankConfig = $config['gateways']['transbank'];

        $this->assertArrayHasKey('mall_mode', $transbankConfig);
        $this->assertArrayHasKey('commerce_codes', $transbankConfig);
        $this->assertIsArray($transbankConfig['commerce_codes']);

        // Service should instantiate without errors
        $service = new \App\Services\Payment\TransbankService($transbankConfig);
        $this->assertIsObject($service);
    }

    /**
     * Test 4: Project transbank_commerce_code resolution
     */
    public function test_proyecto_transbank_commerce_code_resolution(): void
    {
        $proyecto = Proyecto::factory()->create(['name' => 'Commerce Test '.uniqid()]);

        // Without codes in config, should return null
        $this->assertNull($proyecto->transbank_commerce_code);
    }

    /**
     * Test 5: Proyecto relationships
     */
    public function test_proyecto_has_payments_relationship(): void
    {
        $proyecto = Proyecto::factory()->create(['name' => 'Payments Test '.uniqid()]);
        $payment = Payment::create([
            'user_id' => $this->user->id,
            'project_id' => $proyecto->id,
            'gateway' => PaymentGateway::TRANSBANK,
            'amount' => 50000,
            'currency' => 'CLP',
            'status' => PaymentStatus::PENDING,
        ]);

        $this->assertTrue($proyecto->payments->contains($payment));
    }
}
