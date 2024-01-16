<?php

namespace Novius\LaravelNovaMenu\Resources;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Novius\LaravelNovaMenu\Actions\TranslateMenu;
use Novius\LaravelNovaMenu\Filters\Locale;

class Menu extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Novius\LaravelNovaMenu\Models\Menu::class;

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
    public static $search = ['name'];

    public static $displayInNavigation = true;

    /**
     * Get the displayable label of the resource.
     */
    public static function label(): string
    {
        return trans('laravel-nova-menu::menu.menus_label');
    }

    /**
     * Get the displayable singular label of the resource.
     */
    public static function singularLabel(): string
    {
        return trans('laravel-nova-menu::menu.menu_singular_label');
    }

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make(trans('laravel-nova-menu::menu.menu_name'), 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Slug::make(trans('laravel-nova-menu::menu.slug'), 'slug')
                ->from('name')
                ->rules('required', 'regex:/^[0-9a-z\-_]+$/i'),

            Select::make(trans('laravel-nova-menu::menu.locale'), 'locale')
                ->options(config('laravel-nova-menu.locales', ['en' => 'English']))
                ->rules('in:'.implode(',', array_keys(config('laravel-nova-menu.locales', ['en' => 'English'])))),

            HasMany::make(trans('laravel-nova-menu::menu.menu_items'), 'items', MenuItem::class),
        ];
    }

    /**
     * Get the cards available for the request.
     */
    public function cards(Request $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     */
    public function filters(Request $request): array
    {
        $locales = config('laravel-nova-menu.locales', ['en' => 'English']);

        return (is_array($locales) && count($locales) > 1) ? [new Locale()] : [];
    }

    /**
     * Get the lenses available for the resource.
     */
    public function lenses(Request $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     */
    public function actions(Request $request): array
    {
        $locales = config('laravel-nova-menu.locales', ['en' => 'English']);
        if (count($locales) <= 1) {
            return [];
        }

        return [
            (new TranslateMenu())->onlyInline(),
        ];
    }
}
