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
        Schema::create('broker_benefits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('broker_category_id');
            $table->string('section');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['included', 'not_applicable'])->default('included');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['broker_category_id', 'section', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broker_benefits');
    }
};
