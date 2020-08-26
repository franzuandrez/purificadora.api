<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerWallet extends Model
{
    //

    protected $table = 'customer_wallet';
    protected $primaryKey = 'customer_wallet_id';

    protected $fillable = [
        'wallet'
    ];

    public function detail()
    {
        return $this->hasMany(CustomerWalletDetail::class, 'customer_wallet_id', 'customer_wallet_id');
    }
}
