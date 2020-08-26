<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    //

    protected $table = 'employee';
    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'name',
        'last_name',
        'status'
    ];
}
