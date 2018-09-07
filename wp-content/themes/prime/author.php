<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 */

get_header();

?>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php echo do_shortcode('[wpdm_user_profile items_per_page=12 template="profile-block.php"]'); ?>
            </div>
        </div>
    </div>


<?php
get_footer();


