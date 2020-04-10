<?php

namespace Novius\LaravelNovaMenu\Tests;

use Novius\LaravelNovaMenu\Models\Menu;

class DisplayTest extends TestCase
{
    public function testBasic()
    {
        $menu = new Menu();
        $menu->name = 'Test menu';
        $menu->slug = 'test-menu';
        $menu->save();

        return $this->assertDatabaseHas('nova_menus', [
            'slug' => 'test-menu',
        ]);
    }
}
