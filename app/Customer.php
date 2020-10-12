<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;


/**
 * App\Customer
 *
 * @property int $customer_id
 * @property string $name
 * @property string|null $last_name
 * @property string|null $nickname
 * @property string|null $address
 * @property float|null $latitude
 * @property float|null $longitude
 * @property Carbon|null $last_date_visited
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Visit[] $visits
 * @property-read int|null $visits_count
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereLastDateVisited($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Customer extends Model
{
    //

    protected $table = 'customer';
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'name',
        'last_name',
        'nickname',
        'address',
        'latitude',
        'longitude',
        'last_date_visited'
    ];
    protected $dates = [
        'last_date_visited'
    ];



    public function lastVisits()
    {
        return $this->visits()
            ->orderBy('visited_date', 'desc')
            ->limit(5);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class, 'customer_id', 'customer_id');

    }


    public function carboys_movements()
    {
        return
            $this->visits()
                ->has('carboys_movements');

    }

    public function borrowed_carboys()
    {

    }


    public function format()
    {
        return [
            'customer_id' => $this->customer_id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'nickname' => $this->nickname,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,

        ];
    }


}
