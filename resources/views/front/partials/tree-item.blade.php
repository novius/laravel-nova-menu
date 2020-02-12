<li data-depth="{{ $tree['infos']['depth'] }}" class="{{ !empty($tree['children']) ? 'has-sub-menu' : '' }}">
    <a href="{{ $tree['infos']['href'] }}" class="{{ $tree['infos']['htmlClasses'] }}" {{ $tree['infos']['targetBlank'] ? 'target="_blank"' : '' }}>
        {{ $tree['infos']['name'] }}
    </a>
    @if (!empty($tree['children']))
        @include('laravel-nova-menu::front/partials/tree-item-list', ['tree' => $tree['children'], 'depth' => $depth])
    @endif
</li>
