<?php 
/**
 * Template Name: Elementor
 */



?><!DOCTYPE html>
    <!-- Nav Header Template : Header Eden -->
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
    <head>
        <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>"/>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <?php if (function_exists('bp_head')) bp_head(); ?>
        <?php wp_head(); ?>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script>
            jQuery(function ($) {
                $("#header-2").sticky({topSpacing: 0});
                $('#logos img, .ttip').tooltip({html: true});

                $(window).scroll(function () {
                    var scroll = getCurrentScroll();
                    if (scroll >= 400) {
                        $('#header-2').addClass('shrinked');
                    }
                    else {
                        $('#header-2').removeClass('shrinked');
                    }
                });
                function getCurrentScroll() {
                    return window.pageYOffset || document.documentElement.scrollTop;
                }

            });

            new WOW().init();
        </script>
    </head>
<body <?php body_class('w3eden'); ?>>

<?php

/**
 * Add anything immediately after body tag
 */
do_action("optimus_body_content_before");

?>

<div id="mainframe" class="<?php echo WPEdenThemeEngine::Layout('wide'); ?> header-eden" <?php do_action('thenext_mainframe_div_attrs'); ?> >


    <div id="main-nav-container">
        <div  class="container">
            <nav class="navbar navbar-eden">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                data-target="#main-menu">
                            <span class="sr-only">Toggle navigation</span>
                            <i class="tn-menu"></i>
                        </button>
                        <a class="site-logo" href="<?php echo esc_url(home_url('/')); ?>"><?php
                            $logourl = ( WPEdenThemeEngine::NextGetOption('site_logo') );
                            if ($logourl)
                                echo "<img class='site-logo' src='{$logourl}' title='" . get_bloginfo('sitename') . "' alt='" . get_bloginfo('sitename') . "' />";
                            else
                                echo get_bloginfo('sitename');
                            ?></a>
                    </div>

                    <div class="collapse navbar-collapse" id="main-menu">
                        <?php


                        $args = array(
                            'theme_location' => 'primary',
                            'depth' => 9,
                            'container' => false,
                            'menu_class' => 'nav navbar-nav navbar-right',
                            'menu_id' => 'mainmenu',
                            'fallback_cb' => false,
                            'walker' => new TheNextNavMenu()
                        );


                        wp_nav_menu($args);


                        ?>

                    </div>
                </div>
            </nav>
        </div>
    </div>





    <script>
        jQuery(function($){
            $('#search').click(function(){
                $('#main-menu, #mainmenu, .site-logo, #search').css('opacity',0);
                $('#main-search-form').fadeIn();
                $('#main-search-form input').focus();
                return false;
            });

            $('#remove-search').click(function(){
                $('#main-menu, #mainmenu, .site-logo, #search').css('opacity',1);
                $('#main-search-form').fadeOut();
            });
        });
    </script>

<div class="container-fluid">
    <div class="row">

        <div class="col-md-12">
            <div id="single-page-<?php the_ID(); ?>" class="single-page">

                <?php while (have_posts()): the_post(); ?>

                    <div <?php post_class('post'); ?>>
                        <div class="clear"></div>
                        <?php do_action("thenext_before_content"); ?>

                            <?php the_content(); ?>

                        <?php do_action("thenext_after_content"); ?>
                    </div>

                <?php endwhile; ?>

            </div>
        </div>

    </div>
</div>

    <?php if (defined('THENEXT_LEFT_NAV')) echo '</div> </div> </div>'; ?>

    <?php do_action(THENEXT_THEME_PREFIX . "body_content_after"); ?>

    <?php wp_footer(); ?>
</body>
</html>
