<?php

namespace Novius\LaravelNovaMenu\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Novius\LaravelNovaMenu\Models\Menu;
use Novius\LaravelNovaMenu\Models\MenuItem;

class MenuHelper
{
    /**
     * Returns a sorted array of linkable items and routes.
     * This collection is used in the back office to feed a select list.
     * This select list is intended for adding new menu items.
     *
     * @return array
     */
    public static function links(): array
    {
        $links = [];
        $linkableObjects = config('laravel-nova-menu.linkable_objects', []);
        foreach ($linkableObjects as $class => $translation) {
            $items = $class::linkableItems(trans($translation));
            $links = array_merge($links, $items);
        }

        $linkableRoutes = config('laravel-nova-menu.linkable_routes', []);
        foreach ($linkableRoutes as $routeName => $translation) {
            if (Route::has($routeName)) {
                $links = array_merge(
                    $links,
                    static::linkableRoute($routeName, trans($translation))
                );
            }
        }

        asort($links);

        return $links;
    }

    /**
     * Returns an array of well-formed linkable route.
     * Check out the config file or the readme file to know more about linkable routes.
     *
     * @overridable
     * @param $routeName
     * @param $translation
     * @return array
     */
    protected static function linkableRoute(string $routeName, string $translation): array
    {
        return [
            'linkable_route:'.$routeName => $translation,
        ];
    }

    /**
     * @param $slug
     * @return string
     */
    public static function displayMenu($slug): string
    {
        $menu = Menu::query()
            ->where('slug', (string) $slug)
            ->first();

        if (empty($menu)) {
            Log::info(sprintf('Menu with slug %s not found : unable to display.', (string) $slug));

            return '';
        }

        $tree = Cache::rememberForever($menu->getTreeCacheName(), function () use ($menu) {
            $items = MenuItem::scoped(['menu_id' => $menu->id])
                ->withDepth()
                ->defaultOrder()
                ->get()
                ->toTree();

            return static::getTree($items);
        });

        return view('laravel-nova-menu::front/menu', [
            'menu' => $menu,
            'tree' => $tree,
        ]);
    }

    /**
     * @param Collection $items
     * @return array
     */
    protected static function getTree(Collection $items): array
    {
        $tree = [];
        foreach ($items as $key => $menuItem) {
            $tree[] = [
                'infos' => [
                    'name' => $menuItem->name,
                    'href' => $menuItem->href(),
                    'depth' => $menuItem->depth,
                    'htmlClasses' => $menuItem->html_classes,
                ],
                'children' => ($menuItem->children->count() ? static::getTree($menuItem->children) : []),
            ];
        }

        return $tree;
    }
}
