<?php

namespace bernardomacedo\DBTranslator;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;

class DBTranslatorServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        /**
         * php artisan vendor:publish --provider="bernardomacedo\DBTranslator\DBTranslatorServiceProvider"
         */
        $this->publishes([
            __DIR__.'/../resources/config/laravel-db-translator.php' => $this->app->configPath().'/db-translator.php',
        ], 'config');

        $this->publishes([
            __DIR__.'/../resources/lang' => base_path('resources/lang/vendor/dbtranslator'),
        ], 'lang');
        
        if (!class_exists('CreateTranslationsTable')) {
            // Publish the migration
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__.'/../resources/migrations/create_translations_tables.php.stub' => $this->app->basePath().'/'.'database/migrations/'.$timestamp.'_create_translations_tables.php',
            ], 'migrations');
        }
        /**
         * Create an helper file for using at blade like {{ intl() }}
         */
        require __DIR__.'/../Helpers/intl.php';

        Storage::extend('translations', function() {
            $client = [
                'driver'    => 'local',
                'root'      => base_path('resources/vendor/dbtranslator')
            ]
            return new Filesystem($client);
        });

    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('dbtranslator', 'Bernardomacedo\DBTranslator\DBTranslator');
    }
}
