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
            if (! Schema::hasColumn('site_settings', 'footer_menu')) {
                $table->json('footer_menu')->nullable()->after('site_url');
            }

            if (! Schema::hasColumn('site_settings', 'footer_legal_text')) {
                $table->longText('footer_legal_text')->nullable()->after('footer_menu');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (Schema::hasColumn('site_settings', 'footer_legal_text')) {
                $table->dropColumn('footer_legal_text');
            }

            if (Schema::hasColumn('site_settings', 'footer_menu')) {
                $table->dropColumn('footer_menu');
            }
        });
    }
};
