<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //


    protected $table = 'payments';

    protected $fillable = [
        'debt_id',
        'quantity',
        'total',
        'employee_id',
        'customer_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Employee::class, 'customer_id', 'customer_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function debt()
    {
        return $this->belongsTo(Debts::class, 'debt_id', 'id');
    }
}
