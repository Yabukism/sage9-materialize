<?php get_header();

$sidebarw = 3;
$bodyw = 12-$sidebarw;

$_wpdm_hide_all = get_option('_wpdm_hide_all', 0);


?>
<div class="headertitle">
    <div class="headercontent">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1><?php the_title(); ?></h1>
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
    <?php the_content(); ?>
    <div class="row mx_comments">
    <?php comments_template(); ?>
    </div>
</div>


<?php get_footer(); ?>
