<?php

namespace Novius\LaravelNovaMenu;

use Closure;
use Novius\LaravelNovaMenu\Helpers\MenuHelper;
use Novius\LaravelNovaMenu\Models\Menu;

class LaravelNovaMenuService
{
    /**
     * The callback that should be used on tree before passing to view
     *
     * @var (Closure(Menu, array):array)|null
     */
    protected ?Closure $treeUsing = null;

    /**
     * The callback that should be used to build tree
     *
     * @var (Closure(Menu):array)|null
     */
    protected ?Closure $buildTreeUsing = null;

    /**
     *  Register callback that should be used on tree before passing to view
     *
     * @param  Closure(Menu, array):array  $callback
     * @return $this
     */
    public function setTreeUsing(Closure $callback): self
    {
        $this->treeUsing = $callback;

        return $this;
    }

    /**
     *  Register callback that should be used to build tree
     *
     * @param  Closure(Menu):array  $callback
     * @return $this
     */
    public function setBuildTreeUsing(Closure $callback): self
    {
        $this->buildTreeUsing = $callback;

        return $this;
    }

    /**
     * Return the tree that will be passed to the view
     */
    public function tree(Menu $menu, array $tree): array
    {
        return ($this->treeUsing ?: static function (Menu $menu, array $tree) {
            return $tree;
        })($menu, $tree);
    }

    /**
     * Return the tree that will be passed to the view
     */
    public function buildTree(Menu $menu): array
    {
        return ($this->buildTreeUsing ?: [MenuHelper::class, 'buildTree'])($menu);
    }
}
