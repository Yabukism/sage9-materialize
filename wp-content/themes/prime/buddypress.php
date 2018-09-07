<?php
/**
 * The template for displaying all BuddyPress pages
 *
 */
//define('THENEXT_HIDE_PAGE_HEADER', 1);
get_header();

?>

<div class="container">
    <div class="row">

        <div class="col-md-12">
            <div id="buddypress-page" class="single-page">

                <?php while (have_posts()): the_post(); ?>

                    <div <?php post_class('post'); ?>>
                        <div class="clear"></div>
                        <?php do_action("thenext_before_content"); ?>
                        <div class="entry-content">
                            <?php wpeden_post_thumb(array(1100, 0), true, array('class' => 'single-page-thumbnail')); ?>
                            <?php the_content(); ?>
                        </div>
                        <?php wp_link_pages(); ?>
                        <?php do_action("thenext_after_content"); ?>
                    </div>
                    <div class="mx_comments">
                        <?php comments_template(); ?>
                    </div>

                <?php endwhile; ?>

            </div>
        </div>


    </div>
</div>

<style>
    #buddypress-page{
        margin-top: -40px;
    }
    .page-header-bottom,
    .page-heading-main{
        display: none;
    }
    #buddypress div#subnav.item-list-tabs {
        background: rgba(0, 0, 0, 0.05) !important;
        margin: 10px 0 30px;
        overflow: hidden;
        padding: 10px;
    }
    #buddypress div.item-list-tabs ul li a span{
        float: right;
        font-size: 50%;
        margin-top: 1px;
        margin-left: 6px;
    }
    #buddypress div.item-list-tabs ul li.current a, #buddypress div.item-list-tabs ul li.selected a{
        background: transparent;
        opacity: 1;
    }
</style>
<?php
get_footer();
