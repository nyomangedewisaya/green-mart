<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table = 'promotions';
    protected $guarded = ['id'];
    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
