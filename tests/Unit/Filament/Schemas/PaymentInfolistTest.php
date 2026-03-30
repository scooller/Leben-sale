<?php

namespace Tests\Unit\Filament\Schemas;

use App\Filament\Resources\Payments\Schemas\PaymentInfolist;
use Tests\TestCase;

class PaymentInfolistTest extends TestCase
{
    public function test_it_normalizes_nested_metadata_values_for_safe_display(): void
    {
        $metadata = [
            'manual_payment_reference' => 'MAN-123',
            'manual_payment_requires_proof' => true,
            'manual_payment_bank_accounts' => [
                [
                    'bank' => 'Banco Demo',
                    'account_number' => '123456',
                ],
            ],
            'manual_payment_expires_at' => null,
        ];

        $normalized = PaymentInfolist::normalizeMetadataForDisplay($metadata);

        $this->assertSame('MAN-123', $normalized['manual_payment_reference']);
        $this->assertSame('true', $normalized['manual_payment_requires_proof']);
        $this->assertSame('[{"bank":"Banco Demo","account_number":"123456"}]', $normalized['manual_payment_bank_accounts']);
        $this->assertSame('', $normalized['manual_payment_expires_at']);
    }

    public function test_it_returns_empty_array_when_metadata_is_null(): void
    {
        $normalized = PaymentInfolist::normalizeMetadataForDisplay(null);

        $this->assertSame([], $normalized);
    }
}
