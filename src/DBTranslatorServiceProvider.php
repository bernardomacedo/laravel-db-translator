<?php

namespace bernardomacedo\DBTranslator;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use bernardomacedo\DBTranslator\Commands\DBTranslatorAdd;
use bernardomacedo\DBTranslator\Commands\DBTranslatorGenerate;
use bernardomacedo\DBTranslator\Commands\DBTranslatorRemove;

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
            __DIR__.'/../resources/config/laravel-db-translator.php' => config_path('db-translator.php'),
        ], 'config');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'dbtranslator');
        
        $this->publishes([
            __DIR__.'/../resources/lang' => base_path('resources/lang/vendor/dbtranslator'),
        ], 'lang');

        if (!class_exists('CreateTranslationsTable')) {
            // Publish the migration
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__.'/../resources/migrations/create_translations_tables.php.stub' => $this->app->basePath().'/database/migrations/'.$timestamp.'_create_translations_tables.php',
            ], 'migrations');
        }
        /**
         * Create an helper file for using at blade like {{ intl() }}
         */
        require_once __DIR__.'/Helpers/intl.php';

        Storage::extend('translations', function() {
            $client = [
                'driver'    => 'local',
                'root'      => base_path('resources/vendor/dbtranslator')
            ];
            return new Filesystem($client);
        });
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../resources/config/laravel-db-translator.php', 'db-translator'
        );
        $this->app->singleton('dbtranslator', 'Bernardomacedo\DBTranslator\DBTranslator');

        $this->app->singleton('dbtranslator.add', function ($app) {
            return new DBTranslatorAdd();
        });
        $this->app->singleton('dbtranslator.remove', function ($app) {
            return new DBTranslatorRemove();
        });
        $this->app->singleton('dbtranslator.generate', function ($app) {
            return new DBTranslatorGenerate();
        });

        $this->commands([
            'dbtranslator.add' // checks translations and adds to default english
        ]);
        $this->commands([
            'dbtranslator.remove' // checks all views and removes entries from database
        ]);
        $this->commands([
            'dbtranslator.generate' // checks all views and removes entries from database
        ]);
    }
}
