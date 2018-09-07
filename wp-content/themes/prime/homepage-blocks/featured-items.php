<?php if(WPEdenThemeEngine::NextGetOption('thenext_disable_featured_section', 0) == 0): ?>
<section class="featured-content-area" id="featured-content-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-featured">
                    <div class="panel-heading"><?php echo WPEdenThemeEngine::NextGetOption('home_featured_heading_o', 'Featured Items'); ?></div>

                    <?php
                    $params = array('posts_per_page' => 8, 'post_type'=> 'wpdmpro');
                    if(WPEdenThemeEngine::NextGetOption('home_featured_category_o', 0) > 0){
                        $params['tax_query'] = array( array('taxonomy' => 'wpdmcategory', 'field'    => 'id', 'terms'    => WPEdenThemeEngine::NextGetOption('home_featured_category_o')));
                    }

                    $q = new WP_Query($params);
                    while($q->have_posts()){ $q->the_post();
                        ?>
                        <div class="col-md-3 col-sm-6 col-xs-12 featured-item-block">
                            <div class="single-item">
                                <a href="<?php the_permalink(); ?>" class="title">
                                    <?php wpdm_post_thumb(array(400,250)); ?>
                                    <?php the_title(); ?>
                                </a>

                                <div class="meta">
                                    <span class=" no-radius ui button mini blue btn btn-xs no-radius"><i class="tn-download"></i> <?php echo get_post_meta(get_the_ID(),'__wpdm_download_count', true); ?></span>
                                    <span class=" no-radius ui button mini green btn btn-xs no-radius"><?php echo function_exists('wpdmpp_effective_price') && wpdmpp_effective_price(get_the_ID()) > 0?wpdmpp_currency_sign().wpdmpp_effective_price(get_the_ID()):'Free'; ?></span>
                                    <a href="<?php the_permalink(); ?>" class=" no-radius ui button mini red btn btn-xs no-radius"><?php _e('Download','prime'); ?></a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>