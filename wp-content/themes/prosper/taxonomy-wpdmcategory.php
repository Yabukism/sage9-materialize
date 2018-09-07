<?php

if ( !defined('ABSPATH')) exit;

get_header();

?>



<div class="headertitle">
    <div class="headercontent">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1><?php single_term_title(); ?></h1>
                    <span class="wtnbreadcrumbs">
                        <?php if ( function_exists('yoast_breadcrumb') ) { ?>

                                    <?php
                                    add_filter( 'wp_seo_get_bc_title', create_function('$title, $id','return "";'), 10, 2 );
                                    yoast_breadcrumb('','');
                                    ?>

                        <?php } ?>
                    </span>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php $count = 0;
            while(have_posts()): the_post();
                $_wpdm_hide_all = get_option('_wpdm_hide_all', 0);
                if(get_option('__wpdm_cpage_style') != 'ltpl') {

                    get_template_part("templates/product-blocks/category-item");
                    if (++$count % 3 == 0) echo "<div class='clear'></div>";
                } else {
                    echo \WPDM\Package::fetchTemplate(get_option('__wpdm_cpage_template'), get_the_ID());
                }
            endwhile; ?>
        </div>
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
