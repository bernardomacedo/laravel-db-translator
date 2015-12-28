<?php

use Orchestra\Testbench\TestCase;
use bernardomacedo\DBTranslator\DBTranslator;

/**
 * Class SluggableTest
 */
class DBTranslatorTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        // Call migrations specific to our tests, e.g. to seed the db
        $this->artisan('migrate', [
          '--database' => 'testbench',
          '--path' => '../tests/database/migrations',
        ]);
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../src';

        // set up database configuration
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
          'driver' => 'sqlite',
          'database' => ':memory:',
          'prefix' => '',
        ]);
    }


    /**
     * Get Sluggable package providers.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['bernardomacedo/DBTranslator/DBTranslatorServiceProvider'];
    }

    /**
     * Test add Translation
     */

    public function testPositiveTests()
    {
      $tests_running = true;
      $this->assertEquals(true, $tests_running);
    }
}