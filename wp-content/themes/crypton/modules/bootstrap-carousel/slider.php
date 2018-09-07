<div class="container"><div class="row"><div class="col-md-12">
<div id="hCarousel" class="carousel slide nomargin">
   <!-- <ol class="carousel-indicators">
        <?php /*$n=0; foreach($slides as $slide): */?>
        <li data-target="#myCarousel" data-slide-to="<?php /*echo $n; */?>" <?php /*if($n++==0) echo 'class="active"'; */?>></li>
        <?php /*endforeach; */?>
           </ol>-->
    <!-- Carousel items -->
    <div class="carousel-inner">
        <?php
        global $post;
        $n=0;
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
            $icon = get_post_meta(get_the_ID(), '__wpdm_icon', true);
            if($icon=='') $icon = 'download-manager/file-type-icons/speed_download.png';
        ?>
        <div class="<?php if($n++==0) echo 'active '; ?>item">

            <?php centrino_post_thumb(array(1100, 500)); ?>

        <div class="carousel-caption">
            <img src="<?php echo plugins_url('/'.$icon); ?>" />
            <h2><a href='<?php the_permalink(); ?>'><?php the_title(); ?></a></h2>
            <p><?php centrino_post_excerpt(100); ?></p>
        </div>
        </div>
        <?php } ?>

    </div>
    <!-- Carousel nav -->
    <a class="carousel-control left" href="#hCarousel" data-slide="prev"><i class="icon icon-white icon-angle-left"></i></a>
    <a class="carousel-control right" href="#hCarousel" data-slide="next"><i class="icon icon-white icon-angle-right"></i></a>
</div>
</div></div></div>
<style>

    .carousel-caption{
        opacity: 0;
    }

    div.active .carousel-caption{

    }

</style>

<script>
    jQuery('div.active .carousel-caption').addClass('animated fadeInUp');
    jQuery('.carousel').on('slid.bs.carousel', function () {
        jQuery('div.carousel-caption').removeClass('animated fadeInUp');
        jQuery('div.active .carousel-caption').addClass('animated fadeInUp');
    });


</script>