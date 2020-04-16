<article @php(post_class())>
  @if ($printPageHeading)
    <header>
      <h1 class="entry-title">
        {!! $title !!}
      </h1>
    </header>
  @endif

  <div class="entry-content">
    {!! $content !!}
  </div>
</article>
