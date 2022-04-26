# Nova menu manager
[![Travis](https://img.shields.io/travis/novius/laravel-nova-menu.svg?maxAge=1800&style=flat-square)](https://travis-ci.org/novius/laravel-nova-menu)
[![Packagist Release](https://img.shields.io/packagist/v/novius/laravel-nova-menu.svg?maxAge=1800&style=flat-square)](https://packagist.org/packages/novius/laravel-nova-menu)
[![Licence](https://img.shields.io/packagist/l/novius/laravel-nova-menu.svg?maxAge=1800&style=flat-square)](https://github.com/novius/laravel-nova-menu#licence)

A Nova tool to manage menus.

## Requirements

* PHP >= 8.1
* Laravel Nova >= 4.0
* Laravel Framework >= 9.0

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
@menu("slug-of-menu")
```

By default a fallback to app()->getLocale() is activated. 

If you want force a specific slug with no fallback you can call :

```blade
@menu("slug-of-menu|no-locale-fallback")
```

### Override views

You can override views with :

```sh
php artisan vendor:publish --provider="Novius\LaravelNovaMenu\LaravelNovaMenuServiceProvider" --tag="views"
```

### Manage internal link possibilities

**linkable_objects**

You can add dynamic routes to `linkable_objects` array (in configuration file).

Example with `App\Models\Foo` Model.

In this example we have a route defined as following :

```php
Route::get('foo/{slug}', 'FooController@show')->name('foo.show');
```

First, you have to add the Model to `laravel-nova-menu.php` config file.

```php
return [
    'linkable_objects'=> [
        App\Models\Foo:class => 'foo.label', // foo.label is a translation key
    ],
    ...
];
```

Then, you have to implements `Linkable` trait to the model.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Novius\LaravelNovaMenu\Traits\Linkable;

class Foo extends Model
{
    use Linkable;

    public function linkableUrl(): string
    {
        return route('foo.show', ['slug' => $this->slug]);
    }

    public function linkableTitle(): string
    {
        return $this->name;
    }
}
```

**linkable_routes**

You can also add static routes to `linkable_routes` array (in configuration file).

Example with a route with name `home`.

```php
return [
    'linkable_objects'=> [
        'contact' => 'contact.page', // contact.page is a translation key
    ],
    ...
];
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
