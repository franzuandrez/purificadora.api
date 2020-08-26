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
        'status',
        'user_id'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }
}
