<?php

namespace Novius\LaravelNovaMenu\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Novius\LaravelNovaMenu\Models\Menu;
use Novius\LaravelNovaMenu\Models\MenuItem;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class MenuHelper
{
    /**
     * Display menu from its slug
     * Fallback to menu with current application locale
     *
     * You can append '|no-locale-fallback' to slug if you want to skip the default fallback
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function displayMenu(Menu|string $slug_or_menu, ?string $view = null, bool $localeFallback = true): string
    {
        if ($slug_or_menu instanceof Menu) {
            $menu = $slug_or_menu;
            $slug = $menu->slug;
        } else {
            $args = explode('|', $slug_or_menu, 2);
            if (isset($args[1]) && $args[1] === 'no-locale-fallback') {
                $localeFallback = false;
            }
            $slug = $args[0];

            $menu = Menu::query()
                ->where('slug', $slug)
                ->first();
        }

        $locale = app()->getLocale();

        if ($localeFallback && $menu !== null && $menu->locale !== $locale) {
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

        if ($menu === null) {
            Log::info(sprintf('Menu with slug %s and locale %s not found : unable to display.', $slug, app()->getLocale()));

            return '';
        }

        $tree = Cache::rememberForever($menu->getTreeCacheName(), static function () use ($menu) {
            return app()->get('laravel-nova-menu')?->buildTree($menu);
        });

        return (string) view($view ?? 'laravel-nova-menu::front/menu', [
            'menu' => $menu,
            'tree' => app()->get('laravel-nova-menu')?->tree($menu, $tree),
        ]);
    }

    public static function buildTree(Menu $menu): array
    {
        $items = MenuItem::query()
            ->scoped(['menu_id' => $menu->id])
            ->withDepth()
            ->defaultOrder()
            ->get()
            ->toTree();

        return static::getTree($items);
    }

    protected static function getTree(Collection $items): array
    {
        $tree = [];
        /** @var MenuItem $menuItem */
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
