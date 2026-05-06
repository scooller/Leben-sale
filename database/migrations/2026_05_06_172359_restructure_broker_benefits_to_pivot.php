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
        // Drop FK and columns from broker_benefits
        Schema::table('broker_benefits', function (Blueprint $table) {
            $table->dropForeign(['broker_category_id']);
            $table->dropIndex(['broker_category_id', 'section', 'sort_order']);
            $table->dropColumn(['broker_category_id', 'status']);
        });

        // New index without broker_category_id
        Schema::table('broker_benefits', function (Blueprint $table) {
            $table->index(['section', 'sort_order']);
        });

        // Pivot table: benefit ↔ category with status
        Schema::create('broker_benefit_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broker_benefit_id')->constrained('broker_benefits')->cascadeOnDelete();
            $table->foreignId('broker_category_id')->constrained('broker_categories')->cascadeOnDelete();
            $table->enum('status', ['included', 'not_applicable'])->default('included');
            $table->timestamps();

            $table->unique(['broker_benefit_id', 'broker_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broker_benefit_category');

        Schema::table('broker_benefits', function (Blueprint $table) {
            $table->dropIndex(['section', 'sort_order']);
            $table->unsignedBigInteger('broker_category_id')->after('id');
            $table->enum('status', ['included', 'not_applicable'])->default('included')->after('description');
            $table->foreign('broker_category_id')->references('id')->on('broker_categories')->cascadeOnDelete();
            $table->index(['broker_category_id', 'section', 'sort_order']);
        });
    }
};
