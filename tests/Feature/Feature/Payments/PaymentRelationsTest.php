<?php

namespace Tests\Feature\Feature\Payments;

use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Plant;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PaymentRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_payments_table_has_plant_id_column(): void
    {
        $this->assertTrue(Schema::hasColumn('payments', 'plant_id'));
    }

    public function test_payment_belongs_to_selected_project_and_plant(): void
    {
        $user = User::factory()->create();
        $project = Proyecto::factory()->create();
        $plant = Plant::factory()->create([
            'salesforce_proyecto_id' => $project->salesforce_id,
            'is_active' => true,
        ]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'plant_id' => $plant->id,
            'gateway' => PaymentGateway::TRANSBANK,
            'amount' => 100000,
            'currency' => 'CLP',
            'status' => PaymentStatus::PENDING,
        ]);

        $this->assertSame($project->id, $payment->project?->id);
        $this->assertSame($plant->id, $payment->plant?->id);
    }
}
