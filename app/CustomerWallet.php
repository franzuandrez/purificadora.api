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
    protected $with = [
        'customers'
    ];

    public function detail()
    {
        return $this->hasMany(CustomerWalletDetail::class, 'customer_wallet_id', 'customer_wallet_id');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class,
            'customer_wallet_detail',
            'customer_wallet_id',
            'customer_id');
    }
}
