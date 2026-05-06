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
        Schema::create('broker_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broker_id')->constrained('brokers')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['broker_id', 'year', 'month']);
            $table->index(['is_published', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broker_galleries');
    }
};
