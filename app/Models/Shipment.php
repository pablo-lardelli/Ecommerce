<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'driver_id',
        'status',
        'fefunded_at',
        'delivered_at',
    ];

    public $casts = [
        'status' => ShipmentStatus::class,
        'fefunded_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
