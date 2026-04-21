<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_channels', function (Blueprint $table) {
            if (! Schema::hasColumn('contact_channels', 'slug_badge_color')) {
                $table->string('slug_badge_color', 32)
                    ->default('gray')
                    ->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contact_channels', function (Blueprint $table) {
            if (Schema::hasColumn('contact_channels', 'slug_badge_color')) {
                $table->dropColumn('slug_badge_color');
            }
        });
    }
};
