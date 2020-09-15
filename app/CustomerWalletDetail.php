<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CustomerWalletDetail
 *
 * @property int $id
 * @property int|null $customer_wallet_id
 * @property int|null $customer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\CustomerWallet|null $customerWallet
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerWalletDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerWalletDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerWalletDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerWalletDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerWalletDetail whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerWalletDetail whereCustomerWalletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerWalletDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerWalletDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
