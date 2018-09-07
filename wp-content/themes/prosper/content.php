<div class="wowitembox col-md-6">
    <div class="wowitemboxinner">
        <div class="imagearea">
            <?php wpeden_post_thumb(array(900, 500), true); ?>
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
            <a href="<?php the_permalink(); ?>" title="Learning Marketing">
                <h2><?php the_title(); ?></h2>
            </a>
            <span class="description"><?php wpeden_post_excerpt(200); ?></span>
            <div class="pull-right text-info"><?php echo get_the_date(); ?></div>
            <a class="readmore" href="<?php the_permalink(); ?>">Read More</a>
        </div>
    </div>
</div>