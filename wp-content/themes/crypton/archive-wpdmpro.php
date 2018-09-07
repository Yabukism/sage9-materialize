<?php

if ( !defined('ABSPATH')) exit;

get_header();

?>

<div class="container home-content taxonomy-wpdmcategory wpdmarc">
    <section>
        <?php
        $term = get_term(wpeden_get_theme_opts('category_1'), 'wpdmcategory');

        ?>

        <div class="home-heading" style="border-bottom: 1px solid #ddd;padding-bottom: -1px">

            <h3 style="margin: 0">
                <ul class="nav nav-tabs pull-right" role="tablist">
                    <li <?php echo isset($_GET['type']) && $_GET['type']=='paid'?'class="active"':''; ?>><a href="<?php echo get_post_type_archive_link( 'wpdmpro' ); echo (get_option('permalink_structure','')!='')?'?':'&'  ?>sort=<?php echo isset($_GET['sort'])?esc_attr($_GET['sort']):'new' ?>&type=paid">Paid</a></li>
                    <li <?php echo isset($_GET['type']) && $_GET['type']=='free'?'class="active"':''; ?>><a href="<?php echo get_post_type_archive_link( 'wpdmpro' ); echo (get_option('permalink_structure','')!='')?'?':'&'  ?>sort=<?php echo isset($_GET['sort'])?esc_attr($_GET['sort']):'new' ?>&type=free">Free</a></li>
                    <li  <?php echo (isset($_GET['type']) && $_GET['type']=='all')||!isset($_GET['type'])?'class="active"':''; ?>><a href="<?php echo get_post_type_archive_link( 'wpdmpro' ); echo (get_option('permalink_structure','')!='')?'?':'&'  ?>sort=<?php echo isset($_GET['sort'])?esc_attr($_GET['sort']):'new' ?>&type=all">All Apps</a></li>
                </ul>
                <ul class="nav nav-tabs pull-left" role="tablist">
                    <li  <?php echo (isset($_GET['sort']) && $_GET['sort']=='new') || !isset($_GET['sort'])?'class="active"':''; ?>><a href="<?php echo get_post_type_archive_link( 'wpdmpro' ); echo (get_option('permalink_structure','')!='')?'?':'&'  ?>sort=new&type=<?php echo isset($_GET['type'])?esc_attr($_GET['type']):'all' ?>">New</a></li>
                    <li <?php echo isset($_GET['sort']) && $_GET['sort']=='popular'?'class="active"':''; ?>><a href="<?php echo get_post_type_archive_link( 'wpdmpro' ); echo (get_option('permalink_structure','')!='')?'?':'&'  ?>sort=popular&type=<?php echo isset($_GET['type'])?esc_attr($_GET['type']):'all' ?>">Popular</a></li>
                    <li <?php echo isset($_GET['sort']) && $_GET['sort']=='trending'?'class="active"':''; ?>><a href="<?php echo get_post_type_archive_link( 'wpdmpro' ); echo (get_option('permalink_structure','')!='')?'?':'&'  ?>sort=trending&type=<?php echo isset($_GET['type'])?esc_attr($_GET['type']):'all' ?>">Trending</a></li>
                </ul>
            </h3>
            <div class="clear"></div>
        </div>
        <div class="tab-content home-tab-c">

            <div class="tab-pane active">
                <div class="row">
                    <?php
                    global $post, $wp_query;
                    $params = array('post_type'=>'wpdmpro','posts_per_page'=>30);
                    if(isset($_GET['sort']) && $_GET['sort']=='popular')
                        //$parmas['meta_query'][] = array('orderby' => 'meta_value_num', 'meta_key' => '__wpdm_download_count', 'order' => 'DESC');
                        $params = array('post_type'=>'wpdmpro','posts_per_page'=>30,'orderby' => 'meta_value_num', 'meta_key' => '__wpdm_download_count', 'order' => 'DESC');
                    if(isset($_GET['sort']) && $_GET['sort']=='trending')
                        $params = array('post_type'=>'wpdmpro','posts_per_page'=>30,'orderby' => 'meta_value_num', 'meta_key' => '__wpdm_download_count', 'order' => 'DESC');
                        //$parmas['meta_query'][] = array('orderby' => 'meta_value_num', 'meta_key' => '__wpdm_view_count', 'order' => 'DESC');
                    //if((isset($_GET['sort']) && $_GET['sort']=='new') || !isset($_GET['sort']))
                    //    $params = array('post_type'=>'wpdmpro','posts_per_page'=>30);
                    if(isset($_GET['type']) && $_GET['type']=='paid')
                        $params['meta_query'][] = array('value' => '1', 'type'    => 'numeric', 'key' => '__wpdm_base_price', 'compare' => '>', 'post_type' =>'wpdmpro');
                    if(isset($_GET['type']) && $_GET['type']=='free')
                        $params['meta_query'][] = array('value' => 0, 'type'    => 'numeric', 'key' => '__wpdm_base_price', 'compare' => '==', 'post_type' =>'wpdmpro');


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

            </div>
        </div>

    </section>

    <?php

    if (  $q->max_num_pages > 1 ) : ?>
        <div class="clear"></div>
        <div id="nav-below" class="navigation post box arc">
            <?php
            global $wp_rewrite;
            $q->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;

            $pagination = array(
                'base' => @add_query_arg('paged','%#%'),
                'format' => '',
                'total' => $q->max_num_pages,
                'current' => $current,
                'show_all' => false,
                'type' => 'list',
                'prev_next'    => True,
                'prev_text' => '<i class="icon icon-angle-left"></i> Previous',
                'next_text' => 'Next <i class="icon icon-angle-right"></i>',
            );

            if( $wp_rewrite->using_permalinks() )
                $pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg('s',get_pagenum_link(1) ) ) . 'page/%#%/', 'paged');



            echo '<div class="pagination pagination-centered">' . str_replace("ul class='page-numbers'","ul class='pagination'", paginate_links($pagination)) . '</div>';
            ?>
        </div><!-- #nav-below -->
    <?php endif; ?>





</div>







<?php get_footer(); ?>
