<?php if(WPEdenThemeEngine::NextGetOption('thenext_disable_clients_review_section', 0) == 0): ?>
<section class="prosper homepopularproducts client-reviews" id="client-reviews">
    <div class="row text-center seactionitemfeatures">
        <div class="narrowheader">
            <h2 class="maintitle"><?php echo WPEdenThemeEngine::NextGetOption('home_optimus_review_title', 'Customer Reviews'); ?></h2>
            <?php echo WPEdenThemeEngine::NextGetOption('home_optimus_review_desc','Our Customers Think, We Are Awesome:'); ?>
        </div>
    </div>
    <div class="container">
        <div class="row">

            <?php
            $q = new WP_Query('post_type=review&posts_per_page=3');
            while($q->have_posts()):
                $q->the_post();
                $meta = get_post_meta(get_the_ID(), 'wpeden_post_meta', true);
                ?>
                <div class="col-md-4">
                    <div class="speach panel panel-default">
                        <div class="panel-body">
                            <strong><?php the_title(); ?></strong>
                            <hr/>
                            <?php the_content(); ?>
                        </div>
                        <div class="panel-footer">
                        <div class="media person">
                            <div class="pull-left">
                                <img src="<?php echo wpeden_dynamic_thumb($meta['picture'], array(90, 90)); ?>" class="img-circle">
                            </div>
                            <div class="media-body">
                                <b><?php echo $meta['name']; ?></b><br/>
                                <?php echo $meta['designation']; ?>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>


        </div>
    </div>
</section>
<?php endif;