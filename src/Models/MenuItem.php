<?php

namespace Novius\LaravelNovaMenu\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;
use Novius\LaravelNovaOrderNestedsetField\Traits\Orderable;

/**
 * Class MenuItem
 * @package Novius\LaravelNovaMenu\Models
 */
class MenuItem extends Model
{
    use NodeTrait {
        setParentIdAttribute as public nodeTraitSetParentIdAttribute;
    }
    use Orderable;

    public const TYPE_INTERNAL_LINK = 1;
    public const TYPE_EXTERNAL_LINK = 2;
    public const TYPE_HTML = 3;
    public const TYPE_EMPTY = 4;

    protected $table = 'nova_menu_items';

    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public $timestamps = true;

    protected $casts = [
        'target_blank' => 'boolean',
        'is_empty_link' => 'boolean',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function getLftName()
    {
        return 'left';
    }

    public function getRgtName()
    {
        return 'right';
    }

    public function getParentIdName()
    {
        return 'parent_id';
    }

    protected function getScopeAttributes()
    {
        return [
            'menu_id',
        ];
    }

    public function setParentIdAttribute($value)
    {
        if (request()->has('viaResourceId')) {
            // Prevent bug with Laravel Nova because `menu_id` is not defined here
            $this->menu_id = (int) request()->post('viaResourceId');
        }

        return $this->nodeTraitSetParentIdAttribute($value);
    }

    /**
     * Creates an href for the menu item according to its type.
     *
     * @return string
     */
    public function href(): string
    {
        $href = '#';

        if (!empty($this->is_empty_link))
            return $href;

        if (!empty($this->html)) {
            return $href;
        }

        if (!empty($this->external_link)) {
            $href = $this->external_link;
        }

        if (!empty($this->internal_link)) {
            $infos = explode(':', $this->internal_link);
            if (Str::startsWith($this->internal_link, 'linkable_route')) {
                if (Route::has($infos[1])) {
                    $href = route($infos[1]);
                }
            } elseif (Str::startsWith($this->internal_link, 'linkable_object')) {
                $className = $infos[1];
                $id = $infos[2];
                $item = $className::find($id);
                if (!empty($item->id)) {
                    $href = $item->linkableUrl();
                }
            }
        }

        return $href;
    }

    /**
     * @return int|null
     */
    public function linkType(): ?int
    {
        if (!empty($this->html)) {
            return self::TYPE_HTML;
        }

        if (!empty($this->internal_link)) {
            return self::TYPE_INTERNAL_LINK;
        }

        if (!empty($this->external_link)) {
            return self::TYPE_EXTERNAL_LINK;
        }

        if (!empty($this->is_empty_link)) {
            return self::TYPE_EMPTY;
        }

        return null;
    }

    public static function getDepthCacheName(int $itemID): string
    {
        return 'laravel-nova-menu.item.depth.'.$itemID;
    }

    /**
     * @return array
     */
    public static function linkTypesLabels(): array
    {
        return [
            self::TYPE_INTERNAL_LINK => trans('laravel-nova-menu::menu.internal_link'),
            self::TYPE_EXTERNAL_LINK => trans('laravel-nova-menu::menu.external_link'),
            self::TYPE_HTML => trans('laravel-nova-menu::menu.html'),
            self::TYPE_EMPTY => trans('laravel-nova-menu::menu.empty_link'),
        ];
    }

    /**
     * @return array
     */
    public static function linkTypesAttributes(): array
    {
        return [
            self::TYPE_INTERNAL_LINK => 'internal_link',
            self::TYPE_EXTERNAL_LINK => 'external_link',
            self::TYPE_HTML => 'html',
            self::TYPE_EMPTY => 'is_empty_link',
        ];
    }
}
