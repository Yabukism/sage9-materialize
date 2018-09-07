<div class="wowitembox col-md-4">
<div class="wowitemboxinner">
    <div class="imagearea">
        <?php wpdm_post_thumb(array(400, 250)); ?>
        <div class="caption">
            <div class="blur">
            </div>
            <div class="caption-text">
                <div class="captionbuttons">
                    <a href="<?php the_permalink(); ?>" class="captiondetails"><i class="fa fa-link"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="notesarea">
        <a href="<?php the_permalink(); ?>">
            <h2><?php the_title(); ?></h2>
        </a>
        <div class="description">

            <?php wpeden_post_excerpt(60); ?>

        </div>
        <hr class="sap"/>
        <div class="notesbottom variable row bx-footer">
            <div class="price col-xs-7">
                <?php echo wpdmpp_price_range(get_the_ID()); ?>
            </div>
            <div class="cart  col-xs-5 text-right">
                <?php echo wpdmpp_waytocart($post, 'btn-sm btn-success'); ?>
            </div>

        </div>
    </div>
</div>
</div>