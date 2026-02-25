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
            // Eliminar colores personalizados - Web Awesome maneja los colores mediante temas y paletas
            // Los colores semánticos (brand, success, warning, danger, neutral) están definidos por el tema/paleta
            $table->dropColumn([
                'primary_color',
                'secondary_color',
                'accent_color',
                'background_color',
                'text_color',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            // Restaurar los campos si se hace rollback
            $table->string('primary_color')->default('#667eea')->after('icon');
            $table->string('secondary_color')->default('#764ba2')->after('primary_color');
            $table->string('accent_color')->nullable()->after('secondary_color');
            $table->string('background_color')->default('#ffffff')->after('accent_color');
            $table->string('text_color')->default('#1f2937')->after('background_color');
        });
    }
};
