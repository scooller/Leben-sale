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
        Schema::create('soql_query_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->longText('soql');
            $table->string('status', 32);
            $table->unsignedInteger('records_count')->default(0);
            $table->unsignedInteger('duration_ms')->nullable();
            $table->unsignedInteger('limit_value')->nullable();
            $table->text('error_message')->nullable();
            $table->json('result_preview')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soql_query_runs');
    }
};
