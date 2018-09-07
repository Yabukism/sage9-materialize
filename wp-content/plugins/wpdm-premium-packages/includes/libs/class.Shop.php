<?php
/**
 * User: shahnuralam
 * Date: 12/10/16
 * Time: 7:36 PM
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class WPDMPP_Shop
{

    function __construct()
    {
        add_action( 'init', array($this, 'postType'), 1 );
        add_action( 'admin_init', array($this, 'metaBoxes'), 1 );
        add_action( 'save_post', array($this, 'saveStore'));
    }

    function postType(){
        $labels = array(
            'name' => __('Shops', 'wpdmpro'),
            'singular_name' => __('Shop', 'wpdmpro'),
            'add_new' => __('Create New', 'wpdmpro'),
            'add_new_item' => __('Create New Shop', 'wpdmpro'),
            'edit_item' => __('Edit Shop', 'wpdmpro'),
            'new_item' => __('Create Shop', 'wpdmpro'),
            'all_items' => __('All Shops', 'wpdmpro'),
            'view_item' => __('View Shop', 'wpdmpro'),
            'search_items' => __('Search Shops', 'wpdmpro'),
            'not_found' => __('No Shop Found', 'wpdmpro'),
            'not_found_in_trash' => __('No Shops found in Trash', 'wpdmpro'),
            'parent_item_colon' => '',
            'menu_name' => __('Shops', 'wpdmpro')

        );

        $tslug = get_option('__wpdm_surl_base', 'shop');
        if(!strpos("_$tslug", "%"))
            $slug = sanitize_title($tslug);
        else
            $slug = $tslug;
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => 1,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'query_var' => true,
            'rewrite' => array('slug' => $slug, 'with_front' => (bool)get_option('__wpdm_purl_with_front', false)), //get_option('__wpdm_purl_base','download')
            'capability_type' => 'post',
            'has_archive' => (get_option('__wpdm_has_archive', false)==false?false:sanitize_title(get_option('__wpdm_archive_page_slug', 'all-downloads'))),
            'hierarchical' => false,
            'taxonomies' => array('post_tag'),
            'menu_icon' => 'dashicons-store',
            'exclude_from_search' => (bool)get_option('__wpdm_exclude_from_search', false),
            'supports' => array('title', 'editor','author')

        );


        register_post_type('shop', $args);
    }

    function metaBoxes()
    {

        $meta_boxes = array(
            'shop-info' => array('title' => __('Shop Info', "wpdmpro"), 'callback' => array($this, 'shopInfo'), 'position' => 'normal', 'priority' => 'core'),

        );


        $meta_boxes = apply_filters("wpdm_shop_meta_box", $meta_boxes);
        foreach ($meta_boxes as $id => $meta_box) {
            extract($meta_box);
            if(!isset($position)) $position = 'normal';
            if(!isset($priority)) $priority = 'core';
            add_meta_box($id, $title, $callback, 'shop', $position, $priority);
        }
    }

    function shopInfo(){
        $store = get_post_meta(get_the_ID(), '__wpdm_store', true);
        ?>
        <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/bootstrap/css/bootstrap.css" >
        <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/font-awesome/css/font-awesome.min.css" >
        <div class="w3eden">
        <div class="form-group">
            <label for="store-logo"><?php _e('Store Logo URL','wpdm-premium-packages'); ?></label>
            <div class="input-group">
                <input type="text" name="__wpdm_store[store_logo]" id="store-logo" class="form-control" value="<?php echo isset($store['store_logo']) ? $store['store_logo'] : ''; ?>"/>
                <span class="input-group-btn">
                        <button class="btn btn-default btn-media-upload" type="button" rel="#store-logo"><i class="fa fa-picture-o"></i></button>
                    </span>
            </div>
        </div>
        <div class="form-group">
            <label for="store-banner"><?php _e('Store Banner URL','wpdm-premium-packages'); ?></label>
            <div class="input-group">
                <input type="text" name="__wpdm_store[store_banner]" id="store-banner" class="form-control" value="<?php echo isset($store['store_banner']) ? $store['store_banner'] : ''; ?>"/>
                <span class="input-group-btn">
                        <button class="btn btn-default btn-media-upload" type="button" rel="#store-banner"><i class="fa fa-picture-o"></i></button>
                    </span>
            </div>
        </div>
        </div>

        <?php
    }

    function saveStore($post){
        if(get_post_type() == 'shop' && isset($_POST['__wpdm_store']) && current_user_can('edit_posts', $post)){
            update_post_meta($post, '__wpdm_store', $_POST['__wpdm_store']);
        }
    }

}

//new WPDMPP_Shop();