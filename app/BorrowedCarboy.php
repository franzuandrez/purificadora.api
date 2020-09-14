<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BorrowedCarboy extends Model
{
    //


    protected $primaryKey = 'id';
    protected $table = 'borrowed_carboy';

    protected $fillable = [
        'quantity',
        'type',
        'observations'
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id', 'visit_id');
    }

    public function scopeBorrowed($query)
    {
        return $query->where('borrowed_carboy.type', 'B');
    }


    public function scopeReturned($query)
    {
        return $query->where('borrowed_carboy.type', 'R');
    }

    public function format()
    {
        return [
            'type' => $this->type == 'B' ? 'Borrowed' : 'Returned',
            'quantity' => $this->quantity,
            'observations' => $this->observations
        ];
    }


}
