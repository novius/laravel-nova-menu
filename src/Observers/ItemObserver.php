<?php

namespace Novius\LaravelNovaMenu\Observers;

use Illuminate\Support\Facades\Cache;
use Novius\LaravelNovaMenu\Models\MenuItem;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ItemObserver
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function saving(MenuItem $item): void
    {
        $this->cleanModel($item);
    }

    public function created(MenuItem $item): void
    {
        Cache::forget($item->menu->getTreeCacheName());
        Cache::forget(MenuItem::getDepthCacheName($item->id));

        if (config('nova-order-nestedset-field.cache_enabled', false)) {
            $item->clearOrderableCache();
        }
    }

    public function updated(MenuItem $item): void
    {
        Cache::forget($item->menu->getTreeCacheName());
        Cache::forget(MenuItem::getDepthCacheName($item->id));

        if (config('nova-order-nestedset-field.cache_enabled', false)) {
            $item->clearOrderableCache();
        }
    }

    public function deleted(MenuItem $item): void
    {
        Cache::forget($item->menu->getTreeCacheName());
        Cache::forget(MenuItem::getDepthCacheName($item->id));

        if (config('nova-order-nestedset-field.cache_enabled', false)) {
            $item->clearOrderableCache();
        }
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    protected function cleanModel(MenuItem $item): void
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
