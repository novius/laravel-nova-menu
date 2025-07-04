<?php

namespace Novius\LaravelNovaMenu\Resources;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Nova\Exceptions\HelperNotSupported;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Novius\LaravelLinkable\Nova\Fields\Linkable;
use Novius\LaravelNovaMenu\Lenses\MenuItems;
use Novius\LaravelNovaMenu\Models\MenuItem as MenuItemModel;
use Novius\LaravelNovaOrderNestedsetField\OrderNestedsetField;

/**
 * @extends Resource<MenuItemModel>
 */
class MenuItem extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<MenuItemModel>
     */
    public static string $model = MenuItemModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

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
     */
    public static function label(): string
    {
        return trans('laravel-nova-menu::menu.menu_items');
    }

    /**
     * Get the displayable singular label of the resource.
     */
    public static function singularLabel(): string
    {
        return trans('laravel-nova-menu::menu.menu_item');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @throws HelperNotSupported
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(),

            Text::make(trans('laravel-nova-menu::menu.name'), function () {
                $resource = $this->resource;
                if (empty($resource->id)) {
                    return '';
                }

                $depth = Cache::rememberForever(MenuItemModel::getDepthCacheName($resource->id), static function () use ($resource) {
                    $result = MenuItemModel::scoped([
                        'menu_id' => $resource->menu_id,
                    ])
                        ->withDepth()
                        ->find($resource->id);

                    return $result->depth ?? '';
                });

                $nbspStr = str_repeat('&nbsp;', ($depth * 7));

                return $nbspStr.$resource->name;
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

            Select::make(trans('laravel-nova-menu::menu.link_type'), 'link_type', static function ($value, MenuItemModel $model) {
                return $model->linkType();
            })
                ->options(MenuItemModel::linkTypesLabels())
                ->withMeta($this->metasDefaultLinkType())
                ->displayUsingLabels()
                ->rules('required')
                ->onlyOnForms(),

            Text::make(trans('laravel-nova-menu::menu.link_type'), function () {
                return $this->linkTypeLabel();
            })->exceptOnForms(),

            Linkable::make(trans('laravel-nova-menu::menu.internal_link'), 'internal_link')
                ->hideFromIndex()
                ->hideFromDetail()
                ->dependsOn(
                    ['link_type'],
                    function (Select $field, NovaRequest $request, FormData $formData) {
                        if ($formData->integer('link_type') !== MenuItemModel::TYPE_INTERNAL_LINK) {
                            $field->hide();
                        } else {
                            $field->required();
                        }
                    }
                ),

            Text::make(trans('laravel-nova-menu::menu.external_link'), 'external_link')
                ->help(trans('laravel-nova-menu::menu.must_start_with_http'))
                ->nullable()
                ->rules('max:191', 'required_if:link_type,'.MenuItemModel::TYPE_EXTERNAL_LINK, function ($attribute, $value, $fail) {
                    if (! empty($value) && ! Validator::make([$attribute => $value], [$attribute => 'url'])->passes()) {
                        $fail(trans('laravel-nova-menu::errors.bad_format_external_link'));
                    }
                })
                ->hideFromDetail()
                ->dependsOn(
                    ['link_type'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData->integer('link_type') !== MenuItemModel::TYPE_EXTERNAL_LINK) {
                            $field->hide();
                        }
                    }
                ),

            Code::make(trans('laravel-nova-menu::menu.html'), 'html')
                ->help(trans('laravel-nova-menu::menu.help_code'))
                ->rules('required_if:link_type,'.MenuItemModel::TYPE_HTML, 'max:'.config('laravel-nova-menu.menu_item_html_max_size'))
                ->hideFromDetail(function ($ressource, $fields) {
                    return empty($fields->html);
                })
                ->hideFromIndex()
                ->dependsOn(
                    ['link_type'],
                    function (Code $field, NovaRequest $request, FormData $formData) {
                        if ($formData->integer('link_type') !== MenuItemModel::TYPE_HTML) {
                            $field->hide();
                        }
                    }
                ),

            Text::make(trans('laravel-nova-menu::menu.html_classes'), 'html_classes')
                ->rules('nullable', 'max:255', 'regex:/^[0-9a-z\- _]+$/i')
                ->help(trans('laravel-nova-menu::menu.html_classes_help'))
                ->hideFromDetail(function ($ressource, $fields) {
                    return empty($fields->html_classes);
                })
                ->hideFromIndex(),

            Text::make(trans('laravel-nova-menu::menu.url'), function () {
                $url = $this->resource->href();

                return sprintf('<a class="link-default" href="%s" title="%s" target="_blank">%s</a>', $url, $url, Str::limit($url, 50));
            })->asHtml()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->hideFromDetail(function ($ressource, $fields) {
                    return ! empty($fields->html);
                }),

            Boolean::make(trans('laravel-nova-menu::menu.target_blank'), 'target_blank')
                ->hideFromIndex()
                ->dependsOn(
                    ['link_type'],
                    function (Boolean $field, NovaRequest $request, FormData $formData) {
                        if ($formData->integer('link_type') === MenuItemModel::TYPE_EMPTY) {
                            $field->hide();
                        }
                    }
                ),

            OrderNestedsetField::make(trans('laravel-nova-menu::menu.order'), 'order'),
        ];
    }

    protected function getParents(NovaRequest $request): Collection
    {
        $resource = $this->model();

        /** @phpstan-ignore method.notFound */
        $query = static::$model::query()
            ->select(['name', 'id', 'menu_id', 'parent_id'])
            ->where('menu_id', $request->viaResourceId)
            ->where('id', '<>', $resource?->id)
            ->ordered();

        return $query->get()->pluck('name', 'id');
    }

    /**
     * Metas values for type link field (default value)
     */
    protected function metasDefaultLinkType(): array
    {
        $resource = $this->model();
        if ($resource?->id === null) {
            return [];
        }

        return [
            'value' => $resource->linkType(),
        ];
    }

    /**
     * Get label of current resource link type
     */
    protected function linkTypeLabel(): string
    {
        $resource = $this->model();
        if ($resource?->id === null) {
            return '';
        }

        return MenuItemModel::linkTypesLabels()[$resource->linkType()] ?? '';
    }

    protected static function applyOrderings(Builder $query, array $orderings): Builder
    {
        $query->orderBy('left');

        return $query;
    }

    /**
     * Get the lenses available for the resource.
     */
    public function lenses(Request $request): array
    {
        return [
            new MenuItems,
        ];
    }
}
