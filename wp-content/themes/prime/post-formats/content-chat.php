

<div  <?php post_class('media'); ?>>
    <a href="<?php the_permalink(); ?>" class="pull-left">
        <?php wpeden_post_thumb(array(200,200)); ?>
    </a>
    <div class="entry-content media-body">
        <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>&nbsp;</h2>
        <?php the_excerpt(); ?>
    </div>
    <div class="clear"></div>
    <div class="breadcrumb"><?php _e('Posted on', 'the-next'); ?> <?php echo get_the_date(); ?> / <?php _e('Posted by', 'the-next'); ?> <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author(); ?></a> / <a href="<?php the_permalink();?>"><?php _e('read more', 'the-next'); ?> &#187;</a></div>
</div>