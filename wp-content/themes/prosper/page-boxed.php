<?php
/**
 * Template Name: Page Boxed
 */
get_header();

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
<div class="container container-boxed">
    <div class="row">


        <div class="col-md-12">
            <div id="single-page-<?php the_ID(); ?>" class="single-page">

                <?php while (have_posts()): the_post(); ?>

                    <div <?php post_class('post'); ?>>
                        <div class="clear"></div>
                        <?php do_action("thenext_before_content"); ?>
                        <div class="entry-content">
                            <?php wpeden_post_thumb(array(1100, 0), true, array('class' => 'single-page-thumbnail')); ?>
                            <?php the_content(); ?>
                        </div>
                        <?php wp_link_pages(); ?>
                        <?php do_action("thenext_after_content"); ?>
                    </div>


                <?php endwhile; ?>

            </div>
        </div>


    </div>
</div>


<?php
get_footer();
