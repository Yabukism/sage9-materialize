<?php



get_header();

?>
<br/>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php echo do_shortcode('[wpdm_user_profile items_per_page=12 template="profile-block.php"]'); ?>
            </div>
        </div>
    </div>


<?php
get_footer();


