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
        if (! Schema::hasColumn('site_settings', 'brand_color')) {
            Schema::table('site_settings', function (Blueprint $table) {
                $table->string('brand_color', 20)->default('#eb0029')->after('webawesome_palette');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('site_settings', 'brand_color')) {
            Schema::table('site_settings', function (Blueprint $table) {
                $table->dropColumn('brand_color');
            });
        }
    }
};
