<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UpcomingVisit extends Model
{
    //

    protected $table = 'upcoming_visits';


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

}
