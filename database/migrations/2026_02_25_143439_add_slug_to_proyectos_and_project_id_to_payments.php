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
        // Agregar slug a proyectos (unique por integridad referencial)
        Schema::table('proyectos', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        // Agregar project_id a payments para soporte de Mall (múltiples códigos)
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('project_id')
                ->nullable()
                ->after('user_id')
                ->constrained('proyectos')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
        });

        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
