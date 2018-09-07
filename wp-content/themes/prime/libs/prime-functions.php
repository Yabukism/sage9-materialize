<?php

//error_reporting(E_ERROR | E_WARNING);
class TheNextChild{

    function __construct(){

        $this->Actions();
        $this->Filters();

    }

    function Actions(){
	    add_action( 'init', array($this, 'RegisterPostTypes'));
	    add_action( 'widgets_init', array($this, 'RegisterSidebars'));
	    add_action( 'admin_enqueue_scripts', array($this, 'AdminEnqueueScripts'));
	    add_action( 'edit_user_profile_update', array($this, 'SaveCustomUserFields') );
	    add_action( 'personal_options_update', array($this, 'SaveCustomUserFields') );
	    add_action( 'user_new_form', array($this, 'CustomUserFields') );
	    add_action( 'show_user_profile', array($this, 'CustomUserFields') );
	    add_action( 'edit_user_profile', array($this, 'CustomUserFields') );
        add_action('thenext_page_header_bottom_left_content', array($this, 'PageHeaderBottomLeft'));
	    add_action( 'wp_enqueue_scripts', array($this, 'EnqueueScripts') );
	    add_action( 'wp_footer', array($this, 'WPFooter') );
    }

    function Filters(){
        //add_filter('page_header_bottom_left_content', array($this, 'SearchPageHeaderBottomLeft'));
        add_filter('thenext_sidebar_styles', array($this, 'SidebarStyles'));
        add_filter('thenext_customizer_panels', array($this, 'CustomHomepageSettingsPanel'));
        add_filter('thenext_customizer_sections', array($this, 'CustomHomepageSettingsSection'));
        add_filter('thenext_customizer_options', array($this, 'CustomHomepageSettingsFields'));
        add_filter('TheNext_MetaBox', array($this, 'MetaBoxes'));
        add_filter('thenext_page_heading_main', array($this, 'BlogTitle'));
        add_filter('wdm_before_fetch_template', array($this, 'TemplateTags'), 99999);
    }

	function AdminEnqueueScripts($hook){
		if(!in_array($hook, array('profile.php','user-new.php', 'post-new.php','post.php')) ) return;
		wp_enqueue_media();
		wp_enqueue_script('media-upload');
		wp_enqueue_style('icons',get_stylesheet_directory_uri().'/fonts/icons/icons.css');
	}

	function EnqueueScripts(){
        wp_dequeue_script('wpdm-bootstrap');
        wp_dequeue_style('wpdm-bootstrap');
		wp_enqueue_style('thenext-child-style',get_stylesheet_directory_uri().'/skins/eden.css', array('bootstrap'));
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-form');
	}

