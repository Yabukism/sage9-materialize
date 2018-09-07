<?php 
/*
Template Name: Full Width 
*/
get_header();
 ?>

<div class="container">
<div class="row">


 


<div class="col-md-12">
<div  id="single-post post-<?php the_ID(); ?>" <?php post_class(); ?>> 
<?php 

while(have_posts()): the_post(); ?>
 
<div <?php post_class('post'); ?>>
<div class="clear"></div>
<h1 class="entry-title"><?php the_title(); ?></h1>
<div class="entry-content">
<?php the_content(); ?>
</div>
<?php wp_link_pages( ); ?>
</div>

<?php endwhile; ?>
</div>
</div>

</div>
</div>


         

<?php get_footer(); ?>
