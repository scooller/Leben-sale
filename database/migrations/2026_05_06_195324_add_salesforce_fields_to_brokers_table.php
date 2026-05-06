<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brokers', function (Blueprint $table) {
            $table->string('salesforce_id')->nullable()->unique()->after('id');
            $table->timestamp('salesforce_synced_at')->nullable()->after('notes');

            // Make user_id nullable so Salesforce-synced brokers don't require a user account
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('brokers', function (Blueprint $table) {
            $table->dropColumn(['salesforce_id', 'salesforce_synced_at']);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
