<?php

namespace Novius\LaravelNovaMenu\View\Components;

use Illuminate\View\Component;
use Novius\LaravelNovaMenu\Helpers\MenuHelper;
use Novius\LaravelNovaMenu\Models\Menu as MenuModel;

class Menu extends Component
{
    public function __construct(public string|MenuModel $menu, public ?string $view = null, public bool $localeFallback = true)
    {
    }

    public function render()
    {
        return MenuHelper::displayMenu($this->menu, $this->view, $this->localeFallback);
    }
}
