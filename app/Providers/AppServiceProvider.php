<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use App\Helpers\ConstantsHelper;
use App\Helpers\DataPullHelper;
use App\Helpers\OptionsHelper;
use App\Helpers\CalculationHelper;
use App\Helpers\DataHelper;
use App\Helpers\FormatHelper;
use App\Helpers\UtilsHelper;
use App\Helpers\GeoipHelper;
use App\Helpers\EvaluationHelper;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/es') === 0) {
            setlocale(LC_TIME, 'es');
            app('translator')->setLocale('es');
        }

        $this->app->singleton('dataPull', function ($app) {
            $dataPull = new DataPullHelper();
            return $dataPull;
        });

        $this->app->singleton('options', function ($app) {
            $dataPull = new OptionsHelper();
            return $dataPull;
        });

        $this->app->singleton('constants', function ($app) {
            $constants = new ConstantsHelper();
            return $constants;
        });

        $this->app->singleton('calculation', function ($app) {
            $calculation = new CalculationHelper();
            return $calculation;
        });

        $this->app->singleton('evaluation', function ($app) {
            $evaluation = new EvaluationHelper();
            return $evaluation;
        });

        $this->app->singleton('data', function ($app) {
            $data = new DataHelper();
            return $data;
        });

        $this->app->singleton('format', function ($app) {
            $format = new FormatHelper();
            return $format;
        });

        $this->app->singleton('utils', function ($app) {
            $utils = new UtilsHelper();
            return $utils;
        });

        $this->app->singleton('geoip', function ($app) {
            $geoip = new GeoipHelper();
            return $geoip;
        });
        Validator::extend(
            'recaptcha',
            'App\Validators\ReCaptcha@validate'
        );
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        return ['dataPull', 'options', 'constants', 'calculation', 'evaluation', 'format', 'utils', 'geoip'];
    }
}
