<?php

namespace Novius\LaravelNovaMenu\Lenses;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;

class MenuItems extends Lens
{
    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public static function query(LensRequest $request, $query): mixed
    {
        return $request->withOrdering($request->withFilters(
            $query
        ));
    }

    /**
     * Get the fields available to the lens.
     */
    public function fields(Request $request): array
    {
        return [
            ID::make('ID', 'id')->sortable(),
        ];
    }

    /**
     * Get the filters available for the lens.
     */
    public function filters(Request $request): array
    {
        return [];
    }

    /**
     * Get the URI key for the lens.
     */
    public function uriKey(): string
    {
        return 'menu-items';
    }
}
