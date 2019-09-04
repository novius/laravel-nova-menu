<?php

namespace Novius\LaravelNovaMenu\Observers;

use Illuminate\Support\Facades\Cache;
use Novius\LaravelNovaMenu\Models\Item;

class ItemObserver
{
    /**
     * @param Item $item
     */
    public function created(Item $item)
    {
        Cache::forget($item->menu->getTreeCacheName());
        Cache::forget(Item::getDepthCacheName($item->id));

        if (config('nova-order-nestedset-field.cache_enabled', false)) {
            $item->clearOrderableCache();
        }
    }

    /**
     * @param Item $item
     */
    public function updated(Item $item)
    {
        Cache::forget($item->menu->getTreeCacheName());
        Cache::forget(Item::getDepthCacheName($item->id));

        if (config('nova-order-nestedset-field.cache_enabled', false)) {
            $item->clearOrderableCache();
        }
    }

    /**
     * @param Item $item
     */
    public function deleted(Item $item)
    {
        Cache::forget($item->menu->getTreeCacheName());
        Cache::forget(Item::getDepthCacheName($item->id));

        if (config('nova-order-nestedset-field.cache_enabled', false)) {
            $item->clearOrderableCache();
        }
    }
}
