<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $guarded = ['id'];
    protected $casts = [
        'order_date' => 'date',
        'receive_date' => 'date',
        'total_amount' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
