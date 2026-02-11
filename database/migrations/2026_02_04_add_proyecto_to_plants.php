<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Column salesforce_proyecto_id is now added in migrate_plants_to_product2
        // This migration is kept for backwards compatibility but does nothing
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to reverse as nothing was changed
    }
};
