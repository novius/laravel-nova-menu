<?php

namespace Novius\LaravelNovaMenu\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Kalnoy\Nestedset\NodeTrait;
use Novius\LaravelLinkable\Facades\Linkable;
use Novius\LaravelNovaOrderNestedsetField\Traits\Orderable;

/**
 * Novius\LaravelNovaMenu\Models\MenuItem
 *
 * @property int $id
 * @property string $name
 * @property int $menu_id
 * @property ?int $parent_id
 * @property int $left
 * @property int $right
 * @property string $external_link
 * @property string internal_link
 * @property string $html_classes
 * @property string $html
 * @property bool $is_empty_link
 * @property bool $target_blank
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @method static Builder|MenuItem newModelQuery()
 * @method static Builder|MenuItem newQuery()
 * @method static Builder|MenuItem query()
 *
 * @mixin \Eloquent
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function getLftName(): string
    {
        return 'left';
    }

    public function getRgtName(): string
    {
        return 'right';
    }

    public function getParentIdName(): string
    {
        return 'parent_id';
    }

    protected function getScopeAttributes(): array
    {
        return [
            'menu_id',
        ];
    }

    public function setParentIdAttribute($value): void
    {
        $request = request();
        if ($request && $request->has('viaResourceId')) {
            // Prevent bug with Laravel Nova because `menu_id` is not defined here
            $this->menu_id = (int) $request->post('viaResourceId');
        }

        $this->nodeTraitSetParentIdAttribute($value);
    }

    /**
     * Creates an href for the menu item according to its type.
     */
    public function href(): string
    {
        $href = '#';

        if (! empty($this->is_empty_link)) {
            return $href;
        }

        if (! empty($this->html)) {
            return $href;
        }

        if (! empty($this->external_link)) {
            $href = $this->external_link;
        }

        if (! empty($this->internal_link)) {
            $href = Linkable::getLink($this->internal_link) ?? $href;
        }

        return $href;
    }

    public function linkType(): ?int
    {
        if (! empty($this->html)) {
            return self::TYPE_HTML;
        }

        if (! empty($this->internal_link)) {
            return self::TYPE_INTERNAL_LINK;
        }

        if (! empty($this->external_link)) {
            return self::TYPE_EXTERNAL_LINK;
        }

        if (! empty($this->is_empty_link)) {
            return self::TYPE_EMPTY;
        }

        return null;
    }

    public static function getDepthCacheName(int $itemID): string
    {
        return 'laravel-nova-menu.item.depth.'.$itemID;
    }

    public static function linkTypesLabels(): array
    {
        return [
            self::TYPE_INTERNAL_LINK => trans('laravel-nova-menu::menu.internal_link'),
            self::TYPE_EXTERNAL_LINK => trans('laravel-nova-menu::menu.external_link'),
            self::TYPE_HTML => trans('laravel-nova-menu::menu.html'),
            self::TYPE_EMPTY => trans('laravel-nova-menu::menu.empty_link'),
        ];
    }

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
