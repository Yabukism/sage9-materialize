<?php get_header(); ?>
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
    <div class="row">
        <?php TheNextFramework::DynamicSidebars('left'); ?>
        <div class="<?php TheNextFramework::ContentAreaWidth(); ?>">
            <div id="primary" class="content-area">
                <main id="main" class="site-main whiteboxed" role="main">
                    <div class="wrapindexcerpt">
                        <div class="contenttext">
                            <div class="post-content">
                                <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                    <?php

                                    while (have_posts()): the_post(); ?>

                                        <div  <?php post_class('post'); ?>>


                                            <?php if (get_post_format() == 'video') { ?>
                                                <div class="thumbnail">

                                                    <?php
                                                    $meta = maybe_unserialize(get_post_meta(get_the_ID(), 'wpeden_post_meta', true));
                                                    echo wp_oembed_get($meta['videourl']);
                                                    ?>

                                                </div>
                                            <?php } else if (get_post_format() == 'gallery') { ?>

                                                <?php //wpeden_post_gallery(900, 0); ?>

                                            <?php } else {
                                                wpeden_post_thumb(array(1100, 0), true, array('class' => 'single-post-thumbnail'));
                                            } ?>

                                            <div class="wowmetaposts entry-meta">
                                                <span class="wowmetadate"><i class="fa fa-clock-o"></i> <?php the_date(); ?></span>
                                                <span class="wowmetaauthor"><i class="fa fa-user"></i> <a href="#"><?php the_author(); ?></a></span>
                                                <span class="wowmetacats"><i class="fa fa-folder-open"></i>	<?php the_category(', '); ?></span>
                                                <span class="wowmetacommentnumber"><i class="fa fa-comments"></i> <a href="#">Leave a Comment</a></span>
                                            </div>

                                            <div class="entry-content">

                                                <?php if (get_post_format() == 'audio') echo do_shortcode('[audio]'); ?>
                                                <?php the_content(); ?>
                                            </div>
                                            <?php wp_link_pages(); ?>



                                            <div class="clear"></div>
                                            <div class="post-author-info post-tags">
                                                <?php the_tags('', ''); ?>
                                                <div class="clear"></div>
                                            </div>

                                            <span
                                                class="text-primary"><?php previous_post_link('%link', '<i class="fa fa-long-arrow-left"></i> ' . __('Previous', 'the-next')); ?></span>
                                            <i class="fa fa-dot-circle-o"></i>
                                            <span><?php next_post_link('%link', __('Next', 'the-next') . ' <i class="fa fa-long-arrow-right"></i>'); ?></span>

                                            <hr/>

                                            <div class="post-author-info well">

                                                <div class="media">
                                                    <div class="pull-left">
                                                        <?php echo get_avatar(get_the_author_meta('ID'), 90); ?>
                                                    </div>
                                                    <div class="media-body">
                                                        <span class="author-name"><?php echo get_the_author_meta('display_name'); ?></span>
                                                        <div class="clear"></div>
                                                        <?php echo get_the_author_meta('description'); ?>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="mx_comments">
                                            <?php comments_template(); ?>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <?php TheNextFramework::DynamicSidebars('right'); ?>
    </div>
</div>



<?php get_footer();
