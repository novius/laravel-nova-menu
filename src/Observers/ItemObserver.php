<?php

namespace Novius\LaravelNovaMenu\Observers;

use Illuminate\Support\Facades\Cache;
use Novius\LaravelNovaMenu\Models\MenuItem;

class ItemObserver
{
    /**
     * @param MenuItem $item
     */
    public function created(MenuItem $item)
    {
        Cache::forget($item->menu->getTreeCacheName());
        Cache::forget(MenuItem::getDepthCacheName($item->id));

        if (config('nova-order-nestedset-field.cache_enabled', false)) {
            $item->clearOrderableCache();
        }
    }

    /**
     * @param MenuItem $item
     */
    public function updated(MenuItem $item)
    {
        Cache::forget($item->menu->getTreeCacheName());
        Cache::forget(MenuItem::getDepthCacheName($item->id));

        if (config('nova-order-nestedset-field.cache_enabled', false)) {
            $item->clearOrderableCache();
        }
    }

    /**
     * @param MenuItem $item
     */
    public function deleted(MenuItem $item)
    {
        Cache::forget($item->menu->getTreeCacheName());
        Cache::forget(MenuItem::getDepthCacheName($item->id));

        if (config('nova-order-nestedset-field.cache_enabled', false)) {
            $item->clearOrderableCache();
        }
    }
}
