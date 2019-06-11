<div class="{{ $classes }}">
  <accordion-list role="tablist">
    @foreach ($posts as $post)
      <accordion class="my-2" :id="{{ $post->ID }}" role="tab">
        <h3 v-if="false">{!! get_the_title($post) !!}</h3>

        <template #title>{!! get_the_title($post) !!}</template>

        {!! wpautop(get_the_excerpt($post)) !!}
      </accordion>
    @endforeach
  </accordion-list>
</div>
