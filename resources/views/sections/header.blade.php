<header class="banner flex flex-col items-center gap-6 p-7 md:flex-row">
  <a class="brand" href="{{ home_url('/') }}">
    {!! $siteName !!}
  </a>

  @if (has_nav_menu('primary_navigation'))
    <nav class="nav-primary" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
      {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav', 'echo' => false]) !!}
    </nav>
  @endif
</header>
<div>
  <h2>Site options</h2>

  {!!
    'GTM ID: '.carbon_get_theme_option( 'crb_gtm_id' );
  !!}
</div>
<hr/>
