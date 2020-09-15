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
        return $this->visits()
            ->orderBy('visited_date', 'desc')
            ->limit(10);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class, 'customer_id', 'customer_id');

    }


    public function borrowed_carboys()
    {
        return
            $this->visits()
                ->whereHas('borrowedCarboys', function (Builder $query) {
                    return $query->where('type', 'B');
                });

    }


    public function retured_carboys()
    {
        return
            $this->visits()
                ->whereHas('borrowedCarboys', function (Builder $query) {
                    return $query->where('type', 'R');
                });
    }

    public function format()
    {
        return [
            'customer_id' => $this->customer_id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'nickname' => $this->nickname,
            'address' => $this->address,
            'latitude'=>$this->latitude,
            'longitude'=>$this->longitude,
        ];
    }


}
