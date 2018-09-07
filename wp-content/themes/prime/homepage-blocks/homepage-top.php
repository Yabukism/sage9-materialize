<section class="header-area-home" id="header-area-home">
    <?php

$params = array(
    "posts_per_page" => intval(WPEdenThemeEngine::NextGetOption('home_slide_count_o', 3)),
    "post_type" => 'wpdmpro',
    "tax_query" => array(array(
        'taxonomy' => 'wpdmcategory',
        'field' => 'term_id',
        'terms' => array(intval(WPEdenThemeEngine::NextGetOption('home_slider_category_o')))
    ))
);

$posts = get_posts($params);
$first = 1;
$i = 0;

?>
<div id="featured-slider-container">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="header-call text-center">
                    <h1>Create an Awesome Digital Marketplace<br/> with WordPress Download Manager. </h1>
                    Prime will give you easier way then ever!

                    <form><input type="hidden" name="post_type" value="wpdmpro"><input id="home-search-input" placeholder="Search..." name="s" class="form-control input-lg" type="text"></form>
                </div>
            </div>
        </div>
    </div>

    <div class="popular-search text-center">
        <?php _e('Popular Tags:','prime'); ?>
        <?php $tags = get_tags(array('number' => 4)); ?>
        <?php foreach ($tags as $tag){

            $tag_link = get_tag_link( $tag->term_id );

            echo "<a href='{$tag_link}' title='{$tag->name} Tag' class='btn btn-flat btn-xs btn-default no-radius {$tag->slug}'>{$tag->name}</a> ";


            } ?>

    </div>

</div>
</section>