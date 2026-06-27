<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'usage', 'purchase', 'admin_adjustment', 'refund'
            $table->integer('amount'); // positive for add, negative for deduct
            $table->integer('balance_after');
            $table->string('description')->nullable();
            $table->json('metadata')->nullable(); // model_id, tokens, conversation_id, etc.
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
    }
};
