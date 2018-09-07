<?php
/**
 * Plugin Name:  WPDM - Premium Packages
 * Plugin URI: https://www.wpdownloadmanager.com/download/premium-package-complete-digital-store-solution/
 * Description: Complete solution for selling digital products
 * Author: Shaon
 * Version: 3.8.9
 * Text Domain: wpdm-premium-packages
 * Author URI: https://www.wpdownloadmanager.com/
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

global $wpdmpp;

if (!class_exists('WPDMPremiumPackage')):
	/**
	 * @class WPDMPremiumPackage
	 */

	define('WPDMPP_Version', '3.8.9');
	define('WPDMPP_BASE_DIR', dirname(__FILE__).'/');
	define('WPDMPP_BASE_URL', plugins_url('wpdm-premium-packages/'));
	define('WPDMPP_MENU_ACCESS_CAP', 'manage_categories');
	define('WPDMPP_ADMIN_CAP', 'manage_categories');

	global $wpdmpp, $wpdmpp_settings;

	class WPDMPremiumPackage
	{

		function __construct()
		{
			global $wpdmpp_settings;
			$wpdmpp_settings = maybe_unserialize(get_option('_wpdmpp_settings'));

			$this->init();
			$this->init_hooks();
		}

		private function init()
		{
			if( ! isset( $_SESSION ) ) session_start();

			global $sap; // Seperator

			if ( function_exists( 'get_option' ) ) {
			    $sap = ( get_option( 'permalink_structure' ) != '') ? '?' : '&';
			}

			$this->include_files();
			$this->wpdmpp_shortcodes();
		}


		private function init_hooks()
		{

		    register_activation_hook( __FILE__, array( 'InstallWPDMPP', 'wpdmpp_install' ) );

		    add_action( 'wpdm-package-form-left', array( $this, 'wpdmpp_meta_box_pricing' ) );
			add_filter( 'wpdm_package_settings_tabs', array( $this, 'wpdmpp_meta_boxes' ) );
            add_filter( 'add_wpdm_settings_tab', array( $this, 'wpdmpp_settings_tab' ) );
			add_action( 'save_post', array( $this, 'wpdmpp_save_meta_data' ), 10, 2);
			add_action( 'wpdm_template_editor_menu', array( $this, 'template_editor_menu' ));
			//add_action( 'wpdm_template_tag_row', array( $this, 'template_tag_row' ));

			add_action( 'init', array( $this, 'wpdmpp_languages' ) );
			add_action( 'init', array( $this, 'wpdmpp_invoice' ) );
			add_action( 'init', array( $this, 'wpdmpp_process_guest_order' ) );
			add_action( 'init', array( $this, 'wpdmpp_download' ), 0);
			add_action( 'init', array( $this, 'wpdmpp_paynow' ) );
			add_action( 'init', array( $this, 'wpdmpp_payment_notification' ) );
			add_action( 'init', array( $this, 'wpdmpp_withdraw_paypal_notification' ) );
			add_action( 'init', array( $this, 'wpdmpp_ajax_payfront' ) );
			add_action( 'init', array( $this, 'wpdmpp_execute' ) );
			add_action( 'init', array( $this, 'wpdmpp_update_profile' ) );
			add_action( 'init', array( $this, 'freeDownload' ) );

			add_action( 'wpdm_login_form', array( $this, 'wpdmpp_invoide_field' ) );
            add_action( 'wpdm_register_form', array( $this, 'wpdmpp_invoide_field' ) );
            add_action( 'wp_login', array( $this, 'wpdmpp_associate_invoice' ), 10, 2 );
            add_action( 'user_register', array( $this, 'wpdmpp_associate_invoice_signup' ), 10, 1 );
            add_action( 'wp_ajax_resolveorder', array( $this, 'wpdmpp_resolveorder' ) );

            add_action( 'wp_ajax_nopriv_gettax', array( $this, 'calculate_tax' ) );
            add_action( 'wp_ajax_gettax', array( $this, 'calculate_tax' ) );

            add_action( 'wp_ajax_product_sales_overview', array( $this, 'wpdmpp_meta_box_sales_overview' ) );

			add_action( 'wp_ajax_nopriv_payment_options', array( $this, 'payment_options' ) );
            add_action( 'wp_ajax_payment_options', array( $this, 'payment_options' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'wpdmpp_enqueue_scripts' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'wpdmpp_admin_enqueue_scripts' ) );

			if ( is_admin() ) {
				add_action( 'wp_ajax_wpdmpp_save_settings', array( $this, 'wpdmpp_save_settings' ) );
				add_action( 'wp_ajax_wpdmpp_ajax_call', array( $this, 'wpdmpp_ajax_call' ) );
			}

			if( ! is_admin() ) {
                add_action( 'init', array( $this, 'wpdmpp_execute' ) );
                add_action( 'wpdm_login_form', array( $this, 'wpdmpp_guest_download_link' ) );
            }

			add_filter( 'wpdm_meta_box', array( $this, 'add_meta_boxes' ) );
			add_filter( 'wpdm_user_dashboard_menu', array( $this, 'wpdmpp_user_dashboard_menu' ) );
			add_filter( 'wpdm_frontend', array( $this, 'wpdmpp_frontend_tabs' ) );
			add_filter( 'wpdm_after_prepare_package_data', array( $this, 'fetch_template_tag' ) );
            add_filter( 'wdm_before_fetch_template', array( $this, 'fetch_template_tag' ) );
            add_filter( 'wpdm_download_link', array( $this, 'download_link' ), 10, 2 );
            add_filter( 'wpdm_check_lock', array( $this, 'wpdmpp_lock_download' ), 10, 2 );
            add_filter( 'wpdm_single_file_download_link', array( $this, 'hide_single_file_download_link' ), 10, 4 );

            //add_action( 'activated_plugin', array( $this, 'pp_save_error' ) );
		}

		function pp_save_error() {
            file_put_contents( ABSPATH.'pp-errors.txt' , ob_get_contents() );
        }

		function wpdmpp_languages()
		{
			load_plugin_textdomain('wpdm-premium-packages', false, dirname(plugin_basename(__FILE__)) . '/languages/');
		}

		function include_files()
		{
			include(dirname(__FILE__) . "/includes/libs/class.InstallWPDMPP.php");
			include(dirname(__FILE__) . "/includes/libs/class.LicenseManager.php");
			include(dirname(__FILE__) . "/includes/libs/class.Order.php");
			include(dirname(__FILE__) . "/includes/libs/class.Payment.php");
			include(dirname(__FILE__) . "/includes/libs/class.CustomActions.php");
			include(dirname(__FILE__) . "/includes/libs/class.CustomColumns.php");
			include(dirname(__FILE__) . "/includes/libs/class.Currencies.php");
			include(dirname(__FILE__) . "/includes/libs/class.BillingInfo.php");
			include(dirname(__FILE__) . "/includes/libs/class.WPDMPPDashboardWidgets.php");
			include(dirname(__FILE__) . "/includes/libs/class.WPDMPPCouponCodes.php");
			include(dirname(__FILE__) . "/includes/libs/class.Shop.php");
			include(dirname(__FILE__) . "/includes/libs/functions.php");
			include(dirname(__FILE__) . "/includes/libs/cart.php");
			include(dirname(__FILE__) . "/includes/libs/hooks.php");

			include(dirname(__FILE__) . "/includes/menus/class.WPDMPPAdminMenus.php");

			include(dirname(__FILE__) . "/includes/widgets/widget-cart.php");

			/**
			 * Auto load default payment mothods
			 */
			global $payment_methods, $wpdmpp_settings;
			$pdir = WPDMPP_BASE_DIR . "includes/libs/payment-methods/";
			$methods = scandir($pdir, 1);

			foreach ($methods as $method) {
				if (!strpos("_".$method, '.')) {
				$path = realpath($pdir . $method . "/class.{$method}.php");
					if (file_exists($path)) {
						$payment_methods[] = $method;
						include_once($path);
					}
				}
			}

			$wpdmpp_settings = maybe_unserialize(get_option('_wpdmpp_settings'));
		}
		
		function calculate_tax(){
		    $cartsubtotal = wpdmpp_get_cart_subtotal();
		    $cart_id = wpdmpp_cart_id();
		    $coupon = get_option($cart_id."_coupon", array());
		    $cartdiscount = isset($coupon['discount'])?$coupon['discount']:0;
		    $cartsubtotal -= $cartdiscount;
		    $tax_total = wpdmpp_calculate_tax2();
		    $total_including_tax = $cartsubtotal + $tax_total;
		    $currency_sign = wpdmpp_currency_sign();

		    $currency_sign_before = wpdmpp_currency_sign_position() == 'before' ? $currency_sign : '';
            $currency_sign_after = wpdmpp_currency_sign_position() == 'after' ? $currency_sign : '';

            $tax_str = $currency_sign_before . number_format((double)str_replace(',','',$tax_total),2) . $currency_sign_after;
            $total_str = $currency_sign_before . number_format((double)str_replace(',','',$total_including_tax),2) . $currency_sign_after;

            $updates = array( 'tax' => $tax_str, 'total' => $total_str , 'subtotal' => $cartsubtotal, 'dis' => $cartdiscount);

            $_SESSION['tax'] = $tax_total;
            $_SESSION['subtotal'] = $cartsubtotal;

            die( json_encode($updates) );
		}


		/**
		 * Metabox content for Pricing and other Premium Pckage Settings
		 */

		function wpdmpp_meta_box_sales_overview_loader(){
			 ?>
			<div id="wpdmpp-sales-overview"><div style="padding: 50px 10px;text-align: center"><i class="fa fa-refresh fa-spin"></i> <?php _e('Loading....','wpdm-premium-packages'); ?></div></div>
			<script>
				jQuery(function ($) {
					$('#wpdmpp-sales-overview').load(ajaxurl, {action: 'product_sales_overview', post: <?php echo wpdm_query_var('post'); ?>});
				});
			</script>
			<?php
		}

		function wpdmpp_meta_box_sales_overview()
		{
			global $post;
			include \WPDM\Template::locate('metaboxes/product-sales-overview.php', dirname(__FILE__) . '/templates/');
			die();
		}


		function payment_options()
		{
			global $post;
			include \WPDM\Template::locate('checkout-cart/checkout.php', dirname(__FILE__) . '/templates/');
			die();
		}

		function add_meta_boxes($metaboxes){
			$pid = wpdm_query_var('post');
			$price = wpdmpp_effective_price($pid);
			if($price > 0){
				$wpdmpp_metaboxes['sales-overview'] =  array('title' => __('Sales Overview', "wpdm-premium-packages"), 'callback' => array($this, 'wpdmpp_meta_box_sales_overview_loader'), 'position' => 'side', 'priority' => 'core');
				$metaboxes = $wpdmpp_metaboxes + $metaboxes;
			}
			return $metaboxes;
		}

		/**
		 * Metabox content for Pricing and other Premium Pckage Settings
		 */
		function wpdmpp_meta_box_pricing()
		{
			global $post;
			include \WPDM\Template::locate('metaboxes/wpdm-pp-settings.php', dirname(__FILE__) . '/templates/');
		}

		/**
		 * @param $tabs
		 * @return mixed
		 * @usage Adding Premium Package Settings Metabox by applying WPDM's 'wpdm_package_settings_tabs' filter
		 */
		function wpdmpp_meta_boxes($tabs)
		{
			if(is_admin())
				$tabs['pricing'] = array('name' => __('Pricing & Discounts', "wpdm-premium-packages"), 'callback' => array( $this, 'wpdmpp_meta_box_pricing' ) );

			return $tabs;
		}

		/**
		 * @param $postid
		 * @param $post
		 * @usage
		 */
		function wpdmpp_save_meta_data($postid, $post)
		{
			if (isset($_POST['post_author'])) {
				$userinfo = get_userdata($_POST['post_author']);

				if ($userinfo->roles[0] != "administrator") {
					if ($_POST['original_post_status'] == "draft" && $_POST['post_status'] == "publish") {
						global $current_user;
						$siteurl = home_url("/");
						$admin_email = get_bloginfo("admin_email");
						$to = $userinfo->user_email; //post author
						$from = $current_user->user_email;
						$link = get_permalink($post->ID);

						$subject = "Product Approved!";
						$message = "Your product {$post->post_title} {$link} is approved to {$siteurl} ";
						$email['subject'] = $subject;
						$email['body'] = $message;
						$email['headers'] = 'From:  <' . $from . '>' . "\r\n";
						$email = apply_filters("product_approval_email", $email);
						wp_mail($to, $email['subject'], $email['body'], $email['headers']);
					}
				}
			}
		}

		/**
		 *  Premium Package Settings Page
		 */
		function wpdmpp_settings()
		{
			include("includes/settings/settings.php");
		}

		function wpdmpp_settings_tab($tabs){
			$tabs['ppsettings'] = wpdm_create_settings_tab('ppsettings', 'Premium Package', array( $this, 'wpdmpp_settings' ), $icon = 'fa fa-shopping-cart');
			return $tabs;
		}

		/**
		 * Generate Order Invoice op request
		 */
		function wpdmpp_invoice()
		{
			if (isset($_GET['id']) && $_GET['id'] != '' && isset($_GET['wpdminvoice'])) {
				include \WPDM\Template::locate("wpdm-pp-invoice.php", __DIR__.'/templates/');
				die();
			}
		}

        /**
         * Shortcodes
         */
		function wpdmpp_shortcodes()
		{
			add_shortcode( 'wpdm-pp-purchases', array( $this, 'wpdmpp_user_purchases' ) );
			add_shortcode( 'wpdm-pp-guest-orders', array( $this, 'wpdmpp_guest_orders' ) );
			add_shortcode( 'wpdm-pp-earnings', array( $this, 'wpdmpp_earnings' ) );
			add_shortcode( 'wpdm-pp-edit-profile' , array( $this, 'wpdmpp_edit_profile' ) );
		}

		/**
		 * [wpdm-pp-purchases] shortcode - Lists all purchases/orders made by current user
         *
		 * @return string
		 */
		function wpdmpp_user_purchases()
		{
			global $current_user;

			$dashboard          = true;
			$wpdmpp_settings    = get_option('_wpdmpp_settings');

			ob_start();
			?>
			<div class="w3eden">
			<?php
			if( ! is_user_logged_in() ) {

			    // Show login/registration form. This is a Download Manager core template
				include_once( wpdm_tpl_path('wpdm-be-member.php') );

				// If guest order is enabled then show guest order page link
				if( isset($_SESSION['last_order']) && $_SESSION['last_order'] != '' && isset($wpdmpp_settings['guest_download']) && $wpdmpp_settings['guest_download'] == 1){
				    include_once \WPDM\Template::locate("partials/guest_order_page_link.php", __DIR__.'/templates/');
				}
			}else{

			    // List all orders made by the user
                $order = new Order();
                $myorders = $order->GetOrders($current_user->ID);

                include_once wpdm_tpl_path('wpdm-pp-purchases.php', WPDMPP_BASE_DIR.'/templates/');
			}
			echo '</div>';

			$purchase_orders_html = ob_get_clean();

			return $purchase_orders_html;
		}

		function wpdmpp_user_dashboard_menu($menu){
			$menu = array_merge(array_splice($menu, 0, 1), array('purchases' => array('name' => __('Purchases','wpdm-premium-packages'), 'callback' => array( $this, 'wpdmpp_purchased_items' ) ) ), $menu);
			return $menu;
		}

		function wpdmpp_purchased_items($params = array()){
			global $wpdb, $current_user;
			$uid = $current_user->ID;
			$purchased_items = $wpdb->get_results("select oi.*,o.date as odate, o.order_status from {$wpdb->prefix}ahm_order_items oi,{$wpdb->prefix}ahm_orders o where o.order_id = oi.oid and o.uid = {$uid} and o.order_status IN ('Expired', 'Completed') order by `date` desc");

			ob_start();
			if(isset($params[2]) && $params[1] == 'order')
			    include wpdm_tpl_path('user-dashboard/order-details.php', WPDMPP_BASE_DIR.'/templates/');
			else if(isset($params[1]) && $params[1] == 'orders')
			    include wpdm_tpl_path('user-dashboard/purchase-orders.php', WPDMPP_BASE_DIR.'/templates/');
			else
			    include wpdm_tpl_path('user-dashboard/purchased-items.php', WPDMPP_BASE_DIR.'/templates/');

			return ob_get_clean();
		}

		/**
		 * [wpdm-pp-guest-orders] shortcode
         *
		 * @return string
		 */

		function wpdmpp_guest_orders(){
			ob_start();
			global $post;

			if(is_object($post) && get_the_permalink() == wpdmpp_guest_order_page() && !isset($_SESSION['guest_order_init']))
			    $_SESSION['guest_order_init'] = uniqid();

			include  wpdm_tpl_path('wpdm-pp-guest-orders.php', WPDMPP_BASE_DIR.'/templates/');
			return ob_get_clean();
		}

		/**
		 * Process Guest Orders
		 */
		function wpdmpp_process_guest_order(){

			if(isset($_POST['go'])) {

				if(!isset($_SESSION['guest_order_init'])) { $_SESSION['guest_order_init'] = uniqid(); die('nosess'); }

				$orderid = $_POST['go']['order'];
				$orderemail = $_POST['go']['email'];

				$o = new Order();
				$order = $o->GetOrder($orderid);

				// No match for order id
                if( ! is_object($order) || ! isset($order->order_id) || $order->order_id != $orderid) die('noordr');

                // Found a match for order id
                $billing_info = unserialize($order->billing_info);
                $billing_email = isset($billing_info['order_email']) ? $billing_info['order_email'] : '';

				if(is_email($orderemail) && $orderemail == $billing_email && $order->uid <= 0){
					$_SESSION['guest_order'] = $orderid;
					die('success');
				}

				// Order assigned to registered user, so no guest access, please login to access order
				if($order->uid > 0) die('nogues');

				die('noordr');
			}

		}

		function wpdmpp_frontend_tabs($tabs){
			$tabs['sales'] = array('label'=>'Sales','shortcode' => '[wpdm-pp-earnings]');
			return $tabs;
		}

		/**
		 * Save admin settings options
		 */
		function wpdmpp_save_settings()
		{
			update_option('_wpdmpp_settings', $_POST['_wpdmpp_settings']);
			die(__('Settings Saved Successfully', "wpdm-premium-packages"));
		}

		function wpdmpp_download()
		{
			if ( ! isset($_GET['wpdmdl']) || ! isset($_GET['oid']) ) return false;

			if(wpdm_query_var('preact') == 'login'){
				$user = wp_signon(array('user_login' => wpdm_query_var('user'), 'user_password' => wpdm_query_var('pass') ));
				if(!$user->ID)
				wp_die(__("Login Failed!","wpdm-premium-packages"));
				else
				wp_set_current_user($user->ID);
			}

			global $wpdb, $current_user;
			$settings = get_option('_wpdmpp_settings');

			$order = new Order();
			$odata = $order->GetOrder($_GET['oid']);
			$items = unserialize($odata->items);

			if($odata->uid != $current_user->ID && !isset($_SESSION['guest_order'])) wp_die(__("Calling 911! You better run now!!","wpdm-premium-packages"));
			if($odata->order_status == 'Expired') wp_die(__("Sorry! Support and Update Access Period is Already Expired","wpdm-premium-packages"));

			$base_price = get_post_meta($_GET['wpdmdl'], '__wpdm_base_price', true);

			$package = get_post($_GET['wpdmdl'], ARRAY_A);
			$package['files'] = maybe_unserialize(get_post_meta($package['ID'], '__wpdm_files', true));


			$cart = maybe_unserialize($odata->cart_data);

			$cfiles = array();

			if(isset($cart[$_GET['wpdmdl']]['files']) && is_array($cart[$_GET['wpdmdl']]['files']) && count($cart[$_GET['wpdmdl']]['files']) > 0){
                $files = $cart[$_GET['wpdmdl']]['files'];
                $files = array_keys($files);
                foreach ($files as $fID){
                    $cfiles[$fID] = $package['files'][$fID];
                }
			}

			$package['individual_file_download'] = maybe_unserialize(get_post_meta($package['ID'], '__wpdm_individual_file_download', true));

			if ($base_price == 0 && (int)$_GET['wpdmdl'] > 0) {
				//for free items
				include(WPDM_BASE_DIR . "/wpdm-start-download.php");
			}
			if (@in_array($_GET['wpdmdl'], $items) && $_GET['oid'] != '' && is_user_logged_in() && $current_user->ID == $odata->uid && $odata->order_status == 'Completed') {
				//for premium item
				if(count($cfiles) > 0){
					if(count($cfiles) > 1){
						$zipped = \WPDM\FileSystem::zipFiles($cfiles, $package['post_title']." ".$odata->oid);
						\WPDM\FileSystem::donwloadFile($zipped, basename($zipped));
					} else{
						$file = array_shift($cfiles);
						\WPDM\FileSystem::donwloadFile($file, basename($file));
					}

					die();
				}
				else
				include(WPDM_BASE_DIR . "/wpdm-start-download.php");
			}

			if (@in_array($_GET['wpdmdl'], $items)
				&& isset($_GET['oid'])
				&& $_GET['oid'] != ''
				&& !is_user_logged_in()
				&& $odata->uid == 0
				&& $odata->order_status == 'Completed'
				&& isset($settings['guest_download'])
				&& isset($_SESSION['guest_order'])) {
					//for guest download
					include(WPDM_BASE_DIR . "/wpdm-start-download.php");

			}

		}

		/**
		 * Create new Order
		 */
		function create_order()
		{
			global $current_user;

			//if(floatval(wpdmpp_get_cart_total()) <=0 ) return;

			$order = new Order();
			if (isset($_SESSION['orderid']) && $_SESSION['orderid'] != '') {
				$order_info = $order->GetOrder($_SESSION['orderid']);
				if ($order_info->order_id) {
					$data = array(
						'cart_data' => serialize(wpdmpp_get_cart_data()),
						'items' => serialize(array_keys(wpdmpp_get_cart_data()))
					);
					$order->UpdateOrderItems(wpdmpp_get_cart_data(), $_SESSION['orderid']);
					$insertid = $order->Update($data, $_SESSION['orderid']);
				} else {
					$cart_data = serialize(wpdmpp_get_cart_data());
					$items = serialize(array_keys(wpdmpp_get_cart_data()));
					$order->NewOrder($_SESSION['orderid'], "", $items, 0, $current_user->ID, 'Processing', 'Processing', $cart_data);
					$order->UpdateOrderItems($cart_data, $_SESSION['orderid']);
				}
			} else {
				$cart_data = serialize(wpdmpp_get_cart_data());
				$items = serialize(array_keys(wpdmpp_get_cart_data()));
				$insertid = $order->NewOrder(uniqid(), "", $items, 0, $current_user->ID, 'Processing', 'Processing', $cart_data);
				$order->UpdateOrderItems($cart_data, $_SESSION['orderid']);
			}
		}

		/**
		 * Saving payment method info from checkout process
		 */
		function wpdmpp_paynow()
		{
			if (isset($_REQUEST['task']) && $_REQUEST['task'] == "paynow") {

				//if(floatval(wpdmpp_get_cart_total()) <= 0 ) die('Empty Cart!');

				global $current_user;

				$this->create_order();

				$data = array(
					'payment_method' => $_POST['payment_method'],
					'billing_info' => serialize($_POST['billing'])
				);

				$order = new Order();
				$od = $order->Update($data, $_SESSION['orderid']);

				if(is_user_logged_in()){
				    $billing_info = $_POST['billing'];
				    $billing_info['email'] = $_POST['billing']['order_email'];
    				$billing_info['phone'] = '';
    				$cb = get_user_meta($current_user->ID, 'user_billing_shipping', true);
    				if(!$cb)
				    update_user_meta($current_user->ID, 'user_billing_shipping', serialize(array('billing' => $billing_info)));;
				}

				$order_info = $order->GetOrder($_SESSION['orderid']);
				$this->wpdmpp_place_order();
				die();
			}
		}

		/**
		 * Placing order from checkout process
		 */
		function wpdmpp_place_order()
		{
			//if(floatval(wpdmpp_get_cart_total()) <= 0 ) return;

			$order = new Order();
			$order_total = $order->CalcOrderTotal($_SESSION['orderid']);
			$oid = $_SESSION['orderid'];
			$tax = 0;

			if(count($order->GetOrderItems($_SESSION['orderid'])) == 0){
				\WPDM_Messages::Error(__("Cart is Empty !", "wpdm-premium-packages"),0);
				die();
			}

			$subtotal = wpdmpp_get_cart_subtotal();
            if(wpdmpp_tax_active() && isset($_SESSION['tax'])){
                $tax = $_SESSION['tax'];
                $order_total = $subtotal + $tax;
            }
			$cart_id = wpdmpp_cart_id();
			$coupon = get_option($cart_id."_coupon", array('code' => '', 'discount' => 0));
			$data = array(
				'subtotal' => $subtotal,
				'total' => $order_total - $coupon['discount'],
				'order_notes' => '',
				'cart_discount' => 0,
				'coupon_discount' => $coupon['discount'],
				'coupon_code' => $coupon['code'],
				'tax' => $tax
			);
			//dd($data);
			$od = $order->Update($data, $_SESSION['orderid']);
			do_action("wpdm_before_placing_order", $_SESSION['orderid']);

			// If order total is not 0 then go to payment gateway
			if ($order_total > 0) {
				$payment = new Payment();
				$payment->InitiateProcessor($_POST['payment_method']);
				$payment->Processor->OrderTitle = 'Order# ' . $_SESSION['orderid'];
				$payment->Processor->InvoiceNo = $_SESSION['orderid'];
				$payment->Processor->Custom = $_SESSION['orderid'];
				$payment->Processor->Amount = number_format($order_total,2);
				echo $payment->Processor->ShowPaymentForm(1);
				if(!isset($payment->Processor->EmptyCartOnPlaceOrder) || $payment->Processor->EmptyCartOnPlaceOrder == true)
				wpdmpp_empty_cart();
				die();
			} else {
				// if order total is 0 then empty cart and redirect to home
				Order::complete_order($oid);
				wpdmpp_empty_cart();
				wpdmpp_js_redirect(wpdmpp_orders_page('id='.$oid));
			}
		}

		/**
		 * Payment notification process
		 */
		function wpdmpp_payment_notification()
		{
			if (isset($_REQUEST['action']) && $_REQUEST['action'] == "wpdmpp-payment-notification") {
				$payment_method = new $_REQUEST['class']();

				if ($payment_method->VerifyNotification()) {
					global $wpdb;
					Order::complete_order($payment_method->InvoiceNo, true, $payment_method);
					do_action("wpdm_after_checkout",$payment_method->InvoiceNo);
					die('OK');
				}
				die("FAILED");
			}
		}

		/**
         * Withdraw money from paypal notification
         */
        function wpdmpp_withdraw_paypal_notification()
        {
            if (isset($_REQUEST['action']) && $_REQUEST['action'] == "withdraw_paypal_notification" && current_user_can(WPDMPP_MENU_ACCESS_CAP)) {

                if (isset($_POST["txn_id"]) && isset($_POST["txn_type"]) && $_POST["status"] == "Completed") {
                    global $wpdb;
                    $wpdb->update(
                        "{$wpdb->prefix}ahm_withdraws",
                        array(
                            'status' => 1
                        ),
                        array('id' => $_POST['custom']),
                        array(
                            '%d'
                        ),
                        array('%d')
                    );
                }
            }
        }

        /**
         * Payment using ajax
         */
        function wpdmpp_ajax_payfront()
        {
            if (isset($_POST['task'], $_POST['action']) && $_POST['task'] == "paymentfront" && $_POST['action'] == "wpdmpp_ajax_call") {
                $data['order_id'] = $_POST['order_id'];
                $data['payment_method'] = $_POST['payment_method'];
                PayNow($data);
                die();
            }
        }

        /**
         * Dynamic function call using AJAX
         */
        function wpdmpp_ajax_call()
        {
            $CustomActions = new CustomActions();
            if (method_exists($CustomActions, $_POST['execute'])) {
                $method = esc_attr($_POST['execute']);
                echo $CustomActions->$method();
                die();
            } else
            die("Function doesn't exist");
        }

        /**
         * Execute Custom Action
         */
        function wpdmpp_execute()
        {
            $CustomActions = new CustomActions();
            if(isset($_POST['action']) && $_POST['action']=='wpdm_pp_ajax_call'){
                if (method_exists($CustomActions, $_POST['execute'])) {
                    $method = esc_attr($_POST['execute']);
                    echo $CustomActions->$method();
                    die();
                }
            }
        }

        /**
         * Function for earnings using shortcode
         */
        function wpdmpp_earnings()
        {
            include \WPDM\Template::locate("wpdm-pp-earnings.php", __DIR__.'/templates/');
        }

        /**
         * Edit Profile Shortcode Function
         */
        function wpdmpp_edit_profile()
        {
            include  \WPDM\Template::locate("wpdm-pp-edit-profile.php", __DIR__.'/templates/');
        }

        /**
         * Update User Profile
         */
        function wpdmpp_update_profile()
        {
            global $current_user;
            if (!is_user_logged_in() || !isset($_POST['profile'])) return;

            $userdata = $_POST['profile'];
            $userdata['ID'] = $current_user->ID;
            if ($_POST['password'] == $_POST['cpassword']) {
                wp_update_user($userdata);
                $userdata['user_pass'] = $_POST['password'];
                update_user_meta($current_user->ID, 'payment_account', $_POST['payment_account']);
                update_user_meta($current_user->ID, 'phone', $_POST['phone']);
                $_SESSION['member_success'] = __("Profile Updated Successfully", "wpdm-premium-packages");

            } else {
                $_SESSION['member_error'][] = __("Confirm Password Not Matched. Profile Update Failed!", "wpdm-premium-packages");
            }
            update_user_meta($current_user->ID, 'user_billing_shipping', serialize($_POST['checkout']));

            wpdmpp_redirect($_SERVER['HTTP_REFERER']);
            die();

        }

        /**
         * Load Scripts and Styles
         * @param $hook
         */
        function wpdmpp_enqueue_scripts($hook)
        {
			wp_enqueue_script('wpdm-pp-js', WPDMPP_BASE_URL.'assets/js/wpdmpp-front.js', array('jquery') );
            wp_enqueue_style('wpdmpp-front', WPDMPP_BASE_URL.'assets/css/wpdmpp.css' );

            $settings = get_option('_wpdmpp_settings');

            if( get_the_ID() == $settings['orders_page_id'] || get_the_ID() == $settings['guest_order_page_id'] ){
                wp_enqueue_script('thickbox');
                wp_enqueue_style('thickbox');
                wp_enqueue_script('media-upload');
                wp_enqueue_media();
            }
        }

        function wpdmpp_admin_enqueue_scripts($hook)
        {
        	if( get_post_type() == 'wpdmpro' || strstr($hook, 'dmpro_page')) {
                wp_enqueue_script('jquery');
                wp_enqueue_script('jquery-form');
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-datepicker');
                wp_enqueue_script('jquery-ui-accordion');

                wp_enqueue_style('wpdmpp-admin', WPDMPP_BASE_URL.'assets/css/wpdmpp-admin.css' );
                wp_enqueue_script('wpdmpp-admin-js', WPDMPP_BASE_URL.'assets/js/wpdmpp-admin.js', array('jquery'));

                // Load Download Manager Scripts
                wp_enqueue_style('wpdm-bootstrap', WPDM_BASE_URL.'assets/bootstrap/css/bootstrap.css' );
                wp_enqueue_script('wpdm-bootstrap', WPDM_BASE_URL.'assets/bootstrap/js/bootstrap.min.js', array('jquery'));
                wp_enqueue_script('jquery-validate', WPDM_BASE_URL.'assets/js/jquery.validate.min.js', array('jquery'));
                wp_enqueue_script('wpdm-bootstrap-select', WPDM_BASE_URL.'assets/js/bootstrap-select.min.js',  array('jquery', 'wpdm-bootstrap'));
                wp_enqueue_style('wpdm-bootstrap-select', WPDM_BASE_URL.'assets/css/bootstrap-select.min.css');
            }
        }

        public static function wpdmpp_is_purchased($pid, $uid = 0){
            global $current_user, $wpdb;
            if(!is_user_logged_in() && !$uid) return false;
            $uid = $uid?$uid:$current_user->ID;
            $orderid = $wpdb->get_var("select o.order_id from {$wpdb->prefix}ahm_orders o, {$wpdb->prefix}ahm_order_items oi  where uid='{$uid}' and o.order_id = oi.oid and oi.pid = {$pid} and order_status='Completed'");
            return $orderid;
        }

        /**
         * Generate Download URL
         * @param $id
         * @return string|void
         */
        static function wpdmpp_customer_download_link($id){
            $orderid = self::wpdmpp_is_purchased($id);
            if($orderid)
                return $orderid ? wpdm_download_url($id, "&oid=$orderid") : "";
        }

        function hide_single_file_download_link($link, $url, $file_path, $file){
            $effective_price = wpdmpp_effective_price($file['ID']);
            if($effective_price > 0) $link = '';
            return $link;
        }

        public static function hasFreeFile($id = null){
        	if(!$id) $id = get_the_ID();
        	$fd = maybe_unserialize(get_post_meta($id, '__wpdm_free_downloads', true));
        	if(is_array($fd) && count($fd) > 0 && $fd[0] != '') return $fd;
        	return false;
        }

        function freeDownload(){
        	if(isset($_GET['wpdmdlfree'])){
        		$id = (int)$_GET['wpdmdlfree'];
        		$freefiles = self::hasFreeFile($id);
        		if(!$freefiles) wp_die('No free file found!');
        		$zipped = \WPDM\FileSystem::zipFiles($freefiles, get_the_title($id));
        		\WPDM\FileSystem::donwloadFile($zipped, basename($zipped));
				die();
        	}
        }

        /**
		 * @param $id
		 * @param $link_label
		 * @param string $class
		 * @return string
		 */
        static function free_download_button($id, $link_label, $class = 'btn btn-lg btn-info btn-block'){
        	return "<a href='".home_url('/?wpdmdlfree='.$id)."' class='{$class}' >".$link_label."</a>";
        }


        function download_link($link, $package){
        	$effective_price = wpdmpp_effective_price($package['ID']);
        	if ($effective_price > 0) {
        		return wpdmpp_add_to_cart_html($package['ID']);
        	}

        	return $link;
        }


        /**
		 * @param $vars
		 * @return mixed
		 */
        function fetch_template_tag($vars)
        {
            global $wpdb;
            //$vars['base_price'] = get_post_meta($vars['ID'], '__wpdm_base_price', true);
            //$vars['sales_price'] = get_post_meta($vars['ID'], '__wpdm_sales_price', true);
            $effective_price = wpdmpp_effective_price($vars['ID']);
            $vars['effective_price'] = $effective_price;
            $vars['currency'] = wpdmpp_currency_sign();
            $vars['currency_code'] = wpdmpp_currency_code();
            $vars['free_download_btn'] = "";
            $vars['premium_file_list'] = \WPDM\libs\FileList::Table($vars);
            if($effective_price > 0 && self::hasFreeFile($vars['ID'])){
            	$vars['free_download_btn'] = self::free_download_button($vars['ID'], $vars['link_label']);
            	$vars['free_download_url'] = home_url('/?wpdmdlfree='.$vars['ID']);
            }
            else {
            	$vars['free_download_btn'] = $vars['free_download_url'] = '';
           	}
            if ($effective_price > 0) {
            	if(method_exists(new \WPDM\libs\FileList(), 'Premium'))
                $vars['premium_file_list'] = \WPDM\libs\FileList::Premium($vars);
                $vars['addtocart_url'] = home_url("?addtocart={$vars['ID']}");
                $vars['addtocart_link'] = wpdmpp_waytocart($vars);
                $vars['addtocart_button'] = $vars['addtocart_link'];
                $vars['addtocart_form'] = wpdmpp_add_to_cart_html($vars['ID']);
                $vars['customer_download_link'] = $this->wpdmpp_customer_download_link($vars['ID']);
                $vars['download_link'] = $vars['addtocart_form'];
                $vars['download_link_extended'] = $vars['addtocart_form'];
                $vars['download_link_popup'] = $vars['addtocart_button'];
                $vars['price_range'] = wpdmpp_price_range($vars['ID']);
            } else {
                $vars['addtocart_url'] = $vars['download_url'];
                $vars['addtocart_link'] = $vars['download_link'];
                $vars['addtocart_form'] = $vars['download_link'];
                $vars['customer_download_link'] = $vars['download_link'];
                $vars['price_range'] =  wpdmpp_currency_sign().'0.00';
            }

            return $vars;
        }

        function template_editor_menu(){
            ?>
            <li class="dropdown">
                <a href="#" id="droppp" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php _e('Premium Package','wpdm-premium-packages'); ?><b class="caret"></b></a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="droppp">
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[addtocart_url]"><?php _e('AddToCart URL','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[addtocart_link]"><?php _e('AddToCart Link','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[addtocart_form]"><?php _e('AddToCart Form','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[customer_download_link]"><?php _e('Customer Download Link','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[free_download_url]"><?php _e('Free Download Button','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[free_download_btn]"><?php _e('Free Download URL','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[price_range]"><?php _e('Price Range','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[premium_file_list]"><?php _e('File List Price','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[effective_price]"><?php _e('Effective Item Price','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[currency_code]"><?php _e('Currency Code','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[currency]"><?php _e('Currency Sign','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[base_price]"><?php _e('Base Price','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[sales_price]"><?php _e('Sales Price','wpdm-premium-packages'); ?></a></li>
                </ul>
            </li>

            <?php
        }

        function template_tag_row(){
            ?>
            <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[addtocart_url]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('AddToCart URL for a package','wpdm-premium-packages'); ?></td></tr>
            <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[addtocart_link]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('AddToCart Link for a package','wpdm-premium-packages'); ?></td></tr>
            <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[addtocart_form]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('AddToCart Form','wpdm-premium-packages'); ?></td></tr>
            <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[customer_download_link]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('Customer Download Link','wpdm-premium-packages'); ?></td></tr>
            <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[free_download_btn]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('Free Download Button','wpdm-premium-packages'); ?></td></tr>
            <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[free_download_url]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('Free Download URL','wpdm-premium-packages'); ?></td></tr>
            <?php
        }

        /**
         * Required for guest checkout
         */
        function wpdmpp_invoide_field(){
            if(isset($_GET['orderid'])){
                echo "<input type='hidden' name='invoice' value='{$_GET['orderid']}' />";
            }
        }

        /**
         * Link Guest Order when user logging in
         * @param $user_login
         * @param $user
         */
        function wpdmpp_associate_invoice($user_login, $user){
            if(isset($_POST['invoice'])){
               $order = new Order();
               $orderdata = $order->GetOrder($_POST['invoice']);
                if($orderdata && intval($orderdata->uid) == 0){
                    Order::Update(array('uid'=>$user->ID), $_POST['invoice']);
                }
            }
        }

        /**
         * Link Guest Order when user Signing Up
         * @param $user_id
         */
        function wpdmpp_associate_invoice_signup($user_id){
            if(isset($_POST['invoice'])){
               $order = new Order();
               $orderdata = $order->GetOrder($_POST['invoice']);
                if($orderdata && intval($orderdata->uid) == 0){
                    Order::Update(array('uid'=>$user_id), $_POST['invoice']);
                }
            }
        }

        /**
         * Resolve unassigned Order
         */
        function wpdmpp_resolveorder(){
            global $current_user;
            $order = new Order();
            $data = $order->GetOrder($_REQUEST['orderid']);
            if(!$data) die("Order not found!");
            if($data->uid!=0) {
                if($data->uid==$current_user->ID)
                die("Order is already linked with your account!");
                else
                die("Order is already linked with an account!");
            }
            Order::Update(array('uid'=>$current_user->ID), $data->order_id);
            die("ok");
        }

        /**
         * Filter for locked Downloads
         * @param $lock
         * @param $id
         * @return string
         */
        function wpdmpp_lock_download($lock, $id){
            $effective_price = wpdmpp_effective_price($id);
            if( intval($effective_price) > 0 )
                $lock = 'locked';

            return $lock;
        }

        function wpdmpp_guest_download_link(){
            global $wp_query;

            if( isset( $wp_query->query_vars['udb_page'] ) && strstr($wp_query->query_vars['udb_page'], 'purchases' ) &&  wpdmpp_guest_order_page() ):
                include_once \WPDM\Template::locate("partials/guest_order_page_link.php", __DIR__.'/templates/');
            endif;
        }


	}

endif;

if(defined('WPDM_Version'))
$wpdmpp = new WPDMPremiumPackage();