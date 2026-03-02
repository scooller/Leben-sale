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
        Schema::create('plant_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_token', 64)->unique();
            $table->string('status')->default('active');
            $table->timestamp('expires_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->string('released_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['plant_id', 'status']);
            $table->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_reservations');
    }
};
