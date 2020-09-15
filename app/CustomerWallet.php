<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CustomerWallet
 *
 * @property int $customer_wallet_id
 * @property string|null $wallet
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Customer[] $customers
 * @property-read int|null $customers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CustomerWalletDetail[] $detail
 * @property-read int|null $detail_count
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerWallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerWallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerWallet query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerWallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerWallet whereCustomerWalletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerWallet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerWallet whereWallet($value)
 * @mixin \Eloquent
 */
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
