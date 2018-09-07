<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
 <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php wp_title(); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/animate.min.css" />
</head>
<body <?php body_class('w3eden'); ?>>


<div  class="wide" >
     
<!-- NAVBAR
    ================================================== -->
    <div class="navbar-wrapper">
      <!-- Wrap the .navbar in .container to center it within the absolutely positioned parent. -->
      <div class="nav-area">
      <div class="container">
        <div class="row header-logo-area">
            <div class="col-md-4">
                <h1><a class="site-logo" href="<?php echo esc_url(home_url('/')); ?>"><?php wpeden_get_site_logo(); ?></a></h1>
            </div>
            <div class="col-md-8">
                <?php if(!dynamic_sidebar('header')) echo "<div class='pull-right tagline'>".get_bloginfo('description')."</div>"; ?>
            </div>
        </div>
      </div>

      <div class="menu-bar" id="menu-bar">
          <div class="container">
          <div class="row">
          <div class="col-md-12">
            <div class="nav-wrapper">
            <nav class="navbar navbar-default" role="navigation">

                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#cryptonmainmenu">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                </div>

                    <div class="collapse navbar-collapse" id="cryptonmainmenu">
                      <?php


                                $args = array(
                                'theme_location' => 'primary',
                                'depth' => 3,
                                'container' => false,
                                'menu_class' => 'nav nav-pills nav-justified',
                                'fallback_cb' => false,
                                'walker' => new wpeden_bootstrap_walker_nav_menu()
                                );


                                wp_nav_menu($args);


                                ?>
                    </div><!--/.nav-collapse -->

                </nav>
            </div>
          </div>
          </div>
              </div>
      </div> <!-- /.container -->
     </div> 
      <?php if(is_archive()&&!is_author()): ?>
      <div class="container">
      <div class="row"><div class="col-md-12 arc-header">
      <h1 class="entry-title">
                        <?php if ( is_day() ) : ?>                            
                        <?php echo get_the_date(); ?>    
                        <?php elseif ( is_month() ) : ?>
                        Monthly Archives: <?php echo get_the_date( 'F Y' ); ?>                        
                        <?php elseif ( is_year() ) : ?>
                        <?php echo get_the_date( 'Y' ); ?>                            
                        <?php elseif(is_category()) : ?>
                        <?php echo single_cat_title( '', false ); ?>
                        <?php elseif(is_tag()) : ?> 
                        <?php echo single_tag_title(); ?>
                        <?php elseif(is_post_type_archive()): ?>
                            <?php echo isset($_GET['sort'])?esc_attr($_GET['sort']):''; ?> <?php echo isset($_GET['type'])?esc_attr($_GET['type']):''; ?> Apps
                        <?php elseif(is_tax()): ?>

                            <?php single_term_title(); ?>

                        <?php rewind_posts();
                        endif; ?>
          <?php if ( function_exists('yoast_breadcrumb') ) { ?>

              <div class="pull-left bcrumb">
                  <?php
                  yoast_breadcrumb('<a>','</a>');
                  ?>
              </div>

          <?php } ?>
      </h1>

      </div></div></div>
      <?php endif; ?>
      
      <?php
          if(is_front_page() || defined('HOMEPAGE'))  get_template_part('homepage','top'); //get_template_part('modules/bootstrap-carousel/slider');
      ?>
      
    </div><!-- /.navbar-wrapper -->
        
