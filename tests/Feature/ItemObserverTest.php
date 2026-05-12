<?php

use Novius\LaravelNovaMenu\Models\Menu;
use Novius\LaravelNovaMenu\Models\MenuItem;

beforeEach(function () {
    $this->menu = createMenu();
});

test('create external link test', function () {
    request()->merge([
        'link_type' => MenuItem::TYPE_EXTERNAL_LINK,
    ]);
    $linkValue = 'https://www.novius.fr';

    $link = new MenuItem;
    $link->name = 'Test external';
    $link->menu_id = $this->menu->id;
    $link->external_link = $linkValue;
    $link->internal_link = 'should_be_null_after_saved';
    $link->html = 'should_be_null_after_saved';
    $link->is_empty_link = 1;
    $link->save();

    expect($link->internal_link)->toBeNull()
        ->and($link->html)->toBeNull()
        ->and($link->is_empty_link)->toBeFalse()
        ->and($link->external_link)->toBe($linkValue);
});

test('create internal link test', function () {
    request()->merge([
        'link_type' => MenuItem::TYPE_INTERNAL_LINK,
    ]);
    $linkValue = 'linkable_route:contact';

    $link = new MenuItem;
    $link->name = 'Test internal';
    $link->menu_id = $this->menu->id;
    $link->external_link = 'should_be_null_after_saved';
    $link->is_empty_link = true;
    $link->html = 'should_be_null_after_saved';
    $link->internal_link = $linkValue;
    $link->save();

    expect($link->external_link)->toBeNull()
        ->and($link->html)->toBeNull()
        ->and($link->is_empty_link)->toBeFalse()
        ->and($link->internal_link)->toBe($linkValue);
});

test('create empty link test', function () {
    request()->merge([
        'link_type' => MenuItem::TYPE_EMPTY,
    ]);

    $link = new MenuItem;
    $link->name = 'Test empty link';
    $link->menu_id = $this->menu->id;
    $link->is_empty_link = true;
    $link->external_link = 'should_be_null_after_saved';
    $link->html = 'should_be_null_after_saved';
    $link->internal_link = 'should_be_null_after_saved';
    $link->save();

    expect($link->external_link)->toBeNull()
        ->and($link->internal_link)->toBeNull()
        ->and($link->html)->toBeNull()
        ->and($link->is_empty_link)->toBeTrue();
});

test('create html link test', function () {
    request()->merge([
        'link_type' => MenuItem::TYPE_HTML,
    ]);

    $html = '<div>test</div>';

    $link = new MenuItem;
    $link->name = 'Test html link';
    $link->menu_id = $this->menu->id;
    $link->html = $html;
    $link->is_empty_link = true;
    $link->external_link = 'should_be_null_after_saved';
    $link->internal_link = 'should_be_null_after_saved';
    $link->save();

    expect($link->external_link)->toBeNull()
        ->and($link->internal_link)->toBeNull()
        ->and($link->is_empty_link)->toBeFalse()
        ->and($link->html)->toBe($html);
});

function createMenu(): Menu
{
    $menu = new Menu;
    $menu->name = 'Test menu';
    if (!$menu->save()) {
        throw new RuntimeException('Unable to save menu.');
    }

    return $menu;
}
