<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SalesDetail
 *
 * @property int $id
 * @property int $product_id
 * @property string $quantity
 * @property string $price
 * @property string $unit_price_discount
 * @property string $subtotal
 * @property int|null $special_offer_id
 * @property int|null $sales_id
 * @property string|null $total
 * @property-read \App\Product $product
 * @property-read \App\Sales|null $sales
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereSalesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereSpecialOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereUnitPriceDiscount($value)
 * @mixin \Eloquent
 */
class SalesDetail extends Model
{
    //


    protected $primaryKey = 'id';
    protected $table = 'sales_detail';
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'quantity',
        'price',
        'unit_price_discount',
        'subtotal',
        'special_offer_id',
        'sales_id',
        'total'
    ];

    protected $with = ['product'];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'sales_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

}
