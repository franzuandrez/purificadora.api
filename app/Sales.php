<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    //

    protected $primaryKey = 'sales_id';
    protected $table = 'sales';

    protected $fillable = [
        'visit_id',
        'total'
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id', 'visit_id');
    }

    public function detail()
    {
        return $this->hasMany(SalesDetail::class, 'sales_id', 'sales_id');
    }

}
