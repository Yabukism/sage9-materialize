<?php get_header();
$pack['ID'] = get_the_ID();
$pack['post_title'] = get_the_title();
$pack['post_content'] = get_the_content();
$pack['post_excerpt'] = get_the_excerpt();
$pack = wpdm_setup_package_data($pack);

?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <?php

                while (have_posts()): the_post(); ?>

                    <div  <?php post_class('post'); ?>>

                        <div class="clear"></div>

                        <?php if (get_post_format() == 'video') { ?>
                            <div class="thumbnail">

                                <?php
                                $meta = maybe_unserialize(get_post_meta(get_the_ID(), 'wpeden_post_meta', true));
                                echo wp_oembed_get($meta['videourl'], array('width' => 648)); ?>

                            </div>
                        <?php } else if (get_post_format() == 'gallery') { ?>

                            <?php centrino_post_gallery(900, 0); ?>

                        <?php } ?>
                        <?php if (get_post_format() == '') centrino_post_thumb(array(900, 0), true, array('class' => 'thumbnail'));
                        $previews = get_post_meta(get_the_ID(), '__wpdm_additional_previews', true);

                        if (is_array($previews) && count($previews) > 0) {

                            ?>
                            <div class="well">
                                <div class="pull-left more-previews">
                                    <?php

                                    foreach ($previews as $preview) {
                                        echo "<a class='more_previews_a' href='{$preview}'><img src='" . wpdm_dynamic_thumb($preview, array(40, 40)) . "' /></a> ";
                                    }
                                    ?>
                                    <div class="clear"></div>
                                </div>
                                <?php if (!isset($pack['base_price']) || $pack['base_price'] == 0) { ?>
                                    <div class="pull-right qdl">
                                        <?php echo $pack['download_link']; ?>
                                    </div>
                                <?php } ?>
                                <div class="clear"></div>
                            </div>
                        <?php } ?>
                        <h1 class="entry-title"><?php the_title(); ?></h1>

                        <div class="entry-content">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#desc" data-toggle="tab">Description</a></li>
                                <li><a href="#afiles" data-toggle="tab">Attached Files</a></li>
                                <?php if (function_exists('wpdm_reviews')) { ?>
                                    <li><a href="#reviews" data-toggle="tab">Reviews</a></li>
                                <?php } ?>
                                <li><a href="#related" data-toggle="tab">Related Downloads</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="desc" class="tab-pane active">
                                    <?php if (get_post_format() == 'audio') echo do_shortcode('[audio]'); ?>
                                    <?php

                                    the_content();


                                    ?>
                                </div>
                                <div id="afiles" class="tab-pane">
                                    <?php
                                    echo \WPDM\libs\FileList::Box($pack);
                                    ?>
                                </div>
                                <?php if (function_exists('wpdm_reviews')) { ?>
                                    <div id="reviews" class="tab-pane">
                                        <?php echo wpdm_reviews(get_the_ID()); ?>
                                    </div>
                                <?php } ?>
                                <div id="related" class="tab-pane">
                                    <?php echo wpdm_similar_packages(get_the_ID(), 8); ?>
                                </div>
                            </div>
                        </div>
                        <?php wp_link_pages(); ?>


                    </div>
                    <div class="mx_comments">
                        <?php comments_template(); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="sidebar">
                <div class="panel panel-default">

                    <div class="panel-heading">Package Info</div>


                            <ul class="list-group">
                            <li class="list-group-item">
                                <span  class="badge"><?php echo  wpdm_package_size(get_the_ID()); ?></span>
                                <?php _e('File Size', 'crypton'); ?>
                            </li>
                            <li class="list-group-item">
                                <span  class="badge"><?php echo wpdm_package_filecount(get_the_ID()); ?></span>
                                <?php _e('Number of Files', 'crypton'); ?>
                            </li>
                            <li class="list-group-item">
                                <span  class="badge"><?php echo (int)$pack['download_count']; ?> <?php _e("time(s)", 'crypton'); ?></span>
                                <?php _e('Downloaded', 'crypton'); ?>
                            </li>
                            <li class="list-group-item">
                                <span  class="badge"><?php echo get_the_date(); ?></span>
                                <?php _e('Published on', 'crypton'); ?>
                            </li>
                            <li class="list-group-item">
                                <span  class="badge"><?php echo wpdm_package_filetypes(get_the_ID()); ?></span>
                                <?php _e('File Types', 'crypton'); ?>
                            </li>
                            </ul>

                        <?php if (!isset($pack['base_price']) || $pack['base_price'] == 0) { ?>
                            <div class="text-center">
                                <?php echo $pack['download_link']; ?>
                            </div>
                        <?php } ?>

                </div>

                <?php if (isset($pack['base_price']) && $pack['base_price'] > 0) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading"><?php _e("Purchase", "wpdmtheme"); ?></div>
                        <div class="panel-body">
                            <?php echo $pack['download_link']; ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="panel panel-default">
                    <div class="panel-heading">Share</div>
                    <div class="panel-body share">
                        <a target="_blank" onclick="javascript:window.open(this.href,
  '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"
                           href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink(get_the_ID())); ?>"
                           class="btn btn-primary"><i class="fa fa-facebook"></i></a>
                        <a href="https://plus.google.com/share?url=<?php echo urlencode(get_permalink(get_the_ID())); ?>"
                           onclick="javascript:window.open(this.href,
  '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"
                           class="btn btn-danger"><i class="fa fa-google-plus"></i></a>
                        <a href="https://twitter.com/intent/tweet?text=<?php urlencode(the_title()); ?>&url=<?php echo urlencode(get_permalink(get_the_ID())); ?>"
                           class="btn btn-primary"><i class="fa fa-twitter"></i></a>
                        <a href="http://www.pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink(get_the_ID())); ?>&text=<?php urlencode(the_title()); ?>&media=<?php echo urlencode(centrino_post_thumb_url(array(900, 0))); ?>"
                           onclick="javascript:window.open(this.href,
  '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"
                           class="btn btn-danger"><i class="fa fa-pinterest"></i></a>
                        <a href="mailto:&subject=Download <?php the_title(); ?>&msg=Download <?php the_title(); ?>: <?php echo urlencode(get_permalink(get_the_ID())); ?>"
                           onclick="javascript:window.open(this.href,
  '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="btn btn-success"><i
                                class="fa fa-envelope"></i></a>
                        <a href="" onclick="javascript:window.open(this.href,
  '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="btn btn-primary"><i
                                class="fa fa-linkedin"></i></a>

                        <div class="clear"></div>
                    </div>
                </div>

                <div class="panel panel-default panel-author">
                    <div class="panel-heading">Author</div>
                    <div class="panel-body">
                        <div class="media">
                            <div class="pull-left">
                                <?php echo get_avatar(get_the_author_meta('ID'), 60); ?>
                            </div>
                            <div class="media-body">
                                <h3 class="media-heading"><?php echo ucwords(get_the_author_meta('display_name')); ?></h3>

                                <button class="btn btn-sm btn-success" id="recommend"><i class="fa fa-thumbs-up"></i>
                                    Recommend <span
                                        class="label label-white"><?php echo crypton_author_recommends($post->post_author); ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="thumbnail navg">

                <?php $ppost = get_previous_post();
                $npost = get_next_post(); ?>

                <div class="col-md-6">
                    <a class="btn btn-info btn-block btn-lg" href="<?php echo get_permalink($ppost->ID); ?>"><i
                            class="fa fa-angle-left"></i> <?php _e('Previous', 'crypton'); ?></a>&nbsp;
                </div>
                <div class="col-md-6">
                    <a class="btn btn-warning btn-block btn-lg"
                       href="<?php echo get_permalink($npost->ID); ?>"><?php _e('Next', 'crypton'); ?> <i
                            class="fa fa-angle-right"></i></a>
                </div>
                <div class="clear"></div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Tags</div>
                <div class="panel-body">
                    <ul>
                        <li><?php the_tags('', '</li><li>'); ?></li>
                    </ul>
                    <div class="clear"></div>
                </div>
            </div>





            <?php dynamic_sidebar('Single Post'); ?>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">

    jQuery(function ($) {
        $('#recommend').click(function () {
            jQuery('#recommend span.label').load('<?php echo site_url('/?action=__wpdm_rec&author='.$post->post_author); ?>');
        });
    });

</script>

<?php get_footer(); ?>
