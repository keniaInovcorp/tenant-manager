<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create subscription audit logs table.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscription_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('old_plan_id')->nullable()->constrained('plans')->onDelete('set null');
            $table->foreignId('new_plan_id')->nullable()->constrained('plans')->onDelete('set null');
            $table->string('action');
            $table->decimal('old_price', 10, 2)->nullable();
            $table->decimal('new_price', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_logs');
    }
};
