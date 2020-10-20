<?php

namespace App;


use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Visit
 *
 * @property int $visit_id
 * @property int $employee_id
 * @property int $customer_id
 * @property Carbon $visited_date
 * @property int $reason_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CarboyMovement|null $borrowedCarboys
 * @property-read Customer $customer
 * @property-read Employee $employee
 * @property-read VisitReason $reason
 * @property-read Collection|Sales[] $sales
 * @property-read int|null $sales_count
 * @method static \Illuminate\Database\Eloquent\Builder|Visit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Visit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Visit query()
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereVisitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereVisitedDate($value)
 * @mixin Eloquent
 */
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


    protected $dates = [
        'visited_date'
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

    public function sales()
    {
        return $this->hasOne(Sales::class, 'visit_id', 'visit_id');
    }

    public function carboys_movements()
    {
        return $this->hasOne(CarboyMovement::class, 'visit_id', 'visit_id');
    }

    public function format()
    {
        return [
            'visited_date' => $this->visited_date,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'customer' => $this->customer->format(),
            'employee' => $this->employee->format(),
            'reason' => $this->reason->format()
        ];
    }


}
