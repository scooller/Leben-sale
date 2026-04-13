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
            if (! Schema::hasColumn('site_settings', 'contact_form_fields')) {
                $table->json('contact_form_fields')->nullable()->after('contact_page_content');
            }

            if (! Schema::hasColumn('site_settings', 'contact_notification_email')) {
                $table->string('contact_notification_email')->nullable()->after('contact_form_fields');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (Schema::hasColumn('site_settings', 'contact_notification_email')) {
                $table->dropColumn('contact_notification_email');
            }

            if (Schema::hasColumn('site_settings', 'contact_form_fields')) {
                $table->dropColumn('contact_form_fields');
            }
        });
    }
};
