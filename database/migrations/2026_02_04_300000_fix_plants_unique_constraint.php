<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Esta migración es un no-op porque la tabla plants fue recreada
        // completamente en 2026_02_04_migrate_plants_to_product2 sin el índice
        // unique que esta migración intentaba remover.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: nada que revertir
    }
};
