<!-- Section Recent Products
					==========================================-->
<?php if(WPEdenThemeEngine::NextGetOption('thenext_new_products_hide', 0) == 0): ?>
<section class="prosper homerecentproducts">
    <div class="clear text-center">
        <div class="separatr">
            <h2 class="maintitle"><?php echo WPEdenThemeEngine::NextGetOption('thenext_new_products_section_heading', 'New Products'); ?> <a href="<?php echo get_permalink(WPEdenThemeEngine::NextGetOption('thenext_new_products_url', 'New Products')); ?>" class="view-all"><span><?php _e('Browse all','prosper'); ?></span></a>
            </h2>
        </div>
    </div>
    <div class="sectionlatestitems clear">
        <?php
            $query = new WP_Query(array('post_type' => 'wpdmpro', 'post_status' => 'publish', 'posts_per_page' => 9));
            while ($query->have_posts()){
        $query->the_post();
                get_template_part("templates/product-blocks/flat");
        }
        ?>
    </div>
    <!-- .wowitemboxlist-->
</section>
<?php endif;