<?php

namespace Novius\LaravelNovaMenu\Filters;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Filters\Filter;

class Locale extends Filter
{
    /**
     * @var string
     */
    public $name = 'Locale';

    public function __construct()
    {
        $this->name = trans('laravel-nova-menu::menu.locale');
    }

    /**
     * Apply the filter to the given query.
     */
    public function apply(Request $request, Builder $query, mixed $value): Builder
    {
        return $query->where('locale', $value);
    }

    /**
     * Get the filter's available options.
     */
    public function options(Request $request): array
    {
        return array_flip(Arr::sort(config('laravel-nova-menu.locales', ['en' => 'English'])));
    }
}
