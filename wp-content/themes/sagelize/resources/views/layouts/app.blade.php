<!doctype html>
<html {{ get_language_attributes() }}>
  @include('partials.head')
  <body @php body_class() @endphp>
    @php do_action('get_header') @endphp
    @include('partials.header')

    <main class="main" role="document">
      <div class="container">
        <div class="row">
          <div class="col s12 m9 offset-m1 xl8 offset-xl1">
            @yield('content')
          </div>

          <div class="col hide-on-small-only m2 xl2 offset-xl1">
            @if (App\display_sidebar())
              <aside class="sidebar">
                @include('partials.sidebar')
              </aside>
            @endif
          </div>

        </div>
      </div>
    </main>
    @php do_action('get_footer') @endphp
    @include('partials.footer')
    @php wp_footer() @endphp
  </body>
</html>
