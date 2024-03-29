<?php

use Novius\LaravelNovaMenu\Observers\ItemObserver;
use Novius\LaravelNovaMenu\Resources\Menu;
use Novius\LaravelNovaMenu\Resources\MenuItem;

return [
    /*
     * Entities linkable by a menu item. For instance "Pages".
     * So the pages of your application will be listed and linkable by an item menu.
     *
     * It must contain pairs of:
     *      full-class-name => prefix for the list in backoffice
     *
     * The "prefix for the list in backoffice" will be the parameter of the laravel function trans().
     *      For instance: App\Models\Page::class => 'path.to.translation.page',
     *
     * Warning: The models listed below must use the trait Linkable and optionally override methods to suit their needs.
     */

    'linkable_objects' => [],

    /*
     * Sometimes you need to link items that are not objects.
     *
     * This config allows you to link routes.
     *     For instance: 'contact' => 'page.contact'
     *
     * "contact" will be the parameter of the laravel function route().
     * "page.contact" will be the parameter of the laravel function trans().
     */
    'linkable_routes' => [],

    /*
     |--------------------------------------------------------------------------
     | Locales
     |--------------------------------------------------------------------------
     |
     | Set all the available locales as [locale => name] pairs.
     |
     | For example ['en' => 'English'].
     |
     */
    'locales' => [
        'en' => 'English',
        // 'fr' => 'Français',
    ],

    /*
     * The max number of html's characters in menu field
     */
    'menu_item_html_max_size' => 100,

    /*
     * Customizable resources
     */
    'resources' => [
        Menu::class,
        MenuItem::class,
    ],

    /*
     * Customizable observers
     *
     * Format : Model::class => Observer::class
     */
    'observers' => [
        \Novius\LaravelNovaMenu\Models\MenuItem::class => ItemObserver::class,
    ],

];
