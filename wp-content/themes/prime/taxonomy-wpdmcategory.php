<?php

if ( !defined('ABSPATH')) exit;

get_header();

?>

<div class="container home-content taxonomy-wpdmcategory">

    <div class="row">
        <?php
        $count = 0;
        while(have_posts()): the_post();
            $_wpdm_hide_all = get_option('_wpdm_hide_all', 0);
            if(get_option('__wpdm_cpage_style') != 'ltpl') {

                get_template_part("templates/product-blocks/flat");
                if (++$count % 3 == 0) echo "<div class='clear'></div>";
            } else {
                echo \WPDM\Package::fetchTemplate(get_option('__wpdm_cpage_template'), get_the_ID());
            }
        endwhile; ?>
    </div>
    <?php
    global $wp_query;
    if (  $wp_query->max_num_pages > 1 ) : ?>
        <div class="clear"></div>
        <div id="nav-below" class="navigation post box arc">
            <?php get_template_part('pagination'); ?>
        </div><!-- #nav-below -->
    <?php endif; ?>





</div>







<?php get_footer(); ?>
