<?php

namespace Rpj\Daterangepicker;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FilterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('jquery', __DIR__.'/../dist/js/jquery.min.js');
            Nova::script('moment', __DIR__.'/../dist/js/moment.min.js');
            Nova::script('daterangepicker', __DIR__.'/../dist/js/daterangepicker.min.js');
            Nova::style('daterangepicker', __DIR__.'/../dist/css/daterangepicker.css');
            Nova::script('daterangepicker_', __DIR__.'/../dist/js/filter.js');
            Nova::style('daterangepicker_', __DIR__.'/../dist/css/filter.css');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
