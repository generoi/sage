<div class="{{ $classes }}">
  <div class="flex -m-2">
    @foreach ($posts as $post)
      <div class="w-1/{{ $fields->small_columns }} md:w-1/{{ $fields->medium_columns }} lg:w-1/{{ $fields->large_columns }} m-2">
        <h3>{!! get_the_title($post) !!}</h3>

        {!! wpautop(get_the_excerpt($post)) !!}
      </div>
    @endforeach
  </div>
</div>
