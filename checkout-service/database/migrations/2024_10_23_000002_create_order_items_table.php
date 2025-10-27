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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            
            // Product information (snapshot at time of order)
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');
            $table->text('product_description')->nullable();
            $table->decimal('product_price', 10, 2);
            $table->string('product_image_url')->nullable();
            
            // Order details
            $table->integer('quantity');
            $table->decimal('subtotal', 10, 2);
            
            // Additional info
            $table->json('product_attributes')->nullable(); // For variants like size, color, etc.
            
            $table->timestamps();
            
            // Indexes
            $table->index('order_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
