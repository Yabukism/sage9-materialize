<?php 

if ( !defined('ABSPATH')) exit; 

get_header(); 

?>



<div class="container home-content taxonomy-ptype">
    <?php if ( function_exists('yoast_breadcrumb') ) { ?>
        <div class="row">
            <div class="col-md-12">
                <?php
                yoast_breadcrumb('<a>','</a>');
                ?>
            </div>
        </div>
    <?php } ?>

<?php

    while(have_posts()): the_post();
?>
        <div class="row">
            <div class="col-md-3">

                    <a href="<?php the_permalink();?>"><?php centrino_post_thumb(array(300,250)); ?></a>

            </div>
            <div class="col-md-5">
                <h3 class="entry-title"><?php the_title(); ?></h3>
                <?php the_excerpt(); ?>
            </div>
            <div class="col-md-4">
                <table class="table table-striped table-bordered">
                    <tbody>
                    <tr><td><?php _e('File Size', 'crypton'); ?></td><td><?php echo wpdm_package_size(get_the_ID()); ?></td></tr>
                    <tr><td><?php _e('Number of Files', 'crypton'); ?></td><td><?php echo wpdm_package_filecount(get_the_ID()); ?></td></tr>
                    <tr><td><?php _e('Downloaded', 'crypton'); ?></td><td><?php echo $pack['download_count']; ?> time(s)</td></tr>
                    <tr><td><?php _e('Published on', 'crypton'); ?></td><td><?php echo get_the_date(); ?></td></tr>
                    <tr><td><?php _e('File Types', 'crypton'); ?></td><td><?php echo wpdm_package_filetypes(get_the_ID()); ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
<?php endwhile; ?>
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
