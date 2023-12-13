<?php

namespace Novius\LaravelNovaMenu\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Novius\LaravelNovaMenu\Models\Menu;
use Novius\LaravelNovaMenu\Models\MenuItem;
use Novius\LaravelNovaMenu\Traits\Linkable;

class MenuHelper
{
    /**
     * Returns a sorted array of linkable items and routes.
     * This collection is used in the back office to feed a select list.
     * This select list is intended for adding new menu items.
     */
    public static function links(): array
    {
        $links = [];
        $linkableObjects = config('laravel-nova-menu.linkable_objects', []);
        foreach ($linkableObjects as $class => $translation) {
            /** @var Linkable $class */
            $links[] = $class::linkableItems(trans($translation));
        }

        $linkableRoutes = config('laravel-nova-menu.linkable_routes', []);
        foreach ($linkableRoutes as $routeName => $translation) {
            if (Route::has($routeName)) {
                $links[] = static::linkableRoute($routeName, trans($translation));
            }
        }

        $links = array_merge(...$links);
        asort($links);

        return $links;
    }

    /**
     * Returns an array of well-formed linkable route.
     * Check out the config file or the readme file to know more about linkable routes.
     *
     * @overridable
     */
    protected static function linkableRoute(string $routeName, string $translation): array
    {
        return [
            'linkable_route:'.$routeName => $translation,
        ];
    }

    /**
     * Display menu from its slug
     * Fallback to menu with current application locale
     *
     * You can append '|no-locale-fallback' to slug if you want to skip the default fallback
     */
    public static function displayMenu(Menu|string $slug, string $view = null, bool $localeFallback = true): string
    {
        if ($slug instanceof Menu) {
            $menu = $slug;
            $slug = $menu->slug;
        } else {
            $args = explode('|', $slug, 2);
            if (isset($args[1]) && $args[1] === 'no-locale-fallback') {
                $localeFallback = false;
            }
            $slug = $args[0];

            $menu = Menu::query()
                ->where('slug', $slug)
                ->first();
        }

        $locale = app()->getLocale();

        if ($localeFallback && ! empty($menu) && $menu->locale !== $locale) {
            if (empty($menu->locale_parent_id)) {
                $menu = Menu::query()
                    ->where('locale_parent_id', $menu->id)
                    ->where('locale', $locale)
                    ->first();
            } else {
                $menu = Menu::query()
                    ->where(function ($query) use ($menu, $locale) {
                        $query->where('id', $menu->locale_parent_id)
                            ->where('locale', $locale);
                    })
                    ->orWhere(function ($query) use ($menu, $locale) {
                        $query->where('locale_parent_id', $menu->locale_parent_id)
                            ->where('locale', $locale);
                    })
                    ->first();
            }
        }

        if (empty($menu)) {
            Log::info(sprintf('Menu with slug %s and locale %s not found : unable to display.', $slug, app()->getLocale()));

            return '';
        }

        $tree = Cache::rememberForever($menu->getTreeCacheName(), static function () use ($menu) {
            $items = MenuItem::scoped(['menu_id' => $menu->id])
                ->withDepth()
                ->defaultOrder()
                ->get()
                ->toTree();

            return static::getTree($items);
        });

        return (string) view($view ?? 'laravel-nova-menu::front/menu', [
            'menu' => $menu,
            'tree' => app()->get('laravel-nova-menu')->tree($menu, $tree),
        ]);
    }

    protected static function getTree(Collection $items): array
    {
        $tree = [];
        foreach ($items as $menuItem) {
            $tree[] = [
                'infos' => [
                    'name' => $menuItem->name,
                    'href' => $menuItem->href(),
                    'depth' => $menuItem->depth,
                    'htmlClasses' => $menuItem->html_classes,
                    'targetBlank' => (bool) $menuItem->target_blank,
                    'html' => $menuItem->html,
                ],
                'children' => ($menuItem->children->count() ? static::getTree($menuItem->children) : []),
            ];
        }

        return $tree;
    }
}
