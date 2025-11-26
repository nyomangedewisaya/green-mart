<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerBankAccount extends Model
{
    protected $table = 'seller_bank_accounts';
    protected $guarded = ['id'];

    public function bankAccounts()
    {
        return $this->belongsTo(Seller::class);
    }

    public function getRouteKeyName()
    {
        return 'bank_name';
    }
}
