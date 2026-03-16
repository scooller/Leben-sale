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
        Schema::table('plants', function (Blueprint $table) {
            $table->unsignedBigInteger('cover_image_id')->nullable()->after('superficie_vendible');
            $table->unsignedBigInteger('interior_image_id')->nullable()->after('cover_image_id');

            $table->foreign('cover_image_id')->references('id')->on('curator')->nullOnDelete();
            $table->foreign('interior_image_id')->references('id')->on('curator')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->dropForeign(['cover_image_id']);
            $table->dropForeign(['interior_image_id']);
            $table->dropColumn(['cover_image_id', 'interior_image_id']);
        });
    }
};
