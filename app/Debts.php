<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Debts
 *
 * @property int $id
 * @property int $product_id
 * @property int $visit_id
 * @property string $quantity
 * @property string|null $price
 * @property string|null $total
 * @property int $employee_collector_id
 * @property string|null $payment_date
 * @property string|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|Debts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Debts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Debts query()
 * @method static \Illuminate\Database\Eloquent\Builder|Debts whereEmployeeCollectorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Debts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Debts wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Debts wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Debts whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Debts whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Debts whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Debts whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Debts whereVisitId($value)
 * @mixin Eloquent
 */
class Debts extends Model
{
    //

    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'visit_id',
        'quantity',
        'price',
        'total',
        'employee_collector_id',
        'payment_date',
        'status',
        'customer_id'
    ];
    protected $with = [
        'product',
        'visit'
    ];

    public function customer()
    {
        return $this
            ->belongsTo(Customer::class, 'customer_id',
                'customer_id');
    }

    public function visit()
    {
        return $this
            ->belongsTo(Visit::class, 'visit_id',
                'visit_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'debt_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

}
