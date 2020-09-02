<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    //


    protected $primaryKey = 'id';
    protected $table = 'sales_detail';

    protected $fillable = [
        'product_id',
        'quantity',
        'price',
        'unit_price_discount',
        'subtotal',
        'special_offer_id',
        'sales_id'
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'sales_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

}
