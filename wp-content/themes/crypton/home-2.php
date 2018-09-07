<?php
/*
Template Name: Homepage: Opera Apps
*/

if (!defined('ABSPATH')) exit;

define("HOMEPAGE", 1);

get_header();

?>

<div class="container">
    <div class="row">
        <?php
        if(!wpeden_get_theme_opts('category_1')){
            ?>

            <div class="col-md-12">
                <div class="alert alert-info">
                    Please configure homepage from <a href="<?php echo admin_url('/themes.php?page=wpeden-themeopts'); ?>">theme options</a>!
                </div>
            </div>
            </div>
    </div>

            <?php
            exit;
        }
        ?>
        <div class="col-md-9 right-sap">
            <section>
                <?php
                $term = get_term(wpeden_get_theme_opts('category_1'), 'wpdmcategory');

                ?>

                <div class="home-heading" style="border-bottom: 1px solid #ddd;padding-bottom: -1px">

                    <h3 style="margin: 0">
                        <ul class="nav nav-tabs pull-right" role="tablist">
                            <li><a href="<?php echo get_post_type_archive_link( 'wpdmpro' ); echo (get_option('permalink_structure','')!='')?'?':'&'  ?>type=paid">Paid</a></li>
                            <li><a href="<?php echo get_post_type_archive_link( 'wpdmpro' ); echo (get_option('permalink_structure','')!='')?'?':'&'  ?>type=free">Free</a></li>
                            <li class="active"><a href="#">All Apps</a></li>
                        </ul>
                        New Apps
                    </h3>
                    <div class="clear"></div>
                </div>
                <div class="tab-content home-tab-c">

                    <div class="tab-pane active">
                        <div class="row">
                            <?php
                            global $post;
                            $params = array('post_type'=>'wpdmpro','posts_per_page'=>6);
                            //$params['tax_query'] = array(array('taxonomy'=>'wpdmcategory','field'=>'id', 'terms'=>array(wpeden_get_theme_opts('category_1'))));

                            $q = new WP_Query($params);
                            while ($q->have_posts()): $q->the_post();

                                ?>
                                <div class="col-md-<?php echo wpeden_get_theme_opts('homepage_np_grids', 2); ?>">
                                    <div class="item-block" id="p-<?php echo get_the_ID(); ?>">
                                        <div class="item-body">
                                            <div class="media product-pane">

                                                <a href="<?php the_permalink(); ?>"><?php centrino_post_thumb(array(150, 150)); ?></a>

                                                <div class="media-body">
                                                    <h3>
                                                        <a href='<?php the_permalink(); ?>'><?php echo substr(get_the_title(), 0, 10); ?>
                                                            ...</a></h3>

                                                    <div class="package-info">
                                                        by <?php the_author(); ?>
                                                    </div>
                                                    <div class="package-rating">
                                                        <?php if (function_exists('wpdm_package_rating_avg')) wpdm_package_rating_avg(get_the_ID()); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            <?php endwhile; ?>


                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <a class="btn btn-theme btn-sm" style="margin: 15px 0" href="<?php  if($term) echo get_term_link($term); ?>">More Apps <i
                                        class="fa fa-long-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
            <br/><br/>
            <section>
                <?php
                $term = get_term(wpeden_get_theme_opts('category_1'), 'wpdmcategory');
                ?>

                <div class="home-heading" style="border-bottom: 1px solid #ddd;padding-bottom: -1px">

                    <h3 style="margin: 0">
                        <ul class="nav nav-tabs pull-right" role="tablist">
                            <li><a href="<?php echo get_post_type_archive_link( 'wpdmpro' ); echo (get_option('permalink_structure','')!='')?'?':'&'  ?>type=paid">Paid</a></li>
                            <li><a href="<?php echo get_post_type_archive_link( 'wpdmpro' ); echo (get_option('permalink_structure','')!='')?'?':'&'  ?>type=free">Free</a></li>
                            <li class="active"><a href="#">All Apps</a></li>
                        </ul>
                        Popular Apps
                    </h3>
                    <div class="clear"></div>
                </div>
                <div class="tab-content home-tab-c">

                    <div class="tab-pane active">
                        <div class="row">
                            <?php
                            global $post;
                            $params = array('post_type'=>'wpdmpro','posts_per_page'=>6,'orderby' => 'meta_value_num', 'meta_key' => '__wpdm_download_count', 'order' => 'DESC');
                            //$params['tax_query'] = array(array('taxonomy'=>'wpdmcategory','field'=>'id', 'terms'=>array(wpeden_get_theme_opts('category_1'))));

                            $q = new WP_Query($params);
                            while ($q->have_posts()): $q->the_post();

                                ?>
                                <div class="col-md-<?php echo wpeden_get_theme_opts('homepage_np_grids', 2); ?>">
                                    <div class="item-block" id="p-<?php echo get_the_ID(); ?>">
                                        <div class="item-body">
                                            <div class="media product-pane">

                                                <a href="<?php the_permalink(); ?>"><?php centrino_post_thumb(array(150, 150)); ?></a>

                                                <div class="media-body">
                                                    <h3>
                                                        <a href='<?php the_permalink(); ?>'><?php echo substr(get_the_title(), 0, 10); ?>
                                                            ...</a></h3>

                                                    <div class="package-info">
                                                        by <?php the_author(); ?>
                                                    </div>
                                                    <div class="package-rating">
                                                        <?php if (function_exists('wpdm_package_rating_avg')) wpdm_package_rating_avg(get_the_ID()); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            <?php endwhile; ?>


                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <a class="btn btn-theme btn-sm" style="margin: 15px 0" href="<?php echo get_term_link($term); ?>">More Apps <i
                                        class="fa fa-long-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
            <br/><br/>
            <section>
                <?php
                    $term = get_term(wpeden_get_theme_opts('category_1'), 'wpdmcategory');
                ?>

                <div class="home-heading" style="border-bottom: 1px solid #ddd;padding-bottom: -1px">

                    <h3 style="margin: 0">
                        <ul class="nav nav-tabs pull-right" role="tablist">
                            <li><a href="<?php echo get_term_link($term); echo (get_option('permalink_structure','')!='')?'?':'&'  ?>type=paid">Paid</a></li>
                            <li><a href="<?php echo get_term_link($term); echo (get_option('permalink_structure','')!='')?'?':'&'  ?>type=free">Free</a></li>
                            <li class="active"><a href="#">All Apps</a></li>
                        </ul>
                        <?php echo $term->name; ?>
                    </h3>
                    <div class="clear"></div>
                </div>
                <div class="tab-content home-tab-c">

                    <div class="tab-pane active">
                        <div class="row">
                            <?php
                            global $post;
                            $params = array('post_type'=>'wpdmpro','posts_per_page'=>6);
                            $params['tax_query'] = array(array('taxonomy'=>'wpdmcategory','field'=>'id', 'terms'=>array(wpeden_get_theme_opts('category_1'))));

                            $q = new WP_Query($params);
                            while ($q->have_posts()): $q->the_post();

                                ?>
                                <div class="col-md-<?php echo wpeden_get_theme_opts('homepage_np_grids', 2); ?>">
                                    <div class="item-block" id="p-<?php echo get_the_ID(); ?>">
                                        <div class="item-body">
                                            <div class="media product-pane">

                                                <a href="<?php the_permalink(); ?>"><?php centrino_post_thumb(array(150, 150)); ?></a>

                                                <div class="media-body">
                                                    <h3>
                                                        <a href='<?php the_permalink(); ?>'><?php echo substr(get_the_title(), 0, 10); ?>
                                                            ...</a></h3>

                                                    <div class="package-info">
                                                        by <?php the_author(); ?>
                                                    </div>
                                                    <div class="package-rating">
                                                        <?php if (function_exists('wpdm_package_rating_avg')) wpdm_package_rating_avg(get_the_ID()); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            <?php endwhile; ?>


                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <a class="btn btn-theme btn-sm" style="margin: 15px 0" href="<?php echo get_term_link($term); ?>">More Apps <i
                                        class="fa fa-long-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
            <br/>
            <br/>


        </div>
        <div class="col-md-3  home-opera-sidebar">

            <div class="widget">
                <?php dynamic_sidebar("homepage_sidebar_right"); ?>
            </div>
        </div>
    </div>
</div>







<?php get_footer(); ?>
