<?php if(WPEdenThemeEngine::NextGetOption('thenext_disable_categories_section', 0) == 0): ?>
<section class="category-area" id="category-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="media">
                    <div class="pull-left">
                        <img style="width: 30px" src="https://cdn4.iconfinder.com/data/icons/technology-devices-1/500/shared-folder_network-32.png" />
                    </div>
                    <div class="media-body">
                        <h2><?php echo WPEdenThemeEngine::NextGetOption('thenext_categories_section_heading', 'Explore Categories'); ?></h2>
                    </div>
                </div><br/>
            </div>
            <?php for($i = 1; $i<=3; $i++):
                $cat = get_term(WPEdenThemeEngine::NextGetOption('thenext_explore_category_'.$i, 'Explore Categories'))

                ?>
            <div class="col-md-4">
                <div class="panel panel-featured panel-category">
                    <div class="panel-heading"><?php echo $cat->name; ?></div>

                    <?php
                    $params = array('posts_per_page' => 4, 'post_type'=> 'wpdmpro', 'tax_query' => array( array('taxonomy' => 'wpdmcategory', 'field'    => 'id', 'terms'    => $cat->term_id) ));
                    $q = new WP_Query($params);
                    while($q->have_posts()){ $q->the_post();
                        ?>
                        <div class="col-md-6 featured-item-block">
                            <div class="single-item">
                                <a href="<?php the_permalink(); ?>" class="title">
                                    <?php wpdm_post_thumb(array(400,250)); ?>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="clear"></div>
                    <div class="panel-footer">
                        <a href="<?php echo get_term_link($cat); ?>" class="ui button mini blue no-radius pull-right"><?php _e('Explore', 'prime'); ?></a>
                        <span class="ui button mini yellow no-radius"><?php echo sprintf(__("%d Items", "prime"), $cat->count); ?></span>
                    </div>
                </div>
            </div>
            <?php endfor; ?>


        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-featured">
                    <div class="panel-heading"><?php echo WPEdenThemeEngine::NextGetOption('thenext_all_categories_heading', 'All Categories'); ?></div>
                    <?php
                    $cats = get_terms("wpdmcategory");
                    foreach($cats as $cat){ ?>

                        <div class="col-md-3 featured-item-block">
                            <a class="category-link" href="<?php echo get_term_link($cat); ?>">
                                <img src="<?php echo \WPDM\libs\CategoryHandler::icon($cat->term_id); ?>" style="width: 20px;margin-right: 5px" />
                                <?php echo $cat->name; ?>
                            </a>
                        </div>

                    <?php }
                    ?>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif;