<?php $theme = 'dark'; ?>
<!-- light/dark/bar/default -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/modules/post-slider/nivo-slider/themes/<?php echo $theme; ?>/<?php echo $theme; ?>.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/modules/post-slider/nivo-slider/nivo-slider.css" type="text/css" media="screen" />
<div class="container"><div class="row"><div class="col-md-12">
        <div class="slider-wrapper theme-<?php echo $theme; ?>">
            <div id="slider" class="nivoSlider">
                <?php $n=0;
                $parmas = array(
                        'post_type' => 'wpdmpro',
                        'showposts' => 4,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'wpdmcategory',
                                'terms' => wpeden_get_theme_opts('slider_category'),
                                'field' => 'term_id',
                            )
                        ),
                        'orderby' => 'title',
                        'order' => 'ASC' );
                $qry = new WP_Query($parmas);
                while($qry->have_posts()){ $qry->the_post();

                ?>
                    <a href="<?php the_permalink(); ?>"><?php  centrino_post_thumb(array(1100, 500), true,array('title'=>'#slide'.$n++)); ?></a>

                <?php

                }
                ?>

            </div>
            <?php $n=0;$z=0;
            $qry = new WP_Query($parmas);
            while($qry->have_posts()){ $qry->the_post();

            ?>
                <div class="scap nivo-html-caption" id="slide<?php echo $n++; ?>">
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p><?php echo centrino_get_excerpt($post, 100); ?></p>
                </div>
            <?php

            }
            ?>

        </div>
</div></div></div>
    <script type="text/javascript" src="<?php echo get_template_directory_uri();?>/modules/nivo-slider/jquery.nivo.slider.js"></script>
    <script type="text/javascript">
    jQuery(window).load(function() {
        jQuery('#slider').nivoSlider({
            pauseTime: 5000,
            beforeChange:function(){
            jQuery('.nivo-caption').removeClass('fadeInUp').addClass('animated fadeOutDown');
            },
            afterChange:function(){
            jQuery('.nivo-caption').removeClass('fadeOutDown').addClass('fadeInUp');
            }
        });
    });
    </script>