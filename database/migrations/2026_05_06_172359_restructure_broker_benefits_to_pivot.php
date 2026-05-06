<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $databaseName = DB::getDatabaseName();
        $driver = DB::getDriverName();

        // Drop FK and columns from broker_benefits (MySQL production path)
        if ($driver === 'mysql' && Schema::hasColumn('broker_benefits', 'broker_category_id')) {
            if ($driver === 'mysql') {
                $foreignKeyExists = DB::table('information_schema.TABLE_CONSTRAINTS')
                    ->where('CONSTRAINT_SCHEMA', $databaseName)
                    ->where('TABLE_NAME', 'broker_benefits')
                    ->where('CONSTRAINT_NAME', 'broker_benefits_broker_category_id_foreign')
                    ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
                    ->exists();

                if ($foreignKeyExists) {
                    DB::statement('ALTER TABLE broker_benefits DROP FOREIGN KEY broker_benefits_broker_category_id_foreign');
                }
            }

            Schema::table('broker_benefits', function (Blueprint $table) {
                $table->dropColumn('broker_category_id');
            });
        }

        if ($driver === 'mysql' && Schema::hasColumn('broker_benefits', 'status')) {
            Schema::table('broker_benefits', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        // New index without broker_category_id
        $newIndexExists = false;

        if ($driver === 'mysql') {
            $newIndexExists = DB::table('information_schema.STATISTICS')
                ->where('TABLE_SCHEMA', $databaseName)
                ->where('TABLE_NAME', 'broker_benefits')
                ->where('INDEX_NAME', 'broker_benefits_section_sort_order_index')
                ->exists();
        }

        if (! $newIndexExists) {
            Schema::table('broker_benefits', function (Blueprint $table) {
                $table->index(['section', 'sort_order']);
            });
        }

        // Pivot table: benefit ↔ category with status
        if (! Schema::hasTable('broker_benefit_category')) {
            Schema::create('broker_benefit_category', function (Blueprint $table) {
                $table->id();
                $table->foreignId('broker_benefit_id')->constrained('broker_benefits')->cascadeOnDelete();
                $table->foreignId('broker_category_id')->constrained('broker_categories')->cascadeOnDelete();
                $table->enum('status', ['included', 'not_applicable'])->default('included');
                $table->timestamps();

                $table->unique(['broker_benefit_id', 'broker_category_id'], 'bbc_benefit_category_unique');
            });
        }
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
