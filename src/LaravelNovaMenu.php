<?php

namespace Novius\LaravelNovaMenu;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Tool;
use Novius\LaravelNovaMenu\Resources\Menu;

class LaravelNovaMenu extends Tool
{
    public function menu(Request $request)
    {
        return MenuSection::resource(Menu::class)->icon('bars-3');
    }
}
