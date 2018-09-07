<?php

if ( !defined('ABSPATH')) exit;

get_header();

?>

<div class="container home-content taxonomy-wpdmcategory">

    <div class="row">
        <?php
        $count = 0;
        while(have_posts()): the_post();
        global $post;
            $_wpdm_hide_all = get_option('_wpdm_hide_all', 0);
            $price_or_free = wpdmpp_effective_price(get_the_ID());
            $buy_or_download_label = $price_or_free > 0?'Buy Now @':'Download';
            $price_or_free = $price_or_free > 0?wpdmpp_currency_sign().$price_or_free:'Free';
            $file_size = wpdm_package_size(get_the_ID());
            $view_count = get_post_meta(get_the_ID(),'__wpdm_view_count', true);

            $author = get_user_by('id', $post->post_author);
            $author_name = $author->display_name;
            $author_profile_url = get_author_posts_url($post->post_author);
            $avatar_url = get_avatar_url($author->user_email);
            $author_package_count = count_user_posts( $post->post_author , "wpdmpro"  );


            get_template_part("templates/product-blocks/flat");
            if (++$count % 3 == 0) echo "<div class='clear'></div>";

        endwhile; ?>
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
