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
        Schema::create('broker_alliances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broker_id')->constrained('brokers')->cascadeOnDelete();
            $table->foreignId('image_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('name');
            $table->string('url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['broker_id', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broker_alliances');
    }
};
