<ul data-depth="{{ $depth }}">
    @foreach($tree as $treeItem)
        @include('laravel-nova-menu::front/partials/tree-item', [
            'tree' => $treeItem,
            'depth' => ($depth + 1),
        ])
    @endforeach
</ul>
