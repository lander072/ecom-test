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
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            
            // Transaction details
            $table->string('transaction_id')->unique(); // External payment gateway transaction ID
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            
            // Payment method
            $table->enum('payment_method', [
                'credit_card',
                'debit_card',
                'paypal',
                'stripe',
                'bank_transfer',
                'cash_on_delivery',
                'other'
            ]);
            
            // Status
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'cancelled',
                'refunded'
            ])->default('pending');
            
            // Payment gateway info
            $table->string('payment_gateway')->nullable(); // e.g., 'stripe', 'paypal'
            $table->json('payment_gateway_response')->nullable(); // Store full response
            
            // Card info (last 4 digits only for security)
            $table->string('card_last_four')->nullable();
            $table->string('card_brand')->nullable(); // visa, mastercard, etc.
            
            // Additional info
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('failure_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('transaction_id');
            $table->index('order_id');
            $table->index('status');
            $table->index('payment_method');
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
