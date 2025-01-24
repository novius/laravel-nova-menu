<?php

namespace Novius\LaravelNovaMenu\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Novius\LaravelNovaMenu\LaravelNovaMenuServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate');
    }

    /**
     * @param  Application  $app
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelNovaMenuServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app->get('config')->set('database.default', 'sqlite');
        $app->get('config')->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app->get('config')->set('sluggable', [
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
