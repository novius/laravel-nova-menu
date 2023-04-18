<?php

namespace Novius\LaravelNovaMenu\Tests\Feature;

use Novius\LaravelNovaMenu\Models\Menu;
use Novius\LaravelNovaMenu\Models\MenuItem;
use Novius\LaravelNovaMenu\Tests\TestCase;

class ItemObserverTest extends TestCase
{
    protected Menu $menu;

    public function setUp(): void
    {
        parent::setUp();

        $this->menu = $this->createMenu();
    }

    /** @test */
    public function createExternalLinkTest()
    {
        request()->merge([
            'link_type' => MenuItem::TYPE_EXTERNAL_LINK,
        ]);
        $linkValue = 'https://www.novius.fr';

        $link = new MenuItem();
        $link->name = 'Test external';
        $link->menu_id = $this->menu->id;
        $link->external_link = $linkValue;
        $link->internal_link = 'should_be_null_after_saved';
        $link->html = 'should_be_null_after_saved';
        $link->is_empty_link = 1;
        $link->save();

        $this->assertNull($link->internal_link);
        $this->assertNull($link->html);
        $this->assertEquals(0, $link->is_empty_link);
        $this->assertEquals($link->external_link, $linkValue);
    }

    /** @test */
    public function createInternalLinkTest()
    {
        request()->merge([
            'link_type' => MenuItem::TYPE_INTERNAL_LINK,
        ]);
        $linkValue = 'linkable_route:contact';

        $link = new MenuItem();
        $link->name = 'Test internal';
        $link->menu_id = $this->menu->id;
        $link->external_link = 'should_be_null_after_saved';
        $link->is_empty_link = 1;
        $link->html = 'should_be_null_after_saved';
        $link->internal_link = $linkValue;
        $link->save();

        $this->assertNull($link->external_link);
        $this->assertNull($link->html);
        $this->assertEquals(0, $link->is_empty_link);
        $this->assertEquals($link->internal_link, $linkValue);
    }

    /** @test */
    public function createEmptyLinkTest()
    {
        request()->merge([
            'link_type' => MenuItem::TYPE_EMPTY,
        ]);

        $link = new MenuItem();
        $link->name = 'Test empty link';
        $link->menu_id = $this->menu->id;
        $link->is_empty_link = 1;
        $link->external_link = 'should_be_null_after_saved';
        $link->html = 'should_be_null_after_saved';
        $link->internal_link = 'should_be_null_after_saved';
        $link->save();

        $this->assertNull($link->external_link);
        $this->assertNull($link->internal_link);
        $this->assertNull($link->html);
        $this->assertEquals(1, $link->is_empty_link);
    }

    /** @test */
    public function createHtmlLinkTest()
    {
        request()->merge([
            'link_type' => MenuItem::TYPE_HTML,
        ]);

        $html = '<div>test</div>';

        $link = new MenuItem();
        $link->name = 'Test html link';
        $link->menu_id = $this->menu->id;
        $link->html = $html;
        $link->is_empty_link = 1;
        $link->external_link = 'should_be_null_after_saved';
        $link->internal_link = 'should_be_null_after_saved';
        $link->save();

        $this->assertNull($link->external_link);
        $this->assertNull($link->internal_link);
        $this->assertEquals(0, $link->is_empty_link);
        $this->assertEquals($link->html, $html);
    }

    protected function createMenu(): Menu
    {
        $menu = new Menu();
        $menu->name = 'Test menu';
        if (! $menu->save()) {
            throw new \Exception('Unable to save menu.');
        }

        return $menu;
    }
}
