<?php
if(is_home() && get_option('show_on_front') == 'posts')
    define('THENEXT_HIDE_PAGE_HEADER',1);

get_header(); ?>

    <!-- PAGE HEADER
            ==========================================-->
    <div class="headertitle">
        <div class="headercontent">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h1><?php single_cat_title(); ?></h1>
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

        <div class="col-md-8">
            <div id="primary" class="content-area">
                <div class="wowitemboxlist">
                    <?php get_template_part('loop', get_post_type()); ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <?php dynamic_sidebar('right'); ?>
        </div>


    </div>
</div>

<?php get_footer(); 
