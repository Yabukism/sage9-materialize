
<?php
$params = array(
    "items_per_page"=> wpeden_get_theme_opts('slides',5),
    "post_type"=>"wpdmpro",
    "tax_query"=>array(array(
        'taxonomy' => 'wpdmcategory',
        'field' => 'id',
        'terms' => array(wpeden_get_theme_opts('slider_category'))
    ))
);
$featured_products = get_posts($params);

?>

<div class="container"><div class="row"><div class="col-md-12">
            <div id="da-slider" class="da-slider">
                <?php foreach($featured_products as $product): ?>
                    <div class="da-slide">
                        <h2><a href="<?php echo get_permalink($product->ID); ?>" class="font-effect-shadow-multiple"><?php echo  esc_attr($product->post_title); ?></a></h2>
                        <p>
                            <?php echo  centrino_get_excerpt($product,100); ?>

                        </p>
                        <div class="da-link">
                            <?php //echo "<div class='pull-left da-pricing'>Price: $12.00</div>"; ?>
                            <a class="btn btn-bordered" href="<?php echo get_permalink($product->ID);?>">Continue to Download...</a>
                        </div>
                        <div class="da-img">
                            <?php centrino_thumb($product, array(350,300)); ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <nav class="da-arrows">
                    <span class="da-arrows-prev"></span>
                    <span class="da-arrows-next"></span>
                </nav>
            </div>
        </div></div></div>
<script>
    jQuery(function($) {

        $('#da-slider').cslider({
            autoplay	: true,
            bgincrement	: 450
        });

    });
</script>
