<header id="Main-header" class="navbar-fixed">
	<!-- Dropdown Structure -->

			<nav class="top-nav">
        @if (has_nav_menu('primary_navigation'))
          {!! wp_nav_menu([
           'theme_location' => 'primary_navigation',
           'menu_class' => 'site-navigation__main-nav',
           'walker' => new Materialize_Walker_Nav_Menu()
          ]) !!}
      @endif




				<div class="container">
					<div class="nav-wrapper">
						<a href="#" data-activates="nav-mobile" class="button-collapse top-nav full hide-on-large-only">
							<i class="material-icons">menu</i>
						</a>
						<ul class="right">
							<li>
								<a class='dropdown-button' href='#' data-activates='dropdown1'>
									<span class="display-l">カテゴリ&nbsp;&nbsp;<i class="material-icons right">arrow_drop_down</i></span>
									<span class="display-m"><i class="material-icons">folder</i></span>
								</a>
							</li>
							<li>
								<a class='dropdown-button' href='#' data-activates='dropdown2'>
									<span class="display-l">用語カテゴリ<i class="material-icons right">arrow_drop_down</i></span>
									<span class="display-m"><i class="material-icons">folder_shared</i></span>
								</a>
							</li>
							<li class="hide-on-med-and-down">
									<a href='<?php echo esc_url(home_url('/downloads-lists/')); ?>' >
									<span class="display-l">ダウンロード&nbsp;</span>
									<span class="display-m"><i class="material-icons">cloud_download</i></span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</nav>

			<ul id="nav-mobile" class="side-nav fixed">
				<li class="brand-logo">
					<div class="valign-wrapper">
						<a class="valign waves-effect waves-light" href="{{ home_url('/') }}">
          <img src="@asset('images/logo.svg')" alt="ホーム" width="140" height="19">
        </a>
					</div>
				</li>
				<li>
					<a class="waves-effect waves-light" href="<?php echo esc_url(home_url('/%E6%97%A5%E6%9C%AC%E5%8F%B2%E3%83%BB%E4%B8%96%E7%95%8C%E5%8F%B2%EF%BC%88%E7%9B%AE%E6%AC%A1%EF%BC%89/')); ?>"><i class="material-icons">school</i>日本史/世界史</a>
				</li>
				<li>
					<a class="waves-effect waves-light" href="<?php echo esc_url(home_url('/timeline/')); ?>"><i class="material-icons">account_balance</i>年表から探す</a>
				</li>
				<li>
					<a class="waves-effect waves-light" href="<?php echo esc_url(home_url('/map-search')); ?>/"><i class="material-icons">add_location</i>地図から探す</a>
				</li>
				<li>
					<a class="waves-effect waves-light" href="<?php echo esc_url(home_url('/keywords-lists/')); ?>"><i class="material-icons">book</i>用語集</a>
				</li>
				<li>
					<a class="waves-effect waves-light" href="<?php echo esc_url(home_url('/chronology/')); ?>"><i class="material-icons">timeline</i>年表・表</a>
				</li>
				<li>
					<a class="waves-effect waves-light" href="<?php echo esc_url(home_url('/downloads-lists/')); ?>"><i class="material-icons pink-text text-lighten-3">cloud_download</i>ダウンロード</a>
				</li>
				<li class="search">
					<?php get_search_form(); ?>
				</li>
			</ul>
			<div>
</header>
