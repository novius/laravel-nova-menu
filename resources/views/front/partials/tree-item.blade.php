<li data-depth="{{ $tree['infos']['depth'] }}">
    <a href="{{ $tree['infos']['href'] }}">
        {{ $tree['infos']['name'] }}
    </a>
    @if (!empty($tree['children']))
        @include('laravel-nova-menu::front/partials/tree-item-list', ['tree' => $tree['children'], 'depth' => $depth])
    @endif
</li>
