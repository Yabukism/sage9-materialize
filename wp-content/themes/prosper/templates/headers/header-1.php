<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>"/>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' type='text/css' media='all'/>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">
	<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Covered+By+Your+Grace'/>
	<?php wp_head(); ?>
</head>
<body  <?php body_class('w3eden'); ?> >
<div id="wrapall">
	<div class="wrapcontent">
		<!-- Branding
    ==========================================-->
		<div class="headerimage text-center" style="background: url('<?php echo WPEdenThemeEngine::NextGetOption('header_bg');?>') center center;">
			<div class="headercontent big">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<h1>
								<!-- Logo -->
								<a class="navbar-brand-middle site-logo" href="<?php echo esc_url(home_url('/')); ?>"><?php
									$logourl = ( WPEdenThemeEngine::NextGetOption('site_logo') );
									if ($logourl)
										echo "<img class='site-logo' src='{$logourl}' title='" . get_bloginfo('sitename') . "' alt='" . get_bloginfo('sitename') . "' />";
									else
										echo get_bloginfo('sitename');
									?></a>
							</h1>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Navigation
    ==========================================-->
		<nav id="wow-menu" class="navbar navbar-default">
			<div class="container">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<!-- Menu -->
				<div id="bs-example-navbar-collapse-1" class="collapse navbar-collapse">

					<?php


					$args = array(
						'theme_location' => 'primary',
						'depth' => 9,
						'container' => false,
						'menu_class' => 'nav navbar-nav',
						'menu_id' => 'menu-main-nav',
						'fallback_cb' => false,
						'walker' => new TheNextNavMenu()
					);


					wp_nav_menu($args);


					?>

				</div>
			</div>
		</nav>
