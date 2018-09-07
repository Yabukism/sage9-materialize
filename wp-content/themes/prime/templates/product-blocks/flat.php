<!-- WPDM Link Template: Prime -->
<div class="col-md-4 recent_item">
    <div class="product-block">


        <div class="panel panel-default">
            <div class="panel-body">
                <div class="price-tag-cont">
                    <div class="price-tag">
                        <div
                            class="price-label"><?php echo wpdmpp_product_price(get_the_ID()) > 0 ? __('Buy  Now @', 'optimus') : __('Download', 'optimus'); ?></div>
                        <?php echo wpdmpp_product_price(get_the_ID()) > 0 ? wpdmpp_currency_sign() . wpdmpp_product_price(get_the_ID()) : __('Free', 'optimus'); ?>
                    </div>
                </div>
                <a href="<?php the_permalink(); ?>"><?php wpeden_post_thumb(array(400, 300), true, array('class' => 'no-radius')); ?></a>

            </div>

            <div class="panel-footer">
                <div class="row"><div class="col-md-12">
                        <h3 class="text-left"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    </div></div>
                <div class="row">
                    <div class="col-md-6"><i class="fa fa-server color-green"></i> <?php echo get_post_meta(get_the_ID(), '__wpdm_package_size', true); ?> </div>
                    <div class="col-md-6"><i class="fa fa-calendar color-blue"></i> <?php echo get_the_date(); ?> </div>

                </div>
                <div class="row">
                    <div class="col-md-6"><?php the_author(); ?></div>
                    <div class="col-md-6"><a class="color-green" href="<?php the_permalink(); ?>">More Details</a></div>
                </div>
            </div>

        </div>

    </div>
</div>
