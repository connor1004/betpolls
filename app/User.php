<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\SoftDeletes;

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Carbon\Carbon;
use App\Facades\Utils;
use App\Leaderboard;
use App\Point;
use DB;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    public static $ROLE_UNKNOWN = 'unknown';
    public static $ROLE_ADMIN = 'admin';
    public static $ROLE_VOTER = 'voter';

    use Authenticatable, Authorizable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'username', 'email', 'password',
        'country',
        'role', 'confirmed', 'robot'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function getConfirmationUrlAttribute()
    {
        $key = env('APP_KEY');
        $token = [
            'exp' => time() + 60 * 15,
            'data' => [
                'current' => time(),
                'id' => $this->id,
                'email' => $this->email
            ]
        ];
        $jwt = JWT::encode($token, $key);
        return Utils::localeUrl('confirm') . "?" . http_build_query(['code' => urlencode($jwt)]);
    }

    public function getResetPasswordUrlAttribute()
    {
        $key = env('APP_KEY');
        $key = env('APP_KEY');
        $token = [
            'exp' => time() + 60 * 15,
            'data' => [
                'current' => time(),
                'id' => $this->id,
                'requester_email' => $this->email
            ]
        ];
        $jwt = JWT::encode($token, $key);
        return Utils::localeUrl('reset') . "?" . http_build_query(['code' => urlencode($jwt)]);
    }

    public function getUrlAttribute()
    {
        return url('users', [$this->username]);
    }

    public function getUrlEsAttribute()
    {
        return url('es/usuarios', [$this->username]);
    }

    public function getLocaleUrlAttribute()
    {
        $locale = app('translator')->getLocale();
        if ($locale === 'es') {
            if (!empty($this->url_es)) {
                return $this->url_es;
            }
        }
        return $this->url;
    }

    public function getNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function isValidConfirmCode($code)
    {
        $key = env('APP_KEY');
        try {
            $credential = JWT::decode($code, $key, ['HS256']);
            return ($credential->data->email === $this->email);
        } catch (ExpiredException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function isValidResetCode($code)
    {
        $key = env('APP_KEY');
        try {
            $credential = JWT::decode($code, $key, ['HS256']);
            return ($credential->data->requester_email === $this->requester_email);
        } catch (ExpiredException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function findByCode($code)
    {
        $key = env('APP_KEY');
        try {
            $credential = JWT::decode($code, $key, ['HS256']);
            return User::find($credential->data->id);
        } catch (ExpiredException $e) {
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getLatestPoint($type, $sport_category_id, $league_id)
    {
        $start_at = (new Carbon)->setTimezone('America/New_York')->subYears(1)->startOfYear()->format('Y-m-d');
        return Point::where([
            'user_id' => $this->id,
            'start_at' => $start_at,
            'type' => $type,
            'sport_category_id' => $sport_category_id,
            'league_id' => $league_id
        ])->first();
    }

    public function getRecentPoint($type, $sport_category_id, $league_id)
    {
        $start_at = (new Carbon)->setTimezone('America/New_York')->startOfYear()->format('Y-m-d');
        return Point::where([
            'user_id' => $this->id,
            'start_at' => $start_at,
            'type' => $type,
            'sport_category_id' => $sport_category_id,
            'league_id' => $league_id
        ])->first();
    }

    public function getLatestLeaderboard($type, $sport_category_id, $league_id, $period_type)
    {
        switch ($period_type) {
            case Leaderboard::$PERIOD_TYPE_WEEKLY:
                $start_at = (new Carbon)->setTimezone('America/New_York')->subWeek(1)->startOfWeek()->format('Y-m-d');
                break;
            case Leaderboard::$PERIOD_TYPE_MONTHLY:
                $start_at = (new Carbon)->setTimezone('America/New_York')->subMonths(1)->startOfMonth()->format('Y-m-d');
                break;
            case Leaderboard::$PERIOD_TYPE_YEARLY:
            default:
                $start_at = (new Carbon)->setTimezone('America/New_York')->subYears(1)->startOfYear()->format('Y-m-d');
                break;
        }

        return Leaderboard::where([
            'user_id' => $this->id,
            'type' => $type,
            'sport_category_id' => $sport_category_id,
            'league_id' => $league_id,
            'period_type' => $period_type,
            'start_at' => $start_at
        ])->first();
    }

    public function getMedalsGroupsAttribute()
    {

        $leaderboards = DB::table('leaderboards')
            ->select(DB::raw('count(*) AS count'), 'position', 'period_type')
            ->where('league_id', 0)
            ->where('sport_category_id', 0)
            ->where('type', 0)
            ->where('user_id', $this->id)
            ->whereIn('position', [1, 2, 3])
            ->where('period_type', '<>', 'forever')
            ->groupBy('position', 'period_type')
            ->orderBy('position', 'ASC')
            ->get();

        $leaderboards_groups = [
            'weekly' => (object) [
                'label' => trans('app.weekly_medals'),
                'items' => []
            ],
            'monthly' => (object) [
                'label' => trans('app.monthly_medals'),
                'items' => []
            ],
            'yearly' => (object) [
                'label' => trans('app.yearly_medals'),
                'items' => []
            ]
        ];

        foreach ($leaderboards as $leaderboard) {
            $leaderboards_groups[$leaderboard->period_type]->items[] = $leaderboard;
        }

        return $leaderboards_groups;
    }

    public function getTrophiesAttribute()
    {
        $start_at = (new Carbon)->setTimezone('America/New_York')->startOfYear()->format('Y-m-d');
        $points = DB::table('points')
            ->select(DB::raw('count(*) AS count'), 'position')
            ->where('start_at', '<', $start_at)
            ->where('league_id', 0)
            ->where('sport_category_id', 0)
            ->where('type', 0)
            ->where('user_id', $this->id)
            ->whereIn('position', [1, 2, 3])
            ->groupBy('position')
            ->orderBy('position', 'ASC')
            ->get();
        return $points;
    }
}
