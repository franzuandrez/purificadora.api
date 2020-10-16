<?php

namespace App;

use Eloquent;
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
 * @method static \Illuminate\Database\Eloquent\Builder|CarboyMovement borrowed()
 * @method static \Illuminate\Database\Eloquent\Builder|CarboyMovement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CarboyMovement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CarboyMovement query()
 * @method static \Illuminate\Database\Eloquent\Builder|CarboyMovement returned()
 * @method static \Illuminate\Database\Eloquent\Builder|CarboyMovement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CarboyMovement whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CarboyMovement whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CarboyMovement whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CarboyMovement whereVisitId($value)
 * @mixin Eloquent
 */
class CarboyMovement extends Model
{
    //


    protected $primaryKey = 'id';
    protected $table = 'carboys_movements';

    protected $fillable = [
        'quantity',
        'type',
        'observations'
    ];
    protected $with = [
        'visit'
    ];
    public $timestamps = false;

    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id', 'visit_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function scopeBorrowed($query)
    {
        return $query->where('carboys_movements.type', 'B');
    }


    public function scopeReturned($query)
    {
        return $query->where('carboys_movements.type', 'R');
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
