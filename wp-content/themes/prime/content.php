<article <?php post_class('post archive-post text-center'); ?>>
    <div class="post-cat"><?php the_category('<span> / </span>'); ?></div>
    <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <div class="post-date"><?php echo get_the_date(); ?></div>
        <a class="thumbnail" href="<?php the_permalink(); ?>"><?php wpeden_post_thumb(array(900, 400), true); ?></a>


    <div class="post-content text-left">
        <?php wpeden_post_excerpt(200); ?>
    </div>
    <!-- /.post-content -->

</article>