<?php
/*
Template Name: Homepage ( Prime )
*/
if (!defined('ABSPATH')) exit;

define('THENEXT_HIDE_PAGE_HEADER',1);

get_header();

?>

<?php get_template_part('homepage-blocks/homepage-top'); ?>
<?php get_template_part('homepage-blocks/featured-items'); ?>
<?php get_template_part('homepage-blocks/featured-stores'); ?>
<?php get_template_part('homepage-blocks/category-area'); ?>
<?php get_template_part('homepage-blocks/client-reviews'); ?>
<?php get_template_part('homepage-blocks/pricing'); ?>












<?php get_footer(); ?>
