<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_email',
        'recipient_name',
        'subject',
        'body_html',
        'body_text',
        'type',
        'reference_type',
        'reference_id',
        'status',
        'sent_at',
        'failed_at',
        'bounced_at',
        'error_message',
        'retry_count',
        'next_retry_at',
        'metadata',
        'email_provider_response',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'bounced_at' => 'datetime',
        'next_retry_at' => 'datetime',
        'metadata' => 'array',
        'email_provider_response' => 'array',
        'retry_count' => 'integer',
    ];

    // Query scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSending($query)
    {
        return $query->where('status', 'sending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeBounced($query)
    {
        return $query->where('status', 'bounced');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForReference($query, string $referenceType, int $referenceId)
    {
        return $query->where('reference_type', $referenceType)
                     ->where('reference_id', $referenceId);
    }

    // Status check methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSending(): bool
    {
        return $this->status === 'sending';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isBounced(): bool
    {
        return $this->status === 'bounced';
    }

    // Status update methods
    public function markAsSending(): void
    {
        $this->update([
            'status' => 'sending',
        ]);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'error_message' => null,
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
            'next_retry_at' => now()->addMinutes($this->calculateRetryDelay()),
        ]);
    }

    public function markAsBounced(string $reason): void
    {
        $this->update([
            'status' => 'bounced',
            'bounced_at' => now(),
            'error_message' => $reason,
        ]);
    }

    // Helper methods
    private function calculateRetryDelay(): int
    {
        // Exponential backoff: 5, 15, 45, 135 minutes, etc.
        return 5 * pow(3, $this->retry_count);
    }

    public function canRetry(): bool
    {
        return $this->isFailed() && 
               $this->retry_count < 5 &&
               ($this->next_retry_at === null || $this->next_retry_at->isPast());
    }
}
