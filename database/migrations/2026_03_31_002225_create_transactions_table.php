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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')
              ->cascadeOnDelete();
            // ربط اختياري بالطلب

               $table->foreignId('order_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['recharge', 'purchase'])->index();
             $table->enum('status', ['pending', 'completed', 'failed'])
        ->default('completed');
          $table->string('reference')->nullable(); // رقم العملية
            $table->text('description')->nullable();




            $table->timestamps();
              $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
