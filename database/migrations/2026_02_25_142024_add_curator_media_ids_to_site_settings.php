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
            $table->unsignedBigInteger('logo_id')->nullable()->after('logo');
            $table->unsignedBigInteger('logo_dark_id')->nullable()->after('logo_dark');
            $table->unsignedBigInteger('icon_id')->nullable()->after('icon');
            $table->unsignedBigInteger('favicon_id')->nullable()->after('favicon');
            $table->unsignedBigInteger('banner_image_id')->nullable()->after('banner_image');

            // Add foreign key constraints
            $table->foreign('logo_id')->references('id')->on('curator')->onDelete('set null');
            $table->foreign('logo_dark_id')->references('id')->on('curator')->onDelete('set null');
            $table->foreign('icon_id')->references('id')->on('curator')->onDelete('set null');
            $table->foreign('favicon_id')->references('id')->on('curator')->onDelete('set null');
            $table->foreign('banner_image_id')->references('id')->on('curator')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropForeign(['logo_id']);
            $table->dropForeign(['logo_dark_id']);
            $table->dropForeign(['icon_id']);
            $table->dropForeign(['favicon_id']);
            $table->dropForeign(['banner_image_id']);

            $table->dropColumn(['logo_id', 'logo_dark_id', 'icon_id', 'favicon_id', 'banner_image_id']);
        });
    }
};
