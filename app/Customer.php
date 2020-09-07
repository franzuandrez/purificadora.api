<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //

    protected $table = 'customer';
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'name',
        'last_name',
        'nickname',
        'address',
        'latitude',
        'longitude',
        'last_date_visited'
    ];
    protected $dates = [
        'last_date_visited'
    ];


    public function lastVisits()
    {
        return $this->hasMany(Visit::class, 'customer_id', 'customer_id')
            ->orderBy('visited_date', 'desc')
            ->limit(10);
    }

    public function format()
    {
        return [
            'name' => $this->name,
            'last_name' => $this->last_name,
            'nickname' => $this->nickname,
            'address' => $this->address
        ];
    }


}
