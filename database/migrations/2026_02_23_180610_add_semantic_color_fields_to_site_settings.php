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
            $table->string('semantic_brand_color', 20)->default('blue')->after('webawesome_palette');
            $table->string('semantic_neutral_color', 20)->default('gray')->after('semantic_brand_color');
            $table->string('semantic_success_color', 20)->default('green')->after('semantic_neutral_color');
            $table->string('semantic_warning_color', 20)->default('yellow')->after('semantic_success_color');
            $table->string('semantic_danger_color', 20)->default('red')->after('semantic_warning_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'semantic_brand_color',
                'semantic_neutral_color',
                'semantic_success_color',
                'semantic_warning_color',
                'semantic_danger_color',
            ]);
        });
    }
};
