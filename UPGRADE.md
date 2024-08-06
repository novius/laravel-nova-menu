# Nova menu manager Upgrade guide

## From v6 to v7

Laravel Nova Menu now uses [Laravel Linkable](https://github.com/novius/laravel-linkable) to manage linkable routes and models. Please read the documentation.

* The config keys `linkable_objects` and `linkable_routes` are now delegate to Laravel Linkable. You can remove them from the config file `laravel-nova-menu` and report their value in the new `laravel-linkable` config file.
* Modify all your models using \Novius\LaravelNovaMenu\Traits\Linkable trait to use the new \Novius\LaravelLinkable\Traits\Linkable. You can remove `linkableUrl` and `linkableTitle` method of your model.

## From v5 to v6

The blade directive `@menu` is deprecated and will be removed in future versions. Use blade component `<x-laravel-nova-menu::menu />` instead.  
