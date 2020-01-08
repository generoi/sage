<!doctype html>
<html {!! get_language_attributes() !!}>
  @include('partials.head')

  <body @php(body_class())>
    @php(wp_body_open())
    <div id="app" class="site">
      <headroom :tolerance="10" :offset="100" class="site-header">
        @php(do_action('get_header'))
        @include('partials.header')
      </headroom>

      <main class="site-content">
        <div class="grid-container">
          @yield('content')
        </div>
      </main>

      <footer class="site-footer">
        @php(do_action('get_footer'))
        @include('partials.footer')
      </footer>
    </div>

    @php(wp_footer())
  </body>
</html>
