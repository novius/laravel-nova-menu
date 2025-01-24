<?php

namespace Novius\LaravelNovaMenu\View\Components;

use Illuminate\View\Component;
use Novius\LaravelNovaMenu\Helpers\MenuHelper;
use Novius\LaravelNovaMenu\Models\Menu as MenuModel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Menu extends Component
{
    public function __construct(public string|MenuModel $menu, public ?string $view = null, public bool $localeFallback = true) {}

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function render(): string
    {
        return MenuHelper::displayMenu($this->menu, $this->view, $this->localeFallback);
    }
}
