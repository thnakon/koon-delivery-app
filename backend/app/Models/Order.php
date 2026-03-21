<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['id'];

    protected $casts = [
        'type'               => OrderType::class,
        'status'             => OrderStatus::class,
        'payment_status'     => PaymentStatus::class,
        'subtotal'           => 'decimal:2',
        'discount'           => 'decimal:2',
        'delivery_fee'       => 'decimal:2',
        'total'              => 'decimal:2',
        'estimated_ready_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
