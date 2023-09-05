<?php

namespace App\Helpers;

use App\Geoip;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GeoipHelper
{
    public $apiKeys;
    protected $geoip = [];

    public $monthArr = [
        'Jan' => 'Ene',
        'Feb' => 'Feb',
        'Mar' => 'Mar',
        'Apr' => 'Abr',
        'May' => 'May',
        'Jun' => 'Jun',
        'Jul' => 'Jul',
        'Aug' => 'Ago',
        'Sep' => 'Sep',
        'Oct' => 'Oct',
        'Nov' => 'Nov',
        'Dec' => 'Dic'
    ];

    public function __construct()
    {
        $this->apiKeys = [
            'd78c23c908f443ebaaf1f0f03b381deb',
            '0cfdcbd4900b415790c4c218c0882127'
        ];
    }

    protected function getGeoipData($ip)
    {
        foreach ($this->apiKeys as $apiKey) {
            $client = new Client([
                'base_uri' => "https://api.ipgeolocation.io/ipgeo?apiKey={$apiKey}&ip={$ip}",
                'proxy' => getenv('HTTP_PROXY'),
            ]);
            try {
                $response = $client->request('GET');
                $originals = json_decode($response->getBody(), true);
                if ($response->getStatusCode() !== 200) {
                    break;
                }
                $originals['time_zone'] = $originals['time_zone']['name'];
                $originals['currency'] = $originals['currency']['code'];
                return $originals;
            } catch (\Exception $e) {
                //
            }
        }
        return null;
    }

    public function getGeoip($ip = null)
    {
        if (empty($ip)) {
            if (app()->environment('local')) {
                $ip = '183.182.113.139';
            } else {
                $ip = Request::ip();
            }
        }
        if (!isset($this->geoip[$ip])) {
            $geoip = Geoip::find($ip);
            if ($geoip === null) {
                $geoipData = $this->getGeoipData($ip);
                if ($geoipData) {
                    $geoip = Geoip::create($geoipData);
                }
            }
            $this->geoip[$ip] = $geoip;
        }

        // timezone correction
        if ($this->geoip[$ip] && $this->geoip[$ip]->city == 'Coquitlam' && $this->geoip[$ip]->state_prov == 'British Columbia') {
            $this->geoip[$ip]->time_zone = 'America/Vancouver';
        }
        
        return $this->geoip[$ip];
    }

    public function getLocalizedDate($date_time, $type = 'date') {
        $geoip = $this->getGeoip();

        $start_at_et = Carbon::createFromFormat('Y-m-d H:i:s', $date_time, 'UTC')->setTimezone('America/New_York');
        $start_at_local = Carbon::createFromFormat('Y-m-d H:i:s', $date_time, 'UTC')
            ->setTimezone($geoip ? $geoip->time_zone : 'UTC');

        if (app('translator')->getLocale() == 'es') {
            $date_et = $start_at_et->format('d/M/Y');
            // $start_at_et->locale('es');
            // $start_at_local->locale('es');
        } else {
            $date_et = $start_at_et->format('M/d/Y');
            // $start_at_et->locale('en');
            // $start_at_local->locale('en');
        }
        
        $date_lo = $start_at_local->format('d/m/Y');
        $time_et = $start_at_et->format('g:i A');
        $time_lo = $start_at_local->format('g:i A');

        if ($type == 'date') {
            // if ($date_et == $date_lo) {
            //     return $date_et;
            // } else {
            //     return $date_et . ' (' . $date_lo . ')';
            // }
            return $date_et;
        } else if ($type == 'date_string') {
            // if ($date_et == $date_lo) {
            //     return $start_at_et->format('d M Y');
            // } else {
            //     return $start_at_et->format('d M Y') . ' (' . $start_at_local->format('d M Y') . ')';
            // }
            return $start_at_et->format('d M Y');
        } else if ($type == 'date_no_year') {
            // if ($date_et == $date_lo) {
            //     return $start_at_et->format('d M');
            // } else {
            //     return $start_at_et->format('d M') . ' (' . $start_at_local->format('d M') . ')';
            // }
            return $start_at_et->format('d M');
        } else if ($type == 'date_time') {
            if (app('translator')->getLocale() == 'es') {
                $date_time_et = $start_at_et->format('d');
                $date_time_et .= '/' . $this->monthArr[$start_at_et->format('M')] . '/';
                $date_time_et .= $start_at_et->format('Y g:i A');
            } else {
                $date_time_et = $start_at_et->format('M/d/Y g:i A');
            }
            
            // $date_time_lo = $start_at_local->format('d/m/Y g:i A');
            // if ($date_time_et == $date_time_lo) {
            //     return $date_time_et;
            // } else {
            //     return $date_time_et . ' ET (' . $date_time_lo . ')';
            // }
            return $date_time_et . ' ET';
        } else if ($type == 'time') {
            // if ($date_et != $date_lo) {
            //     return $time_et . ' ET (' . $start_at_local->format('M j, g:i A') . ')';
            // } else {
            //     if ($time_et == $time_lo) {
            //         return $time_et;
            //     } else {
            //         return $time_et . ' ET (' . $time_lo . ')';
            //     }
            // }
            return $time_et . ' ET';
        }
    }
}
