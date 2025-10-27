<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_description',
        'product_price',
        'product_image_url',
        'quantity',
        'subtotal',
        'product_attributes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
        'product_attributes' => 'array',
    ];

    /**
     * Get the order that owns the order item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Calculate subtotal based on quantity and price.
     */
    public function calculateSubtotal(): float
    {
        return $this->quantity * $this->product_price;
    }

    /**
     * Boot method to automatically calculate subtotal.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderItem) {
            if (!$orderItem->subtotal) {
                $orderItem->subtotal = $orderItem->calculateSubtotal();
            }
        });

        static::updating(function ($orderItem) {
            if ($orderItem->isDirty(['quantity', 'product_price'])) {
                $orderItem->subtotal = $orderItem->calculateSubtotal();
            }
        });
    }
}
