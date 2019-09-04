<li data-depth="{{ $tree['infos']['depth'] }}">
    {{ $tree['infos']['name'] }}
    @if (!empty($tree['children']))
        @include('laravel-nova-menu::front/partials/tree-item-list', ['tree' => $tree['children'], 'depth' => $depth])
    @endif
</li>
