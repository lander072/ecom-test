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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            
            // Template identification
            $table->string('name')->unique(); // e.g., 'order_confirmation'
            $table->string('display_name'); // e.g., 'Order Confirmation Email'
            $table->text('description')->nullable();
            
            // Template content
            $table->string('subject');
            $table->text('body_html');
            $table->text('body_text')->nullable();
            
            // Template variables
            $table->json('available_variables')->nullable(); // ['order_number', 'customer_name', etc.]
            
            // Template settings
            $table->boolean('is_active')->default(true);
            $table->string('category')->default('general'); // order, user, system, etc.
            
            // Version control
            $table->integer('version')->default(1);
            
            $table->timestamps();
            
            // Indexes
            $table->index('category');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
