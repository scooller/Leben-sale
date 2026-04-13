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
        if (! Schema::hasColumn('site_settings', 'evento_sale')) {
            Schema::table('site_settings', function (Blueprint $table) {
                $table->boolean('evento_sale')->default(false)->after('site_url');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('site_settings', 'evento_sale')) {
            Schema::table('site_settings', function (Blueprint $table) {
                $table->dropColumn('evento_sale');
            });
        }
    }
};
