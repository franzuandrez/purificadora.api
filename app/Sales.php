<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Sales
 *
 * @property int $sales_id
 * @property int $visit_id
 * @property string $total
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SalesDetail[] $detail
 * @property-read int|null $detail_count
 * @property-read \App\Visit $visit
 * @method static \Illuminate\Database\Eloquent\Builder|Sales newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sales newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sales query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereSalesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereVisitId($value)
 * @mixin \Eloquent
 */
class Sales extends Model
{
    //

    protected $primaryKey = 'sales_id';
    protected $table = 'sales';

    public $timestamps = false;


    protected $with = [
        'detail'
    ];

    protected $fillable = [
        'visit_id',
        'total'
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id', 'visit_id');
    }

    public function detail()
    {
        return $this->hasMany(SalesDetail::class, 'sales_id', 'sales_id');
    }

    public function format()
    {
        return [
            'total' => $this->attributes['total'],
            'visit' => $this->visit->format()
        ];
    }

}
