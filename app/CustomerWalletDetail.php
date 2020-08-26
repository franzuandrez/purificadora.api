<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerWalletDetail extends Model
{
    //

    protected $table = 'customer_wallet_detail';
    protected $primaryKey = 'id';

    protected $fillable = [
        'customer_wallet_id',
        'customer_id',
    ];


    public function customerWallet()
    {
        return $this->belongsTo(CustomerWallet::class, 'customer_wallet_id', 'customer_wallet_id');
    }


}
