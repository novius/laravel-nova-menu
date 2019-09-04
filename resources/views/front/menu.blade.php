<nav>
    <div>
        {{ $menu->name }}
    </div>
    @include('laravel-nova-menu::front/partials/tree-item-list', ['tree' => $tree, 'depth' => 0])
</nav>
