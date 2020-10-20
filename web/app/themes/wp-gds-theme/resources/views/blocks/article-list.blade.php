<div class="{{ esc_attr($block->classes) }}" @if (!empty($block->id) || $use_pagination) id="{{ $block->id ?? 'listing' }}" @endif>
  <div class="masonry-grid small:2">
    @while ($query->have_posts()) @php($query->the_post())
      <div class="column">
        @includeFirst(['teasers.' . get_post_type(), 'teasers.teaser'], ['post' => get_post()])
      </div>
    @endwhile
    @php(wp_reset_postdata())
  </div>

  @if ($use_pagination)
    <div class="wp-block-buttons aligncenter">
      @include('partials.pagination', ['query' => $query, 'fragment' => $block->id ?? 'listing'])
    </div>
  @endif
</div>