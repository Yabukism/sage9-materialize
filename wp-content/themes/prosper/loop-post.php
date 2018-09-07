<?php
do_action("thenext_before_loop");
global $wp_query;
$wp_query->set('post_type', 'post');

while ($wp_query->have_posts()): $wp_query->the_post();
    ?>

        <?php get_template_part("content", get_post_format()); ?>

<?php endwhile; ?>

<?php
global $wp_query;
if ($wp_query->max_num_pages > 1) :
    ?>
    <div class="clear"></div>
    <div id="nav-below" class="navigation post box arc">
        <?php get_template_part('pagination'); ?>
    </div>
<?php endif; ?>
<?php do_action("thenext_after_loop"); ?>
