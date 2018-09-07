<?php
/*
Template Name: Homepage
*/
if (!defined('ABSPATH')) exit;

define('THENEXT_HIDE_PAGE_HEADER',1);

get_header();

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php get_template_part('homepage-blocks/new-items'); ?>
            <?php get_template_part('homepage-blocks/featured-pages'); ?>
            <?php get_template_part('homepage-blocks/popular-products'); ?>
            <?php get_template_part('homepage-blocks/client-reviews'); ?>
        </div>
    </div>
</div>














<?php get_footer(); ?>
