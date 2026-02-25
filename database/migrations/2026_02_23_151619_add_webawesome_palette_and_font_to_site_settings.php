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
            // Paleta de colores Web Awesome
            // Free: default, bright, shoelace
            // Pro: rudimentary, elegant, mild, natural, anodized, vogue
            $table->string('webawesome_palette')->default('natural')->after('webawesome_theme');

            // Tipografía (Google Fonts u otra fuente)
            $table->string('font_family_body')->nullable()->after('webawesome_palette');
            $table->string('font_family_heading')->nullable()->after('font_family_body');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['webawesome_palette', 'font_family_body', 'font_family_heading']);
        });
    }
};
