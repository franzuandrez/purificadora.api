<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisitReason extends Model
{
    //

    protected $primaryKey = 'reason_id';
    protected $table = 'visit_reason';

    protected $fillable = [
        'description'
    ];
}
