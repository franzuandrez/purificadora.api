<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\BorrowedCarboy
 *
 * @property int $id
 * @property int $visit_id
 * @property int $quantity
 * @property string|null $type B=Borrowed,R=Returned
 * @property string|null $observations
 * @property-read \App\Visit $visit
 * @method static \Illuminate\Database\Eloquent\Builder|BorrowedCarboy borrowed()
 * @method static \Illuminate\Database\Eloquent\Builder|BorrowedCarboy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BorrowedCarboy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BorrowedCarboy query()
 * @method static \Illuminate\Database\Eloquent\Builder|BorrowedCarboy returned()
 * @method static \Illuminate\Database\Eloquent\Builder|BorrowedCarboy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BorrowedCarboy whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BorrowedCarboy whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BorrowedCarboy whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BorrowedCarboy whereVisitId($value)
 * @mixin \Eloquent
 */
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
