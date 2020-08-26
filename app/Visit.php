<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    //

    protected $table = 'visit';
    protected $primaryKey = 'visit_id';

    protected $fillable = [
        'employee_id',
        'customer_id',
        'reason_id',
        'visited_date',
        'latitude',
        'longitude'
    ];

    public function customer()
    {

        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');

    }

    public function reason()
    {
        return $this->belongsTo(VisitReason::class, 'reason_id', 'reason_id');
    }

}
