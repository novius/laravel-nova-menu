# Nova menu manager
[![Travis](https://img.shields.io/travis/novius/laravel-nova-menu.svg?maxAge=1800&style=flat-square)](https://travis-ci.org/novius/laravel-nova-menu)
[![Packagist Release](https://img.shields.io/packagist/v/novius/laravel-nova-menu.svg?maxAge=1800&style=flat-square)](https://packagist.org/packages/novius/laravel-nova-menu)
[![Licence](https://img.shields.io/packagist/l/novius/laravel-nova-menu.svg?maxAge=1800&style=flat-square)](https://github.com/novius/laravel-nova-menu#licence)

A Nova tool to manage menus.

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

## Edit default config and templates

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

### Override views

You can override views with :

```sh
php artisan vendor:publish --provider="Novius\LaravelNovaMenu\LaravelNovaMenuServiceProvider" --tag="views"
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
