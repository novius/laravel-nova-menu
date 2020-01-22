<?php

namespace Novius\LaravelNovaMenu;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Novius\LaravelNovaMenu\Observers\ItemObserver;
use Novius\LaravelNovaMenu\Resources\MenuItem;
use Novius\LaravelNovaMenu\Resources\Menu;

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

            Nova::resources([
                Menu::class,
                MenuItem::class,
            ]);
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
            return "<?php echo Novius\LaravelNovaMenu\Helpers\MenuHelper::displayMenu($expression) ?>";
        });

        \Novius\LaravelNovaMenu\Models\MenuItem::observe(ItemObserver::class);
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
