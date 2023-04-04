<?php

namespace Novius\LaravelNovaMenu\Observers;

use Illuminate\Support\Facades\Cache;
use Novius\LaravelNovaMenu\Models\MenuItem;

class ItemObserver
{
    public function saving(MenuItem $item)
    {
        $this->cleanModel($item);
    }

    public function created(MenuItem $item)
    {
        Cache::forget($item->menu->getTreeCacheName());
        Cache::forget(MenuItem::getDepthCacheName($item->id));

        if (config('nova-order-nestedset-field.cache_enabled', false)) {
            $item->clearOrderableCache();
        }
    }

    public function updated(MenuItem $item)
    {
        Cache::forget($item->menu->getTreeCacheName());
        Cache::forget(MenuItem::getDepthCacheName($item->id));

        if (config('nova-order-nestedset-field.cache_enabled', false)) {
            $item->clearOrderableCache();
        }
    }

    public function deleted(MenuItem $item)
    {
        Cache::forget($item->menu->getTreeCacheName());
        Cache::forget(MenuItem::getDepthCacheName($item->id));

        if (config('nova-order-nestedset-field.cache_enabled', false)) {
            $item->clearOrderableCache();
        }
    }

    protected function cleanModel(MenuItem $item)
    {
        if (request()->has('link_type')) {
            // Prevent multi-types : keep only the value on submitted type
            $attributes = MenuItem::linkTypesAttributes();
            if (array_key_exists(request()->get('link_type'), $attributes)) {
                $attrToClean = collect($attributes)->forget(request()->get('link_type'));
                foreach ($attrToClean as $attr) {
                    $item->{$attr} = null;
                }
            } else {
                foreach ($attributes as $attr) {
                    $item->{$attr} = null;
                }
            }

            if ((int) request()->get('link_type') === MenuItem::TYPE_EMPTY) {
                $item->{menuItem::linkTypesAttributes()[MenuItem::TYPE_EMPTY]} = 1;
            } else {
                $item->{menuItem::linkTypesAttributes()[MenuItem::TYPE_EMPTY]} = 0;
            }

            unset($item->link_type);
        }
    }
}
