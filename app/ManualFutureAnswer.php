<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Facades\Utils;

class ManualFutureAnswer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'page_id', 'future_id', 'candidate_id', 'display_order', 'vote_count',
        'score', 'standing', 'odds', 'winning_points', 'losing_points', 'is_absent',
        'meta', 'meta_es',
    ];

    protected $casts = [
        'meta' => 'array',
        'meta_es' => 'array',
    ];

    public static function getNextDisplayOrder()
    {
        return ManualFutureAnswer::max('display_order') + 1;
    }

    public function page()
    {
        return $this->belongsTo('App\ManualPollPage');
    }

    public function future() {
        return $this->belongsTo('App\ManualFuture');
    }

    public function candidate() {
        return $this->belongsTo('App\ManualCandidate', 'candidate_id');
    }
}
