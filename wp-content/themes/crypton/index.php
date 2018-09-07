<?php get_header(); ?>

<div class="container">
<div class="row">
<div class="col-md-8">
      
<?php get_template_part('loop'); ?>

</div>
<div class="col-md-4">

<?php dynamic_sidebar('Archive Page'); ?>
</div>
</div>
</div>
      
<?php get_footer(); ?>
