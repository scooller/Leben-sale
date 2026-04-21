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
        Schema::create('short_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('slug', 32)->unique();
            $table->string('title')->nullable();
            $table->text('destination_url');
            $table->string('status', 32)->default('active');
            $table->string('tag_manager_id', 50)->nullable();
            $table->unsignedBigInteger('visits_count')->default(0);
            $table->timestamp('last_visited_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_by']);
            $table->index('expires_at');
            $table->index('last_visited_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_links');
    }
};
