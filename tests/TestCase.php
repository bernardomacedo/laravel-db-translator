<?php

namespace bernardomacedo\DBTranslator\Test;

use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();
        //$this->app['cache']->clear();
        $this->setUpDatabase($this->app);
        $this->setUpRoutes($this->app);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \bernardomacedo\DBTranslator\DBTranslatorServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Intl'        => \bernardomacedo\DBTranslator\Models\Intl::class,
            'Languages'   => \bernardomacedo\DBTranslator\Models\Languages::class,
            'Translated'  => \bernardomacedo\DBTranslator\Models\Translated::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('app.key', 'sF5r4kJy5HEcOEx3NWxUcYj1zLZLHxuu');
        $app['config']->set('fallback_locale', 'en');
        $app['config']->set('locale', 'en');

    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $this->artisan('migrate', ['--realpath' => realpath(__DIR__ . '/database/migrations')]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpRoutes($app)
    {
        \Route::get('/', ['middleware' => 'localize', function () {
            return 'Whoops';
        }]);
    }
}
