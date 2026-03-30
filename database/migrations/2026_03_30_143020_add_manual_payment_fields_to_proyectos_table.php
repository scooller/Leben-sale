<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->text('manual_payment_instructions')
                ->nullable()
                ->after('transbank_commerce_code');

            $table->json('manual_payment_bank_accounts')
                ->nullable()
                ->after('manual_payment_instructions');

            $table->string('manual_payment_link')
                ->nullable()
                ->after('manual_payment_bank_accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropColumn([
                'manual_payment_instructions',
                'manual_payment_bank_accounts',
                'manual_payment_link',
            ]);
        });
    }
};