    function RegisterPostTypes(){
        $labels = array(
            'name'               => _x( 'Reviews', 'post type general name', 'eden' ),
            'singular_name'      => _x( 'Review', 'post type singular name', 'eden' ),
            'menu_name'          => _x( 'Reviews', 'admin menu', 'eden' ),
            'name_admin_bar'     => _x( 'Review', 'add new on admin bar', 'eden' ),
            'add_new'            => _x( 'Add New', 'Review', 'eden' ),
            'add_new_item'       => __( 'Add New Review', 'eden' ),
            'new_item'           => __( 'New Review', 'eden' ),
            'edit_item'          => __( 'Edit Review', 'eden' ),
            'view_item'          => __( 'View Review', 'eden' ),
            'all_items'          => __( 'All Reviews', 'eden' ),
            'search_items'       => __( 'Search Reviews', 'eden' ),
            'parent_item_colon'  => __( 'Parent Reviews:', 'eden' ),
            'not_found'          => __( 'No Reviews found.', 'eden' ),
            'not_found_in_trash' => __( 'No Reviews found in Trash.', 'eden' )
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => false,
            'rewrite'            => array( 'slug' => 'review' ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'      => 'dashicons-format-status',
            'supports'           => array( 'title', 'editor' )
        );

        register_post_type( 'review', $args );

        $plabels = array(
            'name'               => _x( 'Pricing Table', 'post type general name', 'eden' ),
            'singular_name'      => _x( 'Pricing Table', 'post type singular name', 'eden' ),
            'menu_name'          => _x( 'Pricing Tables', 'admin menu', 'eden' ),
            'name_admin_bar'     => _x( 'Pricing Table', 'add new on admin bar', 'eden' ),
            'add_new'            => _x( 'Add New', 'Pricing Table', 'eden' ),
            'add_new_item'       => __( 'Add New Pricing Table', 'eden' ),
            'new_item'           => __( 'New Pricing Table', 'eden' ),
            'edit_item'          => __( 'Edit Pricing Table', 'eden' ),
            'view_item'          => __( 'View Pricing Table', 'eden' ),
            'all_items'          => __( 'All Pricing Tables', 'eden' ),
            'search_items'       => __( 'Search Pricing Tables', 'eden' ),
            'parent_item_colon'  => __( 'Parent Pricing Tables:', 'eden' ),
            'not_found'          => __( 'No Pricing Tables found.', 'eden' ),
            'not_found_in_trash' => __( 'No Pricing Tables found in Trash.', 'eden' )
        );

        $pargs = array(
            'labels'             => $plabels,
            'public'             => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => false,
            'rewrite'            => array( 'slug' => 'pricing' ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'      => 'dashicons-media-spreadsheet',
            'supports'           => array( 'title' )
        );

        register_post_type( 'pricingtable', $pargs );
        remove_post_type_support('pricingtable', 'editor');
    }

    function MetaBoxes($metaboxes){
        $metaboxes['ReviewMetaBoxHTML'] = array('title' => 'Client Info', 'callback' => array($this, 'ReviewMetaBoxHTML'), 'position' => 'normal', 'priority' => 'core', 'post_type' => 'review');
        $metaboxes['PricingMetaBoxHTML'] = array('title' => 'Package Features & Pricing', 'callback' => array($this, 'PricingMetaBoxHTML'), 'position' => 'normal', 'priority' => 'core', 'post_type' => 'pricingtable');
        return $metaboxes;
    }

    function ReviewMetaBoxHTML($post){
        $meta = get_post_meta($post->ID, 'wpeden_post_meta', true);
        ?>
        <div class="w3eden">


                    <table class="table table-striped table-bordered">
                        <tr>
                            <th style="max-width: 300px;width: 300px"><label for="faceReview_profile">Name</label></th>
                            <td><input type="text" name="wpeden_post_meta[name]" value="<?php echo isset($meta['name'])?$meta['name']:''; ?>" class="form-control" /></td>
                        </tr>

                        <tr>
                            <th style="max-width: 300px;width: 300px"><label for="faceReview_profile">Designation</label></th>
                            <td><input type="text" name="wpeden_post_meta[designation]" value="<?php echo isset($meta['designation'])?$meta['designation']:''; ?>" class="form-control" /></td>
                        </tr>

                        <tr>
                            <th><label for="__thenext_prifle_pic">Picture</label></th>
                            <td><?php echo self::MediaPicker(array('id'=>'profile-image','name'=>'wpeden_post_meta[picture]','selected'=>isset($meta['picture'])?$meta['picture']:'')); ?></td>
                        </tr>



                    </table>


        </div>
        <?php
    }

    function PricingMetaBoxHTML($post){
        $meta = get_post_meta($post->ID, 'wpeden_post_meta', true);
        ?>
        <style>.ratings.tn-star { font-size: 14pt !important; color: #cccccc; } .ratings.tn-star.text-success { font-size: 14pt !important; color: #38d042; } .rrating input[type=radio]{ margin: -5px 5px 0 10px; } .pclr input[type=radio] { margin: -2px 5px  0 0; } </style>
        <div class="w3eden">


                    <table class="table table-striped table-bordered">

                        <tr>
                            <th style="max-width: 300px;width: 300px"><label for="faceReview_profile">Description</label></th>
                            <td><input type="text" name="wpeden_post_meta[package_desc]" value="<?php echo isset($meta['package_desc'])?$meta['package_desc']:''; ?>" class="form-control" /></td>
                        </tr>

                        <tr>
                            <th style="max-width: 300px;width: 300px"><label for="faceReview_profile">Price</label></th>
                            <td><input type="text" name="wpeden_post_meta[package_price]" value="<?php echo isset($meta['package_price'])?$meta['package_price']:''; ?>" class="form-control" /></td>
                        </tr>

                        <tr>
                            <th style="max-width: 300px;width: 300px"><label for="faceReview_profile">Rating</label></th>
                            <td class="rrating">
                                <label><input type="radio" name="wpeden_post_meta[package_rating]" value="1" <?php echo isset($meta['package_rating']) && $meta['package_rating']==1?'checked=checked':''; ?> /> <i class="ratings tn-star text-success"></i><i class="ratings tn-star"></i>
                                <i class="ratings tn-star"></i> <i class="ratings tn-star"></i> <i class="ratings tn-star"></i></label>
                            <label><input type="radio" name="wpeden_post_meta[package_rating]" value="2" <?php echo isset($meta['package_rating']) && $meta['package_rating']==2?'checked=checked':''; ?> /> <i class="ratings tn-star text-success"></i><i class="ratings tn-star text-success"></i>
                                <i class="ratings tn-star"></i> <i class="ratings tn-star"></i> <i class="ratings tn-star"></i></label>

                            <label><input type="radio" name="wpeden_post_meta[package_rating]" value="3" <?php echo isset($meta['package_rating']) && $meta['package_rating']==3?'checked=checked':''; ?> /> <i class="ratings tn-star text-success"></i><i class="ratings tn-star text-success"></i>
                                <i class="ratings tn-star text-success"></i> <i class="ratings tn-star"></i> <i class="ratings tn-star"></i></label>

           <label><input type="radio" name="wpeden_post_meta[package_rating]" value="4" <?php echo isset($meta['package_rating']) && $meta['package_rating']==4?'checked=checked':''; ?> /> <i class="ratings tn-star text-success"></i><i class="ratings tn-star text-success"></i>
                                <i class="ratings tn-star text-success"></i> <i class="ratings tn-star text-success"></i> <i class="ratings tn-star"></i></label>

           <label><input type="radio" name="wpeden_post_meta[package_rating]" value="5" <?php echo isset($meta['package_rating']) && $meta['package_rating']==5?'checked=checked':''; ?> /> <i class="ratings tn-star text-success"></i><i class="ratings tn-star text-success"></i>
                                <i class="ratings tn-star text-success"></i> <i class="ratings tn-star text-success"></i> <i class="ratings tn-star text-success"></i></label>

                            </td>
                        </tr>

                        <tr>
                            <th style="max-width: 300px;width: 300px"><label for="faceReview_profile">Button Label</label></th>
                            <td><input type="text" name="wpeden_post_meta[package_btnlbl]" value="<?php echo isset($meta['package_btnlbl'])?$meta['package_btnlbl']:''; ?>" class="form-control" /></td>
                        </tr>

                        <tr>
                            <th style="max-width: 300px;width: 300px"><label for="faceReview_profile">Button URL</label></th>
                            <td><input type="text" name="wpeden_post_meta[package_btnurl]" value="<?php echo isset($meta['package_btnurl'])?$meta['package_btnurl']:''; ?>" class="form-control" /></td>
                        </tr>

                        <tr>
                            <th style="max-width: 300px;width: 300px"><label for="faceReview_profile">Color Scheme</label></th>
                            <td class="pclr">
                                <label class="label label-default" style="padding: 6px 10px"><input type="radio" name="wpeden_post_meta[package_color]" value="default" <?php echo isset($meta['package_color']) && $meta['package_color'] =='default'?'checked=checked':''; ?>  /> Default</label>
                                <label class="label label-primary" style="padding: 6px 10px"><input type="radio" name="wpeden_post_meta[package_color]" value="primary" <?php echo isset($meta['package_color']) && $meta['package_color'] =='primary'?'checked=checked':''; ?>  /> Primary</label>
                                <label class="label label-info" style="padding: 6px 10px"><input type="radio" name="wpeden_post_meta[package_color]" value="info" <?php echo isset($meta['package_color']) && $meta['package_color'] =='info'?'checked=checked':''; ?>  /> Info</label>
                                <label class="label label-warning" style="padding: 6px 10px"><input type="radio" name="wpeden_post_meta[package_color]" value="warning" <?php echo isset($meta['package_color']) && $meta['package_color'] =='warning'?'checked=checked':''; ?>  /> Warning</label>
                                <label class="label label-danger" style="padding: 6px 10px"><input type="radio" name="wpeden_post_meta[package_color]" value="danger" <?php echo isset($meta['package_color']) && $meta['package_color'] =='danger'?'checked=checked':''; ?>  /> Danger</label>
                                <label class="label label-success" style="padding: 6px 10px"><input type="radio" name="wpeden_post_meta[package_color]" value="success" <?php echo isset($meta['package_color']) && $meta['package_color'] =='success'?'checked=checked':''; ?>  /> Success</label>
                            </td>
                        </tr>

                        <tr>
                            <th style="max-width: 300px;width: 300px"><label for="faceReview_profile">Features</label></th>
                            <td><textarea type="text" name="wpeden_post_meta[package_features]" class="form-control"><?php echo isset($meta['package_features'])?$meta['package_features']:''; ?></textarea>
                            <em class="note">1 Feature Per Line</em>
                            </td>
                        </tr>


                        <tr>
                            <th><label for="__thenext_prifle_pic">Picture</label></th>
                            <td><?php echo self::MediaPicker(array('id'=>'profile-image','name'=>'wpeden_post_meta[picture]','selected'=>isset($meta['picture'])?$meta['picture']:'')); ?></td>
                        </tr>



                    </table>


        </div>
        <?php
    }

	function WPFooter(){
		?>
		<script>
			jQuery(function($){
				$("#main-nav-container").sticky({topSpacing:<?php echo is_user_logged_in()?32:0; ?>});
				$('#featureCarousel').carousel({
					interval:   4000
				});

				var clickEvent = false;
				$('#featureCarousel').on('click', '.nav a', function() {
					clickEvent = true;
					$('.nav li').removeClass('active');
					$(this).parent().addClass('active');
				}).on('slid.bs.carousel', function(e) {
					if(!clickEvent) {
						var count = $('#featureCarousel .nav').children().length -1;
						var current = $('#featureCarousel .nav li.active');
						current.removeClass('active').next().addClass('active');
						var id = parseInt(current.data('slide-to'));

						if(count == id) {
							$('#featureCarousel .nav li').first().addClass('active');
						}
					}
					clickEvent = false;
				});

                $('#contact-form').submit(function(){
                    $('#btn-submit').attr('disabled','disabled').html('Please Wait...')
                    $(this).ajaxSubmit({
                        success: function(res){
                            $('#btn-submit').html('Message Sent!')
                        }
                    });
                    return false;
                });
			});
		</script>
		<?php
	}

    function RegisterSidebars() {
        register_sidebar( array(
            'name' => __( 'Single Package', 'optimus' ),
            'id' => 'single-package',
            'description' => __( 'Widgets in this area will be shown on product/item details page.', 'optimus' ),
            'before_widget' => '<li id="%1$s" class="widget %2$s">',
            'after_widget'  => '</li>',
            'before_title'  => '<h2 class="widgettitle">',
            'after_title'   => '</h2>',
        ) );
    }

    function SidebarStyles($styles){
        $styles['boxed-panel'] = array(
            'style_name' => 'Boxed Panel',
            'before_widget' => '<div class="widget-boxed-panel">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-boxed-panel-heading widget-title">',
            'after_title' => '</h3>'
        );
        return $styles;
    }

    function PageHeaderBottomLeft(){
        $header_bottom_left_cont = "";
        if (function_exists('yoast_breadcrumb'))
            $header_bottom_left_cont =  yoast_breadcrumb('<a>', '</a>', false);
        echo apply_filters('page_header_bottom_left_content',$header_bottom_left_cont);
    }

    function SearchPageHeaderBottomLeft($header_bottom_left_cont = null){

        if(!is_search()) return $header_bottom_left_cont;

        ob_start();

        $all_post_types = get_post_types('','objects');
        $search_post_types = WPEdenThemeEngine::GetOption('search_post_types', array());
        $current_post_type = isset($_GET['post_type']) && post_type_exists($_GET['post_type'])?$_GET['post_type']:'post';

        ?>
        <ul class="nav nav-pills pills-post-type">

            <?php foreach($search_post_types as $post_type){ ?>

                <li class='<?php echo $post_type == esc_attr(get_query_var('post_type')) || ( $current_post_type =='' && $post_type == 'post' )?'active':''; ?>' ><a href="<?php echo home_eden_url("/?post_type={$post_type}&s=".get_query_var('s')); ?>"><?php echo $all_post_types[$post_type]->labels->name; ?></a></li>

            <?php } ?>

        </ul>
        <?php
        $cont = ob_get_clean();
        return $cont;
    }

    function CustomHomepageSettingsPanel($thenext_panels){
        $thenext_panels['thenext_o_homepage_options'] = array(
            'title' => __('Homepage (Prime)', 'the-next'),
            'description' => '',
            'priority' => 4,
        );
        return $thenext_panels;
    }
    function CustomHomepageSettingsSection($thenext_sections){
        $thenext_sections['thenext_home_slider_o'] = array(
            'title' => __('Slider', 'the-next'),
            'description' => '',
            'panel' => 'thenext_o_homepage_options',
            'priority' => 118,
        );
        $thenext_sections['thenext_featured_category_o'] = array(
            'title' => __('Featured Category', 'the-next'),
            'description' => '',
            'panel' => 'thenext_o_homepage_options',
            'priority' => 119,
        );
        $thenext_sections['thenext_stores'] = array(
            'title' => __('Stores', 'the-next'),
            'description' => '',
            'panel' => 'thenext_o_homepage_options',
            'priority' => 120,
        );
        $thenext_sections['thenext_explore_categories'] = array(
            'title' => __('Explore Categories', 'the-next'),
            'description' => '',
            'panel' => 'thenext_o_homepage_options',
            'priority' => 121,
        );
        $thenext_sections['thenext_asseenon'] = array(
            'title' => __('Media Highlights', 'the-next'),
            'description' => '',
            'panel' => 'thenext_o_homepage_options',
            'priority' => 122,
        );
        $thenext_sections['thenext_o_features_section'] = array(
            'title' => __('Highlighted Pages', 'the-next'),
            'description' => '',
            'panel' => 'thenext_o_homepage_options',
            'priority' => 120,
        );
        $thenext_sections['thenext_home_team'] = array(
            'title' => __('Our Team', 'the-next'),
            'description' => '',
            'panel' => 'thenext_o_homepage_options',
            'priority' => 121,
        );
        $thenext_sections['thenext_home_reviews'] = array(
            'title' => __('Client Reviews', 'the-next'),
            'description' => '',
            'panel' => 'thenext_o_homepage_options',
            'priority' => 122,
        );
        $thenext_sections['thenext_home_pricing'] = array(
            'title' => __('Pricing', 'the-next'),
            'description' => '',
            'panel' => 'thenext_o_homepage_options',
            'priority' => 123,
        );
        $thenext_sections['thenext_home_contact'] = array(
            'title' => __('Contact', 'the-next'),
            'description' => '',
            'panel' => 'thenext_o_homepage_options',
            'priority' => 124,
        );
        //echo "<pre>".print_r($thenext_sections,1);die();
        return $thenext_sections;
    }

    function CustomHomepageSettingsFields($thenext_options){

        $thenext_options['home_slider_category_o'] = array(
            'label' => __('Slider Category', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'dropdown-taxonomy',
            'section' => 'thenext_home_slider_o',
            'default' => '1',
            'taxonomy' => 'wpdmcategory',
        );

        $thenext_options['thenext_disable_categories_section'] = array(
            'label' => __('Disable Categories Section', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'checkbox',
            'section' => 'thenext_explore_categories',
            'std' => 1
        );

        $thenext_options['thenext_categories_section_heading'] = array(
            'label' => __('Categories Section Heading', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_explore_categories',
            'default' => 'Explore Categories'
        );

        $thenext_options['thenext_explore_category_1'] = array(
            'label' => __('Featured Category #1', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'dropdown-taxonomy',
            'section' => 'thenext_explore_categories',
            'default' => '1',
            'taxonomy' => 'wpdmcategory',
        );

        $thenext_options['thenext_explore_category_2'] = array(
            'label' => __('Featured Category #2', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'dropdown-taxonomy',
            'section' => 'thenext_explore_categories',
            'default' => '1',
            'taxonomy' => 'wpdmcategory',
        );

        $thenext_options['thenext_explore_category_3'] = array(
            'label' => __('Featured Category #3', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'dropdown-taxonomy',
            'section' => 'thenext_explore_categories',
            'default' => '1',
            'taxonomy' => 'wpdmcategory',
        );

        $thenext_options['thenext_all_categories_heading'] = array(
            'label' => __('All Categories Heading', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_explore_categories',
            'default' => 'All Categories'
        );

        $thenext_options['thenext_disable_asseenon_section'] = array(
            'label' => __('Disable Media Highlight Section', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'checkbox',
            'section' => 'thenext_asseenon',
            'std' => 1
        );

        $thenext_options['thenext_asseenon_heading'] = array(
            'label' => __('Media Highlight Heading', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_asseenon',
            'default' => 'As Seen On'
        );

        $thenext_options['thenext_disable_stores_section'] = array(
            'label' => __('Disable Stores Section', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'checkbox',
            'section' => 'thenext_stores',
            'std' => 1
        );

        $thenext_options['thenext_stores_section_heading'] = array(
            'label' => __('Stores Section Heading', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_stores',
            'default' => 'Stores'
        );

        $thenext_options['thenext_new_stores_heading'] = array(
            'label' => __('New Stores Heading', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_stores',
            'default' => 'New Stores'
        );

        $thenext_options['thenext_featured_stores_heading'] = array(
            'label' => __('Featured Store Heading', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_stores',
            'default' => 'Featured Store'
        );

        $thenext_options['thenext_featured_store_id'] = array(
            'label' => __('Featured Store ID', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'number',
            'section' => 'thenext_stores',
            'default' => 1
        );


        $thenext_options['thenext_disable_featured_section'] = array(
            'label' => __('Disable Featured Items Section', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'checkbox',
            'section' => 'thenext_featured_category_o',
            'std' => 1
        );

        $thenext_options['home_featured_heading_o'] = array(
            'label' => __('Featured Items Heading', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_featured_category_o',
            'default' => 'Featured Items'
        );


        $thenext_options['home_featured_category_o'] = array(
            'label' => __('Featured Items Category', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'dropdown-taxonomy',
            'section' => 'thenext_featured_category_o',
            'default' => '1',
            'taxonomy' => 'wpdmcategory',
        );

        $thenext_options['home_slide_count_o'] = array(
            'label' => __('Number of Posts', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'number',
            'section' => 'thenext_home_slider_o',
            'default' => 5,
            'min' => 1,
        );

        $thenext_options['nav_header']['choices'][5] = array(
            'value' => 'header-6',
            'title' => 'Nav Style 6',
            'src' => get_stylesheet_directory_uri() . '/imgs/headers/header-6.png',
        );

        $thenext_options_fpages = array(
            'home_featured_page_o_1' => array(
            'label' => __('Featured Page 1', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'dropdown-pages',
            'section' => 'thenext_featured_pages_o',
            'default' => 0,
        ),
            'home_featured_page_o_2' => array(
                'label' => __('Featured Page 2', 'the-next'),
                'transport' => 'postMessage',
                'type' => 'dropdown-pages',
                'section' => 'thenext_featured_pages_o',
                'default' => 0,
            ),
            'home_featured_page_o_3' => array(
                'label' => __('Featured Page 3', 'the-next'),
                'transport' => 'postMessage',
                'type' => 'dropdown-pages',
                'section' => 'thenext_featured_pages_o',
                'default' => 0,
            ),
            'home_featured_page_o_4' => array(
                'label' => __('Featured Page 4', 'the-next'),
                'transport' => 'postMessage',
                'type' => 'dropdown-pages',
                'section' => 'thenext_featured_pages_o',
                'default' => 0,
            ));

        $thenext_options += $thenext_options_fpages;

        $thenext_options_highlights = array(
            'home_feature_title_o' => array(
                'label' => __('Headline', 'the-next'),
                'transport' => 'postMessage',
                'type' => 'text',
                'section' => 'thenext_o_features_section',
                'default' => '',
            ),
            'home_feature_desc_o' => array(
                'label' => __('Description', 'the-next'),
                'transport' => 'postMessage',
                'type' => 'text',
                'section' => 'thenext_o_features_section',
                'default' => '',
            ),
            'home_feature_page_o_1' => array(
                'label' => __('Feature Page 1', 'the-next'),
                'transport' => 'postMessage',
                'type' => 'dropdown-pages',
                'section' => 'thenext_o_features_section',
                'default' => 0,
            ),
            'home_feature_page_o_2' => array(
                'label' => __('Feature Page 2', 'the-next'),
                'transport' => 'postMessage',
                'type' => 'dropdown-pages',
                'section' => 'thenext_o_features_section',
                'default' => 0,
            ),
            'home_feature_page_o_3' => array(
                'label' => __('Feature Page 3', 'the-next'),
                'transport' => 'postMessage',
                'type' => 'dropdown-pages',
                'section' => 'thenext_o_features_section',
                'default' => 0,
            ),
            'home_feature_page_o_4' => array(
                'label' => __('Feature Page 4', 'the-next'),
                'transport' => 'postMessage',
                'type' => 'dropdown-pages',
                'section' => 'thenext_o_features_section',
                'default' => 0,
            ),
            'home_feature_page_o_5' => array(
                'label' => __('Feature Page 5', 'the-next'),
                'transport' => 'postMessage',
                'type' => 'dropdown-pages',
                'section' => 'thenext_o_features_section',
                'default' => 0,
            ),
            'home_feature_page_o_6' => array(
                'label' => __('Feature Page 6', 'the-next'),
                'transport' => 'postMessage',
                'type' => 'dropdown-pages',
                'section' => 'thenext_o_features_section',
                'default' => 0,
            ));

        $thenext_options += $thenext_options_highlights;

        $thenext_options['home_optimus_team_title'] =array(
            'label' => __('Headline', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_home_team',
            'default' => 'Our Team',
        );
        $thenext_options['home_optimus_team_desc'] =array(
            'label' => __('Sub Heading', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_home_team',
            'default' => 'Awesome Magicians Behind',
        );

        for($i=1; $i<=4; $i++) {
            $thenext_options['home_optimus_team_member_'.$i] = array(
                'label' => __('Team Member #'.$i, 'the-next'),
                'transport' => 'postMessage',
                'type' => 'text',
                'section' => 'thenext_home_team',
                'default' => '1',
            );
        }

        $thenext_options['thenext_disable_clients_review_section'] = array(
            'label' => __('Disable Reviews Section', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'checkbox',
            'section' => 'thenext_home_reviews',
            'std' => 1
        );

        $thenext_options['home_optimus_review_title'] =array(
            'label' => __('Heading', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_home_reviews',
            'default' => 'Customer Reviews',
        );

        $thenext_options['home_optimus_review_desc'] =array(
            'label' => __('Sub Heading', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_home_reviews',
            'default' => 'Our Customers Think, We Are Awesome',
        );

        $thenext_options['thenext_disable_pricing_section'] = array(
            'label' => __('Disable Pricing Table Section', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'checkbox',
            'section' => 'thenext_home_pricing',
            'std' => 1
        );

        $thenext_options['home_optimus_pricing_title'] =array(
            'label' => __('Heading', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_home_pricing',
            'default' => 'Pricing',
        );

        $thenext_options['home_optimus_pricing_desc'] =array(
            'label' => __('Sub Heading', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_home_pricing',
            'default' => 'Get from the best offers:',
        );

        for($i=1; $i<=4; $i++) {
            $thenext_options['home_optimus_pricing_col_'.$i] = array(
                'label' => __('Pricing Table (ID) #'.$i, 'the-next'),
                'transport' => 'postMessage',
                'type' => 'text',
                'section' => 'thenext_home_pricing',
                'default' => '1',
            );
        }

        $thenext_options['home_optimus_contact_title'] =array(
            'label' => __('Heading', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_home_contact',
            'default' => 'Feeling Curious?',
        );

        $thenext_options['home_optimus_contact_desc'] =array(
            'label' => __('Sub Heading', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_home_contact',
            'default' => 'Feeling Curious?',
        );

        $thenext_options['home_optimus_subs_title'] =array(
            'label' => __('Subscription Form Heading', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_home_contact',
            'default' => 'Subscribe',
        );

        $thenext_options['home_optimus_subs_desc'] =array(
            'label' => __('Subscription Form Message', 'the-next'),
            'transport' => 'postMessage',
            'type' => 'text',
            'section' => 'thenext_home_contact',
            'default' => 'Do not miss a single news!',
        );

        return $thenext_options;
    }


	function CustomUserFields( $user )
	{
		?>
		<div class="w3eden">
			<div class="panel panel-default">
				<div class="panel-heading"><b>New User Profile Links</b></div>
				<div class="panel-body">

					<table class="table table-striped table-bordered">
						<tr>
							<th style="max-width: 300px;width: 300px"><label for="faceReview_profile">Designation</label></th>
							<td><input type="text" name="__thenext_designation" value="<?php echo esc_attr(get_the_author_meta( '__thenext_designation', $user->ID )); ?>" class="form-control" /></td>
						</tr>

						<tr>
							<th style="max-width: 300px;width: 300px"><label for="faceReview_profile">FaceReview Profile</label></th>
							<td><input type="text" name="__thenext_faceReview" value="<?php echo esc_attr(get_the_author_meta( '__thenext_faceReview', $user->ID )); ?>" class="form-control" /></td>
						</tr>

						<tr>
							<th><label for="twitter_profile">Twitter Profile</label></th>
							<td><input type="text" name="__thenext_twitter" value="<?php echo esc_attr(get_the_author_meta( '__thenext_twitter', $user->ID )); ?>" class="form-control" /></td>
						</tr>

						<tr>
							<th><label for="google_profile">Google+ Profile</label></th>
							<td><input type="text" name="__thenext_googleplus" value="<?php echo esc_attr(get_the_author_meta( '__thenext_googleplus', $user->ID )); ?>" class="form-control" /></td>
						</tr>

						<tr>
							<th><label for="google_profile">LinkedIn Profile</label></th>
							<td><input type="text" name="__thenext_linkedin" value="<?php echo esc_attr(get_the_author_meta( '__thenext_linkedin', $user->ID )); ?>" class="form-control" /></td>
						</tr>
						<tr>
							<th><label for="__thenext_prifle_pane_bg">Profile Pane Background Image URL</label></th>
							<td><?php echo self::MediaPicker(array('id'=>'profile-bg-image','name'=>'__thenext_profile_bg_img','selected'=>esc_attr(get_the_author_meta( '__thenext_profile_bg_img', $user->ID )))); ?></td>
						</tr>


						<tr>
							<th><label for="__thenext_prifle_pic">Profile Picture</label></th>
							<td><?php echo self::MediaPicker(array('id'=>'profile-image','name'=>'__thenext_prifle_pic','selected'=>esc_attr(get_the_author_meta( '__thenext_prifle_pic', $user->ID )))); ?></td>
						</tr>



					</table>

				</div>
			</div>
		</div>

	<?php
	}

    public static function PricingPackage($params){
        ob_start();
        extract($params);
        $post = get_post($id);
        $template = isset($template)?$template:'package';
        include(get_stylesheet_directory()."/templates/pricingtable/{$template}.php");
        $data = ob_get_clean();
        if(isset($echo) &&  $echo == 1)
            echo $data;
        else
            return $data;
    }

	function SaveCustomUserFields( $user_id )
	{
		foreach($_POST as $meta_key => $meta_value)
			if(strpos($meta_key, "_thenext_")){
				update_user_meta( $user_id,$meta_key, sanitize_text_field( $meta_value ) );
			}
	}

    function ContactRequest(){
        if(isset($_POST['contact'])){

        }
    }

    /**
     * @usage Generate Media Picker
     * @param $params
     * @return string
     */
    public static function MediaPicker($params){
        extract($params);

        $html = "<div class='input-group' style='max-width: 450px'><input class='form-control {$id}' type='text' name='{$name}' id='{$id}_image' value='{$selected}' /><span class='input-group-btn'><button rel='#{$id}_image' class='btn btn-default btn-media-upload' type='button'><i class='wp-menu-image dashicons-before dashicons-admin-media'></i></button></span></div>";
        $html .="<div style='clear:both'></div>";
        return $html;
    }

    function BlogTitle($title){
        if(is_home()) return 'Blog';
        return $title;
    }

    function TemplateTags($vars){
        if(isset($vars['effective_price']) && intval($vars['effective_price']) >0) {
            $vars['buy_or_download_label'] = __('Buy Now @', 'optimus');
            $vars['price_or_free'] =wpdmpp_currency_sign() . $vars['effective_price'];
        }
        else {
            $vars['buy_or_download_label'] = __('Download', 'optimus');
            $vars['price_or_free'] = __('Free', 'optimus');
        }
        $vars['live_preview'] = '';
        $vars['download_link_popup'] = str_replace(array("btn ", "[btnclass]"), "btn btn-lg btn-info no-radius btn-block ", $vars['download_link_popup']);
        return $vars;
    }
}


new TheNextChild();
