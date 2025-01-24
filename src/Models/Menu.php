<?php

namespace Novius\LaravelNovaMenu\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property int id
 * @property string name
 * @property string slug
 * @property ?string locale
 * @property ?int locale_parent_id
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @method static Builder|Menu newModelQuery()
 * @method static Builder|Menu newQuery()
 * @method static Builder|Menu query()
 *
 * @mixin Eloquent
 */
class Menu extends Model
{
    use HasSlug;

    protected $table = 'nova_menus';

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function parent(): HasOne
    {
        return $this->hasOne(static::class, 'id', 'locale_parent_id');
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getTreeCacheName(): string
    {
        return 'laravel-nova-menu.front.tree.'.$this->id;
    }
}
