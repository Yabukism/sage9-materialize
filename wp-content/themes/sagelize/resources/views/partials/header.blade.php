<header id="Main-header" class="navbar-fixed">
  <nav class="main-navigation" role="navigation">
  	<div class="nav-wrapper">
  		<a href="#" data-activates="primary-mobile-menu" class="button-collapse"><i class="material-icons">menu</i></a>


      {!! wp_nav_menu([
        'theme_location' => 'top_navigation1',
        'container' => false,
        'menu_id' => 'dropdown1',
        'menu_class' => 'right right hide-on-med-and-down',
        'walker' => new Materialize_Walker_Nav_Menu()
        ]) !!}


  	</div>
  </nav
</header>
