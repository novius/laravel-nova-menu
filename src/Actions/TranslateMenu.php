<?php

namespace Novius\LaravelNovaMenu\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelNovaMenu\Models\Menu;

class TranslateMenu extends Action
{
    /**
     * Perform the action on the given models.
     */
    public function handle(ActionFields $fields, Collection $models): ActionResponse|Action
    {
        if ($models->count() > 1) {
            return Action::danger(trans('laravel-nova-menu::errors.action_only_available_for_single_menu'));
        }

        /** @var Menu $menuToTranslate */
        $menuToTranslate = $models->first();
        $locale = $fields->get('locale');
        if ($menuToTranslate->locale === $locale) {
            return Action::danger(trans('laravel-nova-menu::errors.menu_already_translated'));
        }

        if (! empty($menuToTranslate->locale_parent_id)) {
            $menuToTranslate = $menuToTranslate->parent;
            if (empty($menuToTranslate)) {
                return Action::danger(trans('laravel-nova-menu::errors.error_during_menu_translation'));
            }
        }

        $otherMenuAlreadyExists = Menu::query()
            ->where('locale', $locale)
            ->where('locale_parent_id', $menuToTranslate->id)
            ->exists();

        if ($otherMenuAlreadyExists) {
            return Action::danger(trans('laravel-nova-menu::errors.menu_already_translated'));
        }

        $translatedMenu = new Menu;
        $translatedMenu->name = $fields->get('name');
        $translatedMenu->locale = $locale;
        $translatedMenu->locale_parent_id = $menuToTranslate->id;

        if (! $translatedMenu->save()) {
            return Action::danger(trans('laravel-nova-menu::errors.error_during_menu_translation'));
        }

        return Action::message(trans('laravel-nova-menu::menu.successfully_translated_menu'));
    }

    /**
     * Get the fields available on the action.
     */
    public function fields(NovaRequest $request): array
    {
        $locales = config('laravel-nova-menu.locales', ['en' => 'English']);

        return [
            Text::make(trans('laravel-nova-menu::menu.menu_name'), 'name')
                ->required()
                ->rules('required', 'max:255'),

            Select::make(trans('laravel-nova-menu::menu.locale'), 'locale')
                ->options($locales)
                ->displayUsingLabels()
                ->rules('required', 'in:'.implode(',', array_keys($locales))),
        ];
    }
}
