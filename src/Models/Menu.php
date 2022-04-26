<?php

namespace Novius\LaravelNovaMenu\Models;

use Illuminate\Database\Eloquent\Model;
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
 */
class Menu extends Model
{
    use HasSlug;

    protected $table = 'nova_menus';

    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public $timestamps = true;

    public function items()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function parent()
    {
        return $this->hasOne(static::class, 'id', 'locale_parent_id');
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getTreeCacheName()
    {
        return 'laravel-nova-menu.front.tree.'.$this->id;
    }
}
