<!doctype html>
<html {!! get_language_attributes() !!}>
  @include('partials.head')

  <body @php(body_class())>
    @php(wp_body_open())
    @php(do_action('get_header'))
    <header class="bg-primary text-white">
      <div class="max-w-container mx-auto py-2 px-1 md:px-2 lg:px-3">
        <a class="brand" href="{{ home_url('/') }}">
          {{ get_bloginfo('name', 'display') }}
        </a>

        <nav class="nav-primary">
          @if (has_nav_menu('primary_navigation'))
            {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']) !!}
          @endif
        </nav>
      </div>
    </header>

    <main id="app" class="max-w-container mx-auto px-1 md:px-2 lg:px-3">
      @yield('content')
    </main>

    @php(do_action('get_footer'))
    @include('partials.footer')

    @php(wp_footer())
  </body>
</html>
