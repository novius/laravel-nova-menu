<li data-depth="{{ $tree['infos']['depth'] }}" @class(['has-sub-menu' => !empty($tree['children'])])>
    @if (!empty($treeItem['infos']['html']))
        {!! $treeItem['infos']['html'] !!}
    @else
        <a href="{{ $tree['infos']['href'] }}" @class([$tree['infos']['htmlClasses']]) {{ $tree['infos']['targetBlank'] ? 'target="_blank"' : '' }}>
            {{ $tree['infos']['name'] }}
        </a>
    @endif

    @if (!empty($tree['children']))
        @include('laravel-nova-menu::front/partials/tree-item-list', ['tree' => $tree['children'], 'depth' => $depth])
    @endif
</li>
