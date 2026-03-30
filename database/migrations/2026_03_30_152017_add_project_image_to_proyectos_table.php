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
            // Si la columna ya existe, no hacer nada
            if (! Schema::hasColumn('proyectos', 'project_image_id')) {
                $table->unsignedBigInteger('project_image_id')
                    ->nullable()
                    ->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            if (Schema::hasColumn('proyectos', 'project_image_id')) {
                $table->dropColumn('project_image_id');
            }
        });
    }
};
