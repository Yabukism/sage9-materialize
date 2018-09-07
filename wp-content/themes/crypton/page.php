<?php get_header(); ?>

<div class="container">
<div class="row">
<div class="col-md-8">
<div  id="single-post post-<?php the_ID(); ?>" <?php post_class(); ?>> 
<?php 

while(have_posts()): the_post(); ?>
 
<div <?php post_class('post'); ?>>
<div class="clear"></div>
<h1 class="entry-title"><?php the_title(); ?></h1>
<div class="entry-content">
<?php centrino_post_thumb(array(1100,0),true, array('class'=>'img-polaroid')); ?>
<?php the_content(); ?>
</div>
<?php wp_link_pages( ); ?>
</div>

<?php endwhile; ?>
</div>
</div>
<div class="col-md-4">
<div class="sidebar"> 
<?php dynamic_sidebar('Single Post'); ?>
</div>
</div>
</div>
</div>
         

<?php get_footer(); ?>
