<?php

namespace Novius\LaravelNovaMenu\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed name
 */
class Menu extends Model
{
    use Sluggable;

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

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'slug_or_name',
            ],
        ];
    }

    public function getSlugOrNameAttribute()
    {
        if ($this->slug != '') {
            return $this->slug;
        }

        return $this->name;
    }

    public function getTreeCacheName()
    {
        return 'laravel-nova-menu.front.tree.'.$this->id;
    }
}
