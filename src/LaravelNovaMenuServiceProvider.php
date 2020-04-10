<?php

namespace Novius\LaravelNovaMenu;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class LaravelNovaMenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
            Nova::resources(config('laravel-nova-menu.resources', []));
        });

        Nova::serving(function (ServingNova $event) {
            Nova::script('laravel-nova-menu', __DIR__.'/../dist/js/tool.js');
            Nova::style('laravel-nova-menu', __DIR__.'/../dist/css/tool.css');
        });

        $packageDir = dirname(__DIR__);

        $this->publishes([$packageDir.'/config' => config_path()], 'config');

        $this->publishes([$packageDir.'/database/migrations' => database_path('migrations')], 'migrations');
        $this->loadMigrationsFrom($packageDir.'/database/migrations');

        $this->loadViewsFrom($packageDir.'/resources/views', 'laravel-nova-menu');
        $this->publishes([$packageDir.'/resources/views' => resource_path('views/vendor/laravel-nova-menu')], 'views');

        $this->loadTranslationsFrom($packageDir.'/resources/lang', 'laravel-nova-menu');
        $this->publishes([__DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-nova-menu')], 'lang');

        Blade::directive('menu', function ($expression) {
            $expression = trim($expression, '\'"');
            $args = explode('|', $expression, 2);
            $localeFallback = 'true';
            if (isset($args[1]) && $args[1] === 'no-locale-fallback') {
                $localeFallback = 'false';
            }
            $expression = '"'.array_shift($args).'"'; // reformat the slug with quotes

            return "<?php echo Novius\LaravelNovaMenu\Helpers\MenuHelper::displayMenu($expression, $localeFallback) ?>";
        });

        foreach (config('laravel-nova-menu.observers', []) as $modelClass => $observerClass) {
            if (class_exists($modelClass) && class_exists($observerClass)) {
                $modelClass::observe($observerClass);
            }
        }
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/laravel-nova-menu')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-nova-menu.php',
            'laravel-nova-menu'
        );
    }
}
