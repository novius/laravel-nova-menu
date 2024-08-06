# Nova menu manager
[![Travis](https://img.shields.io/travis/novius/laravel-nova-menu.svg?maxAge=1800&style=flat-square)](https://travis-ci.org/novius/laravel-nova-menu)
[![Packagist Release](https://img.shields.io/packagist/v/novius/laravel-nova-menu.svg?maxAge=1800&style=flat-square)](https://packagist.org/packages/novius/laravel-nova-menu)
[![Licence](https://img.shields.io/packagist/l/novius/laravel-nova-menu.svg?maxAge=1800&style=flat-square)](https://github.com/novius/laravel-nova-menu#licence)

A Nova tool to manage menus.

## Requirements

* PHP >= 8.1
* Laravel Nova >= 4.0
* Laravel Framework >= 9.0 | >= 10.0 | >= 11.0 

> **NOTE**: These instructions are for Laravel >= 9.0 and Laravel Nova 4.0. If you are using prior version, please
> see the [previous version's docs](https://github.com/novius/laravel-nova-menu/tree/3-x).


## Installation

```sh
composer require novius/laravel-nova-menu
```

Then, launch migrations 

```sh
php artisan migrate
```

### Configuration

Some options that you can override are available.

```sh
php artisan vendor:publish --provider="Novius\LaravelNovaMenu\LaravelNovaMenuServiceProvider" --tag="config"
```

## Edit default templates

Run:

```sh
php artisan vendor:publish --provider="Novius\LaravelNovaMenu\LaravelNovaMenuServiceProvider" --tag="views"
```

## Usage

### Blade directive

You can display menu with : 

```blade
<x-laravel-nova-menu::menu menu="slug-of-menu" />
```

You can also display menu by passing the model instance :

```blade
<x-laravel-nova-menu::menu :menu="Menu::find(1)" />
```

By default a fallback to app()->getLocale() is activated. 

If you want force a specific slug with no fallback you can call :

```blade
<x-laravel-nova-menu::menu menu="slug-of-menu" :localeFallback="false" />
```

If you want to use a specific view you can call :

```blade
<x-laravel-nova-menu::menu menu="slug-of-menu" view="partial/menu" />
```

### Override views

You can override views with :

```sh
php artisan vendor:publish --provider="Novius\LaravelNovaMenu\LaravelNovaMenuServiceProvider" --tag="views"
```

### Manage internal link possibilities

Laravel Nova Menu uses [Laravel Linkable](https://github.com/novius/laravel-linkable) to manage linkable routes and models. Please read the documentation.

### Customize tree passed to the view

```php
<?php

namespace App\Providers;

use Novius\LaravelNovaMenu\LaravelNovaMenuService;

class AppServiceProvider extends ServiceProvider
{
     // ...
     
    public function boot()
    {
        /**
         * @var LaravelNovaMenuService $menu
         */
        $menu = $this->app->get('laravel-nova-menu');
        $menu->setTreeUsing(function(Menu $menu, array $tree) {
            // ... your actions on tree
            return $tree;
        });
    }
}
```

### Customize tree building

```php
<?php

namespace App\Providers;

use Novius\LaravelNovaMenu\LaravelNovaMenuService;

class AppServiceProvider extends ServiceProvider
{
     // ...
     
    public function boot()
    {
        /**
         * @var LaravelNovaMenuService $menu
         */
        $menu = $this->app->get('laravel-nova-menu');
        $menu->setBuildTreeUsing(function(Collection $items) {
            // ... your actions to build tree as an array
            return $tree;
        });
    }
}
```

## Lint

Run php-cs with:

```sh
composer run-script lint
```

## Contributing

Contributions are welcome!
Leave an issue on Github, or create a Pull Request.


## Licence

This package is under [GNU Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html) or (at your option) any later version.
