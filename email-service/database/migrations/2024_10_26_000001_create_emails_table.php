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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            
            // Email details
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->string('subject');
            $table->text('body_html')->nullable();
            $table->text('body_text')->nullable();
            
            // Email type and reference
            $table->enum('type', [
                'order_confirmation',
                'order_shipped',
                'order_delivered',
                'order_cancelled',
                'password_reset',
                'welcome',
                'general'
            ])->default('general');
            
            $table->string('reference_type')->nullable(); // e.g., 'order', 'user'
            $table->unsignedBigInteger('reference_id')->nullable(); // e.g., order_id
            
            // Status tracking
            $table->enum('status', [
                'pending',
                'sending',
                'sent',
                'failed',
                'bounced'
            ])->default('pending');
            
            // Timestamps
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            
            // Error tracking
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('next_retry_at')->nullable();
            
            // Additional data
            $table->json('metadata')->nullable(); // Store order details, etc.
            $table->json('email_provider_response')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('recipient_email');
            $table->index('status');
            $table->index('type');
            $table->index(['reference_type', 'reference_id']);
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
