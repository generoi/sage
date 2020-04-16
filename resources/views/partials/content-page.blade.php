<article @php(post_class())>
  @if ($printPageHeading)
    <header>
      <h1 class="entry-title">
        {!! $title !!}
      </h1>
    </header>
  @endif

  <div class="entry-content">
    @php(the_content())
  </div>

  <footer>
    {!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
  </footer>
</article>
