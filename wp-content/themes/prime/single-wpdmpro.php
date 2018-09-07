<?php get_header();

$sidebarw = 3;
$bodyw = 12-$sidebarw;

$_wpdm_hide_all = get_option('_wpdm_hide_all', 0);


?>

<div class="container">
    <?php the_content(); ?>
    <div class="row mx_comments">
    <?php comments_template(); ?>
    </div>
</div>


<?php get_footer(); ?>
