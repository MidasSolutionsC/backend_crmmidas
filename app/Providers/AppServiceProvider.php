<?php

namespace App\Providers;

use Exception;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function boot()
    {
        Validator::extend('valid_json', function ($attribute, $value, $parameters, $validator) {
            if (!is_string($value)) {
                return false; // No es una cadena JSON válida
            }
        
            json_decode($value);
            return (json_last_error() == JSON_ERROR_NONE);
        });
    }
}
