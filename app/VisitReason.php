<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\VisitReason
 *
 * @property int $reason_id
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $is_system
 * @method static \Illuminate\Database\Eloquent\Builder|VisitReason newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VisitReason newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VisitReason query()
 * @method static \Illuminate\Database\Eloquent\Builder|VisitReason whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VisitReason whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VisitReason whereIsSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VisitReason whereReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VisitReason whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class VisitReason extends Model
{
    //

    protected $primaryKey = 'reason_id';
    protected $table = 'visit_reason';

    protected $fillable = [
        'description'
    ];

    public function format()
    {
        return [
            'description' => $this->description
        ];
    }
}
