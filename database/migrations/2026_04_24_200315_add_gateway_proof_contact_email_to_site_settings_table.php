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
        Schema::table('site_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('site_settings', 'gateway_proof_contact_email')) {
                $table->string('gateway_proof_contact_email')
                    ->nullable()
                    ->after('gateway_manual_enabled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (Schema::hasColumn('site_settings', 'gateway_proof_contact_email')) {
                $table->dropColumn('gateway_proof_contact_email');
            }
        });
    }
};
