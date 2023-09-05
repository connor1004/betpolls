<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Facades\Utils;

class ManualFutureVote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'page_id', 'future_id', 'user_id', 'answer_id', 'score', 'matched',
        'calculated', 'calculated_at'
    ];

    public function page()
    {
        return $this->belongsTo('App\ManualPollPage');
    }

    public function future() {
        return $this->belongsTo('App\ManualFuture');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function answer() {
      return $this->belongsTo('App\ManualFutureAnswer');
  }
}
