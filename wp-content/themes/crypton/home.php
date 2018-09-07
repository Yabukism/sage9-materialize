<?php

/*
Template Name: Homepage Default
*/

if ( !defined('ABSPATH')) exit; 

 

get_header(); 

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
<div class="container">

        <div class="section-head"><span><?php echo __('New Downloads','crypton'); ?></span></div>


            
        <div class="row">
            <?php
            global $post;
            $q = new WP_Query('post_type=wpdmpro&posts_per_page=12');
            while($q->have_posts()): $q->the_post();

            ?>
            <div class="col-md-<?php echo wpeden_get_theme_opts('homepage_np_grids',3); ?>">
                <div class="panel panel-default package-block" id="p-<?php echo get_the_ID(); ?>">
                    <div class="panel-body">
                    <div class="media product-pane">

                        <a href="<?php the_permalink();?>" class="pull-left"><?php centrino_post_thumb(array(60,50)); ?></a>
                        <div class="media-body">
                            <h3><a href='<?php the_permalink(); ?>'><?php the_title(); ?></a></h3>
                            <div class="package-info">
                                <?php echo wpdm_package_size(get_the_ID()); ?>
                            </div>
                            <div class="package-rating">
                                <?php if(function_exists('wpdm_package_rating_avg')) wpdm_package_rating_avg(get_the_ID()) ;?>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="panel-footer">
                        <span class="text-success"><?php echo $dc = number_format(intval(get_post_meta(get_the_ID(),'__wpdm_download_count',true)),0,'',','); ?> download<?php if($dc>1) echo 's'; ?></span>
                        <a class="pull-right btn btn-xs btn-default" href="<?php the_permalink(); ?>">more <i class="fa fa-long-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>


        </div>

</div>
<br/>
            <div class="container">

                <div class="section-head"><span><?php echo __('Popular Downloads','crypton'); ?></span></div>



                <div class="row">
                    <?php
                    global $post;
                    $q = new WP_Query('post_type=wpdmpro&posts_per_page=12&orderby=meta_value_num&meta_key=__wpdm_download_count&order=DESC');
                    while($q->have_posts()): $q->the_post();

                        ?>
                        <div class="col-md-<?php echo wpeden_get_theme_opts('homepage_np_grids',3); ?>">
                            <div class="panel panel-default package-block" id="p-<?php echo get_the_ID(); ?>">
                                <div class="panel-body">
                                    <div class="media product-pane">

                                        <a href="<?php the_permalink();?>" class="pull-left"><?php centrino_post_thumb(array(60,50)); ?></a>
                                        <div class="media-body">
                                            <h3><a href='<?php the_permalink(); ?>'><?php the_title(); ?></a></h3>
                                            <div class="package-info">
                                                <?php echo wpdm_package_size(get_the_ID()); ?>
                                            </div>
                                            <div class="package-rating">
                                                <?php  if(function_exists('wpdm_package_rating_avg')) wpdm_package_rating_avg(get_the_ID()) ;?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <span class="text-success"><?php echo $dc = number_format(intval(get_post_meta(get_the_ID(),'__wpdm_download_count',true)),0,'',','); ?> download<?php if($dc>1) echo 's'; ?></span>
                                    <a class="pull-right btn btn-xs btn-default" href="<?php the_permalink(); ?>">more <i class="fa fa-long-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>


                </div>

            </div>

</div>
</div>
</div>

<br/>
<div class="container">

    <div class="section-head"><span><?php echo wpeden_get_theme_opts('blog_section_title','From Blog'); ?></span> <div class="pull-right"></div> </div>




                <div class="row">
                    <?php
                    $q = new WP_Query('posts_per_page=3');
                    $ccnt = 0;
                    while($q->have_posts()): $q->the_post(); ?>
                        <div class="col-md-4 home-cat-single">
                            <div class="entry-content media thumbnail">
                                <a href="<?php the_permalink();?>" class="pull-left">
                                    <?php centrino_post_thumb(array(70,60),true, array('class'=>'img-rounded')); ?>
                                </a>
                                <div class="media-body from-blog">
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>&nbsp;</h3>
                                <div class="post-meta"><i class="icon icon-time"></i> <?php echo get_the_date(); ?></div>

                                </div>
                            </div>

                        </div>
                    <?php endwhile; ?>
                </div>



 
<div class="clear"></div>
         

</div>

         


        
<?php get_footer(); ?>
