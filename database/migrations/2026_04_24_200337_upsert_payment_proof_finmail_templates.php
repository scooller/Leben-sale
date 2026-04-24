<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('email_templates')) {
            return;
        }

        $now = Carbon::now();

        $templates = [
            [
                'key' => 'manual-payment-proof-submitted-admin',
                'name' => [
                    'es' => 'Comprobante manual recibido (admin)',
                    'en' => 'Manual proof received (admin)',
                ],
                'subject' => [
                    'es' => 'Pago pendiente de aprobacion: {{ payment.gateway_tx_id | "-" }}',
                    'en' => 'Payment pending approval: {{ payment.gateway_tx_id | "-" }}',
                ],
                'preheader' => [
                    'es' => 'Se recibio un comprobante manual y requiere revision.',
                    'en' => 'A manual payment proof was received and requires review.',
                ],
                'body' => [
                    'es' => '<p>Se recibio un comprobante para un pago manual.</p><p><strong>Referencia unica:</strong> {{ payment.gateway_tx_id | "-" }}</p><p><strong>Monto:</strong> {{ payment.amount | "-" }} {{ payment.currency | "CLP" }}</p><p><strong>Cliente:</strong> {{ user.name | "-" }} ({{ user.email | "-" }})</p><p><strong>Unidad:</strong> {{ plant.name | "-" }}</p><p><strong>Proyecto:</strong> {{ project.name | "-" }}</p><p><strong>Revisar comprobante:</strong> {{ payment_review_url | "-" }}</p>',
                    'en' => '<p>A payment proof was received for a manual payment.</p><p><strong>Unique reference:</strong> {{ payment.gateway_tx_id | "-" }}</p><p><strong>Amount:</strong> {{ payment.amount | "-" }} {{ payment.currency | "CLP" }}</p><p><strong>Customer:</strong> {{ user.name | "-" }} ({{ user.email | "-" }})</p><p><strong>Unit:</strong> {{ plant.name | "-" }}</p><p><strong>Project:</strong> {{ project.name | "-" }}</p><p><strong>Review proof:</strong> {{ payment_review_url | "-" }}</p>',
                ],
                'category' => 'transactional',
                'tags' => ['manual', 'payment', 'admin'],
                'token_schema' => [
                    'user' => ['name', 'email'],
                    'plant' => ['name'],
                    'project' => ['name'],
                    'payment' => ['gateway_tx_id', 'amount', 'currency', 'status'],
                    'payment_review_url' => 'string',
                ],
            ],
            [
                'key' => 'payment-proof-submitted-admin',
                'name' => [
                    'es' => 'Comprobante de pago recibido (admin)',
                    'en' => 'Payment proof received (admin)',
                ],
                'subject' => [
                    'es' => 'Comprobante recibido: {{ payment.gateway_tx_id | "-" }}',
                    'en' => 'Proof received: {{ payment.gateway_tx_id | "-" }}',
                ],
                'preheader' => [
                    'es' => 'Se recibio un comprobante de pago y requiere revision.',
                    'en' => 'A payment proof was received and requires review.',
                ],
                'body' => [
                    'es' => '<p>Se recibio un comprobante para un pago.</p><p><strong>Referencia unica:</strong> {{ payment.gateway_tx_id | "-" }}</p><p><strong>Monto:</strong> {{ payment.amount | "-" }} {{ payment.currency | "CLP" }}</p><p><strong>Cliente:</strong> {{ user.name | "-" }} ({{ user.email | "-" }})</p><p><strong>Unidad:</strong> {{ plant.name | "-" }}</p><p><strong>Proyecto:</strong> {{ project.name | "-" }}</p><p><strong>Revisar comprobante:</strong> {{ payment_review_url | "-" }}</p>',
                    'en' => '<p>A payment proof was received.</p><p><strong>Unique reference:</strong> {{ payment.gateway_tx_id | "-" }}</p><p><strong>Amount:</strong> {{ payment.amount | "-" }} {{ payment.currency | "CLP" }}</p><p><strong>Customer:</strong> {{ user.name | "-" }} ({{ user.email | "-" }})</p><p><strong>Unit:</strong> {{ plant.name | "-" }}</p><p><strong>Project:</strong> {{ project.name | "-" }}</p><p><strong>Review proof:</strong> {{ payment_review_url | "-" }}</p>',
                ],
                'category' => 'transactional',
                'tags' => ['payment', 'proof', 'admin'],
                'token_schema' => [
                    'user' => ['name', 'email'],
                    'plant' => ['name'],
                    'project' => ['name'],
                    'payment' => ['gateway_tx_id', 'amount', 'currency', 'status'],
                    'payment_review_url' => 'string',
                ],
            ],
        ];

        foreach ($templates as $template) {
            DB::table('email_templates')->updateOrInsert(
                ['key' => $template['key']],
                [
                    'name' => json_encode($template['name'], JSON_UNESCAPED_UNICODE),
                    'category' => $template['category'],
                    'tags' => json_encode($template['tags'], JSON_UNESCAPED_UNICODE),
                    'subject' => json_encode($template['subject'], JSON_UNESCAPED_UNICODE),
                    'preheader' => json_encode($template['preheader'], JSON_UNESCAPED_UNICODE),
                    'body' => json_encode($template['body'], JSON_UNESCAPED_UNICODE),
                    'token_schema' => json_encode($template['token_schema'], JSON_UNESCAPED_UNICODE),
                    'is_active' => true,
                    'is_locked' => false,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('email_templates')) {
            return;
        }

        DB::table('email_templates')
            ->where('key', 'payment-proof-submitted-admin')
            ->delete();
    }
};
