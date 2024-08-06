<?php

use Novius\LaravelNovaMenu\Observers\ItemObserver;
use Novius\LaravelNovaMenu\Resources\Menu;
use Novius\LaravelNovaMenu\Resources\MenuItem;

return [
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
