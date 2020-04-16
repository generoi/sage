<li class="{{ $item->classes ?? '' }} {{ ($item->active || $item->activeAncestor) ? 'active': '' }}">
  <a
    href="{{ $item->url }}"
    target="{{ $item->target ?? '' }}"
    title="{{ $item->title ?? '' }}"
    class="{{ ($item->active || $item->activeAncestor) ? 'active': '' }}"
  >
    {!! esc_html($item->label) !!}
  </a>

  @if ($item->children)
    <ul class="vertical menu {{ $item->active ? 'is-active' : '' }}">
      @foreach ($item->children as $child)
        @include('partials.menu-item', ['item' => $child])
      @endforeach
    </ul>
  @endif
</li>
