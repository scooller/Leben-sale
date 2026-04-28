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
        if (! Schema::hasColumn('contact_submissions', 'salesforce_synced_at')) {
            Schema::table('contact_submissions', function (Blueprint $table) {
                $table->timestamp('salesforce_synced_at')->nullable()->after('salesforce_case_error');
            });
        }

        if (! Schema::hasColumn('contact_submissions', 'salesforce_sync_trigger')) {
            Schema::table('contact_submissions', function (Blueprint $table) {
                $table->string('salesforce_sync_trigger', 20)->nullable()->after('salesforce_synced_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('contact_submissions', 'salesforce_sync_trigger')) {
                $columns[] = 'salesforce_sync_trigger';
            }

            if (Schema::hasColumn('contact_submissions', 'salesforce_synced_at')) {
                $columns[] = 'salesforce_synced_at';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
