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
            if (! Schema::hasColumn('site_settings', 'contact_page_title')) {
                $table->string('contact_page_title')->nullable()->after('contact_address');
            }

            if (! Schema::hasColumn('site_settings', 'contact_page_subtitle')) {
                $table->text('contact_page_subtitle')->nullable()->after('contact_page_title');
            }

            if (! Schema::hasColumn('site_settings', 'contact_page_content')) {
                $table->longText('contact_page_content')->nullable()->after('contact_page_subtitle');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (Schema::hasColumn('site_settings', 'contact_page_content')) {
                $table->dropColumn('contact_page_content');
            }

            if (Schema::hasColumn('site_settings', 'contact_page_subtitle')) {
                $table->dropColumn('contact_page_subtitle');
            }

            if (Schema::hasColumn('site_settings', 'contact_page_title')) {
                $table->dropColumn('contact_page_title');
            }
        });
    }
};
