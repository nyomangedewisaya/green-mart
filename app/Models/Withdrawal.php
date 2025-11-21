<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $table = 'withdrawals';
    protected $guarded = ['id'];
    protected $casts = [
        'amount' => 'integer'
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
