
<li>
    <a href="{{ $menu->childrenRecursive->isNotEmpty() ? '#' . Str::slug($menu->name) : $menu->link }}" data-href="{{ $menu->link }}">
        @if($menu->icon)
            {!! $menu->icon !!}
        @endif
        <span class="label">{{ $menu->name }}</span>
    </a>

    @if($menu->childrenRecursive->isNotEmpty())

        <ul id="{{ Str::slug($menu->name) }}">
            @foreach($menu->childrenRecursive as $child)
                @include('_layout.partials.menu-item-child', ['menu' => $child])
            @endforeach
        </ul>
    @endif
</li>
