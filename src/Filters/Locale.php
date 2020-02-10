<?php

namespace Novius\LaravelNovaMenu\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class Locale extends Filter
{
    public $name = 'Locale';

    public function __construct()
    {
        $this->name = trans('laravel-nova-menu::menu.locale');
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->where('locale', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return array_flip(config('laravel-nova-menu.locales', ['en' => 'English']));
    }
}
