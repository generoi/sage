<div class="site">
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
    @include('partials.footer')
  </footer>
</div>
