<?php if(WPEdenThemeEngine::NextGetOption('thenext_disable_clients_review_section', 0) == 0): ?>
<section class="client-reviews" id="client-reviews">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="media">
                    <div class="pull-left">
                        <img style="width: 30px" src="https://cdn1.iconfinder.com/data/icons/business-13/6144/18-64.png" />
                    </div>
                    <div class="media-body">
                        <h2><?php echo WPEdenThemeEngine::NextGetOption('home_optimus_review_title', 'Customer Reviews'); ?></h2>
                        <?php echo WPEdenThemeEngine::NextGetOption('home_optimus_review_desc','Our Customers Think, We Are Awesome:'); ?>
                    </div>
                </div><br/>
            </div>

            <?php
            $q = new WP_Query('post_type=review&posts_per_page=3');
            while($q->have_posts()):
                $q->the_post();
                $meta = get_post_meta(get_the_ID(), 'wpeden_post_meta', true);
                ?>
                <div class="col-md-4">
                    <div class="speach">
                        <strong><?php the_title(); ?></strong><br/>
                        <?php the_content(); ?>
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
            <?php endwhile; ?>


        </div>
    </div>
</section>
<?php endif;