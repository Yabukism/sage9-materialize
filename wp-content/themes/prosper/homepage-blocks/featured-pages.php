<!-- Section Features home_featured_page_1
					==========================================-->
<?php if(WPEdenThemeEngine::NextGetOption('thenext_featured_pages_hide', 0) == 0): ?>
<section class="prosper homefeatures">
    <div class="row text-center seactionitemfeatures">
        <div class="narrowheader">
            <h2 class="maintitle"><?php echo WPEdenThemeEngine::NextGetOption('thenext_featured_pages_section_heading', 'Features Pages'); ?></h2>
            <?php echo WPEdenThemeEngine::NextGetOption('thenext_featured_pages_section_intro', 'Features Pages Section Intro Goes Here'); ?>
        </div>
    </div>
    <div class="autoclear">
        <?php
            for($i = 1; $i <= 4; $i++){
            $page = get_post(WPEdenThemeEngine::NextGetOption('home_featured_page_'.$i, 'Features Pages'));
        ?>
        <div class="col-md-6">
            <div class="featurebox row">
                <div class="col-md-2">
                    <a href="<?php echo get_permalink($page); ?>"><?php wpdm_thumb($page, array(200,200)); ?></a>
                </div>
                <div class="col-md-10">
                    <h2><?php echo $page->post_title; ?></h2>
                    <p>
                        <?php wpeden_get_excerpt($page->ID, 100); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="clear">
        </div>
    </div>
</section>
<?php endif;