<?php

namespace Novius\LaravelNovaMenu\Resources;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use KossShtukert\LaravelNovaSelect2\Select2;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelNovaMenu\Helpers\MenuHelper;
use Novius\LaravelNovaMenu\Lenses\MenuItems;
use Novius\LaravelNovaMenu\Tools\BackToMenu;
use Novius\LaravelNovaOrderNestedsetField\OrderNestedsetField;

class MenuItem extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Novius\LaravelNovaMenu\Models\MenuItem::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * Indicates if the resoruce should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * The number of resources to show per page via relationships.
     *
     * @var int
     */
    public static $perPageViaRelationship = 50;

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return trans('laravel-nova-menu::menu.menu_items');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return trans('laravel-nova-menu::menu.menu_item');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(),

            Text::make(trans('laravel-nova-menu::menu.name'), function () use ($request) {
                $resource = $this->resource;

                $depth = Cache::rememberForever(\Novius\LaravelNovaMenu\Models\MenuItem::getDepthCacheName($resource->id), function () use ($resource) {
                    $result = \Novius\LaravelNovaMenu\Models\MenuItem::scoped([
                        'menu_id' => $resource->menu_id,
                    ])->withDepth()->find($resource->id);

                    if (empty($result)) {
                        return '';
                    }

                    return $result->depth;
                });

                $nbspStr = '';
                for ($i = 0; $i < ($depth * 7); $i++) {
                    $nbspStr .= '&nbsp;';
                }

                return $nbspStr.$this->name;
            })->asHtml()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->hideFromDetail(),

            Text::make(trans('laravel-nova-menu::menu.name'), 'name')
                ->hideFromIndex()
                ->rules('required', 'max:191'),

            Select::make(trans('laravel-nova-menu::menu.parent_item'), 'parent_id')
                ->options($this->getParents($request))
                ->displayUsingLabels()
                ->hideWhenUpdating()
                ->hideFromDetail()
                ->hideFromIndex(),

            Text::make(trans('laravel-nova-menu::menu.external_link'), 'external_link')
                ->help(trans('laravel-nova-menu::menu.must_start_with_http'))
                ->nullable()
                ->rules('max:191', 'required_without:internal_link', function ($attribute, $value, $fail) {
                    if (!empty($value) && !Validator::make([$attribute => $value], [$attribute => 'url'])->passes()) {
                        return $fail(trans('laravel-nova-menu::errors.bad_format_external_link'));
                    }
                })
                ->hideFromIndex()
                ->hideFromDetail(),

            Select2::make(trans('laravel-nova-menu::menu.internal_link'), 'internal_link')
                ->options(MenuHelper::links())
                ->rules('nullable', 'required_without:external_link', 'in:'.implode(',', array_keys(MenuHelper::links())))
                ->configuration([
                    'width' => '100%',
                    'allowClear' => true,
                    'multiple' => false,
                    'minimumResultsForSearch' => config('laravel-nova-menu.select2_minimumResultsForSearch', 5),
                ])
                ->hideFromIndex()
                ->hideFromDetail(),

            Text::make(trans('laravel-nova-menu::menu.html_classes'), 'html_classes')
                ->rules('nullable', 'max:255', 'regex:/^[0-9a-z\- _]+$/i')
                ->help(trans('laravel-nova-menu::menu.html_classes_help'))
                ->hideFromIndex(),

            Text::make(trans('laravel-nova-menu::menu.url'), function () use ($request) {
                $url = $this->resource->href();

                return sprintf('<a href="%s" title="%s" target="_blank">%s</a>', $url, $url, Str::limit($url, 50));
            })->asHtml()
                ->hideWhenCreating()
                ->hideWhenUpdating(),

            OrderNestedsetField::make(trans('laravel-nova-menu::menu.order'), 'order'),

            BackToMenu::make()->test()->withMeta([
                'menu_id' => $this->resource->menu_id,
                'back_trans' => trans('laravel-nova-menu::menu.back_to_menu'),
            ]),
        ];
    }

    protected function getParents(NovaRequest $request)
    {
        $resource = $this->model();

        $query = static::$model::select('name', 'id', 'menu_id', 'parent_id')
            ->where('menu_id', $request->viaResourceId)
            ->where('id', '<>', $resource->id)
            ->ordered();

        return $query->get()->pluck('name', 'id');
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $orderings
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyOrderings($query, array $orderings)
    {
        return $query->orderBy('left', 'asc');
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [
            new MenuItems,
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
