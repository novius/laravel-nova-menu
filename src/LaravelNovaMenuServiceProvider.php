<?php

namespace Novius\LaravelNovaMenu;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Nova;

class LaravelNovaMenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            if (! $this->app->runningUnitTests()) {
                Nova::resources(config('laravel-nova-menu.resources', []));
            }
        });

        $packageDir = dirname(__DIR__);

        $this->publishes([$packageDir.'/config' => config_path()], 'config');

        $this->publishes([$packageDir.'/database/migrations' => database_path('migrations')], 'migrations');
        $this->loadMigrationsFrom($packageDir.'/database/migrations');

        $this->loadViewsFrom($packageDir.'/resources/views', 'laravel-nova-menu');
        $this->publishes([$packageDir.'/resources/views' => resource_path('views/vendor/laravel-nova-menu')], 'views');

        $this->loadTranslationsFrom($packageDir.'/lang', 'laravel-nova-menu');
        $this->publishes([__DIR__.'/../lang' => lang_path('vendor/laravel-nova-menu')], 'lang');

        Blade::directive('menu', function ($expression) {
            return "<?php echo Novius\LaravelNovaMenu\Helpers\MenuHelper::displayMenu($expression) ?>";
        });

        foreach (config('laravel-nova-menu.observers', []) as $modelClass => $observerClass) {
            if (class_exists($modelClass) && class_exists($observerClass)) {
                $modelClass::observe($observerClass);
            }
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-nova-menu.php',
            'laravel-nova-menu'
        );
    }
}
