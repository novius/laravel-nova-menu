<?php

namespace Novius\LaravelNovaMenu\Tests;

use Illuminate\Support\Facades\Artisan;
use Novius\LaravelNovaMenu\LaravelNovaMenuServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelNovaMenuServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('sluggable', [
            'onUpdate' => false,
            'separator' => '-',
            'method' => null,
            'maxLength' => null,
            'maxLengthKeepWords' => true,
            'slugEngineOptions' => [],
            'reserved' => null,
            'unique' => true,
            'includeTrashed' => false,
        ]);
    }
}
