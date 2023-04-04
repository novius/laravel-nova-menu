<?php

namespace Novius\LaravelNovaMenu\Traits;

/**
 * This trait provides models with some basic overridable behaviour intended for:
 *
 *  - Storing ids and class names of related linkable items (Pages, forms...).
 *  - Storing urls of linkable items.
 *
 * This is used for building the menu links in front office.
 *
 * Trait Linkable
 */
trait Linkable
{
    abstract public function linkableUrl(): string;

    abstract public function linkableTitle(): string;

    public static function linkableItems(string $prefix = ''): array
    {
        return static::all()->mapWithKeys(function ($item) use ($prefix) {
            $objectId = 'linkable_object:'.get_class($item).':'.$item->linkableId();
            $title = static::linkableLabel($item->linkableTitle(), $prefix);

            return [
                $objectId => $title,
            ];
        })->toArray();
    }

    /**
     * Returns the id of the linkable item.
     *
     * @overridable
     */
    protected function linkableId(): string
    {
        $primaryKey = $this->getKeyName();

        return $this->{$primaryKey};
    }

    /**
     * Returns a label, optionally prefixed.
     */
    protected static function linkableLabel(string $name, string $prefix = ''): string
    {
        $label = $name;
        if ($prefix) {
            $label = implode(' - ', [$prefix, $name]);
        }

        return $label;
    }
}
