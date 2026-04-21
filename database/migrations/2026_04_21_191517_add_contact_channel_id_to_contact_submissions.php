<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Idempotent: skip if column already exists (handles partial runs not recorded
        // in the migrations table, e.g. a previous failed deploy that left the column).
        if (! Schema::hasColumn('contact_submissions', 'contact_channel_id')) {
            Schema::table('contact_submissions', function (Blueprint $table) {
                $table->foreignId('contact_channel_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('contact_channels')
                    ->nullOnDelete();

                $table->index('contact_channel_id');
            });
        }

        // Back-fill existing rows to the default channel (if one exists).
        if (Schema::hasTable('contact_channels')) {
            $defaultChannelId = DB::table('contact_channels')
                ->where('is_default', true)
                ->where('is_active', true)
                ->value('id');

            if ($defaultChannelId !== null) {
                DB::table('contact_submissions')
                    ->whereNull('contact_channel_id')
                    ->update(['contact_channel_id' => $defaultChannelId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropForeign(['contact_channel_id']);
            $table->dropIndex(['contact_channel_id']);
            $table->dropColumn('contact_channel_id');
        });
    }
};
