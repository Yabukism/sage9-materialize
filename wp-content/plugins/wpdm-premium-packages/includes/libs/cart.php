<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shortcode function for [wpdm-pp-cart], Shows Premium Package Cart
 *
 * @return string
 */
function wpdmpp_show_cart(){
    global $wpdb;
    wpdmpp_calculate_discount();
    $cart_data      = wpdmpp_get_cart_data();
    $login_html     = "";
    $payment_html   = "";
    $settings       = get_option('_wpdmpp_settings');
    $guest_checkout = (isset($settings['guest_checkout']) && $settings['guest_checkout'] == 1) ? 1 : 0;
    $cart_id        = wpdmpp_cart_id();
    $coupon         = get_option($cart_id."_coupon");

    if(is_array($coupon)) {
        $coupon['discount'] = WPDMPPCouponCodes::validate_coupon($coupon['code']);
        update_option($cart_id . "_coupon", $coupon);
    }

    $cart_subtotal          = number_format((double)str_replace(',', '', wpdmpp_get_cart_subtotal()), 2);
    $cart_total             = number_format((double)str_replace(',', '', wpdmpp_get_cart_total()), 2);
    $cart_tax               = number_format((double)str_replace(',', '', wpmpp_get_cart_tax()), 2);
    $cart_total_with_tax    = number_format((double)str_replace(',', '', $cart_total + $cart_tax), 2);
    $cart_coupon            = wpdmpp_get_cart_coupon();
    $cart_coupon_discount   = isset($cart_coupon['discount'])?number_format($cart_coupon['discount'],2, '.', ''):0.00;

    $T = new \WPDM\Template();
    $T->assign('guest_checkout', $guest_checkout);
    $T->assign('cart_data', $cart_data);
    $T->assign('cart_subtotal', $cart_subtotal);
    $T->assign('cart_total', $cart_total);
    $T->assign('cart_tax', $cart_tax);
    $T->assign('cart_total_with_tax', $cart_total_with_tax);
    $T->assign('cart_coupon', $cart_coupon);
    $T->assign('settings', $settings);
    $T->assign('cart_coupon_discount', $cart_coupon_discount);

    return $T->fetch('checkout-cart/cart.php', WPDMPP_BASE_DIR.'templates');
}

/**
 * @usage Shows a requested cart. Hooked to 'wp_loaded'
 */
function wpdmpp_load_saved_cart(){
    if( isset( $_REQUEST['savedcart'] ) ) {
        $cartid = preg_replace("/[^a-zA-Z0-9]*/i", "", $_REQUEST['savedcart']);
        $cartfile = WPDM_CACHE_DIR.'/saved-cart-'.$cartid.'.txt';
        $saved_cart_data = '';

        if(file_exists($cartfile)) $saved_cart_data = file_get_contents($cartfile);
        $saved_cart_data = WPDM_Crypt::Decrypt($saved_cart_data);

        if(is_array($saved_cart_data) && count($saved_cart_data) > 0)
            wpdmpp_update_cart_data($saved_cart_data);

        wpdmpp_redirect(wpdmpp_cart_page());
    }
}

/**
 * @usage Shows Paymnet Options on checkout step. Hooked to 'init'
 */
function wpdmpp_load_payment_methods(){
    if( !wpdm_is_ajax() || !isset($_REQUEST['wpdmpp_load_pms'] ) ) return;
    $settings = get_option('_wpdmpp_settings');
    $guest_checkout = (isset($settings['guest_checkout']) && $settings['guest_checkout'] == 1) ? 1 : 0;
    if(!is_user_logged_in() && !$guest_checkout) die('You are not logged in!');
    $payment_html = "";
    include_once(WPDMPP_BASE_DIR . "/templates/checkout-cart/checkout.php");
    echo $payment_html;
    die();
}

/**
 * Checking product coupon whether valid or not
 *
 * @param $pid
 * @param $coupon
 * @return int
 */
function check_coupon($pid, $coupon){
    $coupon_code = get_post_meta($pid, '__wpdm_coupon_code', true);
    $coupon_discount = get_post_meta($pid, '__wpdm_coupon_discount', true);
    $coupon_expire = get_post_meta($pid, '__wpdm_coupon_expire', true);

    if( is_array( $coupon_code ) ) {
        foreach($coupon_code as $key => $val){
            if($val == $coupon) {
                $expire = isset($coupon_expire[$key])?strtotime($coupon_expire[$key]):0;
                if($expire > 0 && time() > $expire) return 0;
                return $coupon_discount[$key];
            }
        }
    }
    return 0;
}

/**
 * add to cart using form submit
 */
function wpdmpp_add_to_cart(){
    if( isset( $_REQUEST['addtocart']) && intval($_REQUEST['addtocart']) > 0  && get_post_type($_REQUEST['addtocart']) == 'wpdmpro') {
        global $wpdb, $post, $wp_query, $current_user;
        $settings = maybe_unserialize(get_option('_wpdmpp_settings'));

        $pid = (int)$_REQUEST['addtocart'];

        $pid = apply_filters("wpdmpp_add_to_cart", $pid);

        if($pid <= 0) return;
        $sales_price = 0;
        $cart_data = wpdmpp_get_cart_data();

        if(isset($cart_data[$pid])) unset($cart_data[$pid]);

        $q = isset($_REQUEST['quantity']) ? intval($_REQUEST['quantity']) : 1;
        $sfiles = isset($_REQUEST['files']) ? explode(",", $_REQUEST['files']) : array();
        $license = isset($_REQUEST['license']) ? $_REQUEST['license'] : '';
        $license_req = get_post_meta($pid, "__wpdm_enable_license", true);
        $license_prices = get_post_meta($pid, "__wpdm_license", true);
        $license_prices = maybe_unserialize($license_prices);

        $pre_licenses = wpdmpp_get_licenses();
        $files = array();
        $fileinfo = get_post_meta($pid, '__wpdm_fileinfo', true);
        $fileinfo = maybe_unserialize($fileinfo);
        $files_price = 0;

        if(count($sfiles) > 0 && $sfiles[0] != '' && is_array($fileinfo)) {
            foreach ($sfiles as $findx) {
                $files[$findx] = $fileinfo[$findx]['price'];
                if ($license_req == 1 && $license != '' && $fileinfo[$findx]['license_price'][$license] > 0) {
                    $files[$findx] = $fileinfo[$findx]['license_price'][$license];
                }
            }
        }
        if($q < 1) $q = 1;

        $base_price = wpdmpp_product_price($pid);
        if($license_req == 1 && isset($license_prices[$license]['price']) && $license_prices[$license]['price'] > 0)
            $base_price = $license_prices[$license]['price'];

        if( ! isset( $_REQUEST['variation'] ) ) {
            $_REQUEST['variation'] = "";
        }

        if((int)get_post_meta($pid, '__wpdm_pay_as_you_want', true) == 0) {
            // If product id already exist ( Product already added to cart )
            if (array_key_exists($pid, $cart_data)) {
                if (isset($cart_data[$pid]['multi']) && $cart_data[$pid]['multi'] == 1) {
                    $product_data = $cart_data[$pid]['item'];
                    $check = false;
                    foreach ($product_data as $key => $item):

                        //Check same variation exist or not
                        if (wpdmpp_array_diff($item['variation'], $_REQUEST['variation']) == true) {

                            //just incremnet qunatity value
                            $cart_data[$pid]['item'][$key]['quantity'] += $q;
                            $cart_data[$pid]['quantity'] += $q;
                            $check = true;
                            break;
                        }
                    endforeach;

                    if ($check == false) {

                        //Same variation does not exist. Add this item as new item
                        $cart_data[$pid]['item'][] = array(
                            'quantity' => $q,
                            'variation' => isset($_POST['variation']) ? $_POST['variation'] : array()
                        );
                        $cart_data[$pid]['quantity'] += $q;
                    }

                    if (isset($cart_data[$pid]['files'])) {
                        $cart_data[$pid]['files'] = maybe_unserialize($cart_data[$pid]['files']);
                        $cart_data[$pid]['files'] += $files;
                    } else
                        $cart_data[$pid]['files'] = $files;
                    $files_price = array_sum($cart_data[$pid]['files']);
                    //dd($files);
                    $base_price = $files_price > 0 ? $files_price : $base_price;
                } else {


                    if (!isset($_REQUEST['variation']) || $_REQUEST['variation'] == '')
                        $_REQUEST['variation'] = array();

                    if (wpdmpp_array_diff($cart_data[$pid]['variation'], $_REQUEST['variation']) == true) {
                        //no change in variation

                        if (isset($cart_data[$pid]['files'])) {
                            $cart_data[$pid]['files'] = maybe_unserialize($cart_data[$pid]['files']);
                            $cart_data[$pid]['files'] += $files;
                        } else
                            $cart_data[$pid]['files'] = $files;
                        $files_price = array_sum($cart_data[$pid]['files']);
                        //$cart_data[$pid]['quantity'] += $q;
                        if (!isset($cart_data[$pid]['price']) || $cart_data[$pid]['price'] == 0) $cart_data[$pid]['price'] = $files_price;
                        else
                            $cart_data[$pid]['price'] = $cart_data[$pid]['price'] > $files_price && $files_price > 0 ? $files_price : $cart_data[$pid]['price'];
                    } else {
                        //change in variation
                        $old_qty = $cart_data[$pid]['quantity'];
                        $old_variation = $cart_data[$pid]['variation'];
                        $old_files = isset($cart_data[$pid]['files']) ? $cart_data[$pid]['files'] : array();
                        $coupon = isset($cart_data[$pid]['coupon']) ? $cart_data[$pid]['coupon'] : '';
                        $coupon_amount = isset($cart_data[$pid]['coupon_amount']) ? $cart_data[$pid]['coupon_amount'] : '';
                        $discount_amount = isset($cart_data[$pid]['discount_amount']) ? $cart_data[$pid]['discount_amount'] : '';
                        $prices = isset($cart_data[$pid]['prices']) ? $cart_data[$pid]['prices'] : '';
                        $variations = isset($cart_data[$pid]['variations']) ? $cart_data[$pid]['variations'] : '';
                        $new_data = array(
                            'quantity' => $q,
                            'files' => $files,
                            'variation' => isset($_POST['variation']) ? $_POST['variation'] : array(),
                        );

                        $cart_data[$pid] = array();
                        $cart_data[$pid]['multi'] = 1;
                        $cart_data[$pid]['quantity'] = $q + $old_qty;
                        $cart_data[$pid]['price'] = $base_price;
                        $cart_data[$pid]['coupon'] = $coupon;
                        $cart_data[$pid]['item'][] = array(
                            'quantity' => $old_qty,
                            'variation' => $old_variation,
                            'files' => $old_files,
                        );
                        $cart_data[$pid]['item'][] = $new_data;
                    }
                }
            } else {
                // product id does not exist in cart. Add to cart as new item
                $variation = isset($_POST['variation']) ? $_POST['variation'] : array();
                $files_price = array_sum($files);
                $base_price = $files_price > 0 && $files_price < $base_price ? $files_price : $base_price;
                $cart_data[$pid] = array('quantity' => $q, 'variation' => $variation, 'price' => $base_price, 'files' => $files);
            }
        } else {
            //Condition for as you want to pay
            $base_price = isset($_REQUEST['iwantopay'])&&$_REQUEST['iwantopay'] >= $base_price ?(double)$_REQUEST['iwantopay']:$base_price;
            $cart_data[$pid] = array('quantity' => $q, 'variation' => array(), 'price' => $base_price, 'files' => array());
        }

        // Update cart data
        wpdmpp_update_cart_data($cart_data);

        // Calculate all discounts (role based, coupon codes, sales price discount )
        wpdmpp_calculate_discount();

        $settings = get_option('_wpdmpp_settings');

        /* Check if current request is AJAX  */
        if( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
            echo wpdmpp_cart_page();
            die();
        }

        if( $settings['wpdmpp_after_addtocart_redirect'] == 1 ) {
            header( "location: ".wpdmpp_cart_page() );
        }
        else header( "location: ".$_SERVER['HTTP_REFERER'] );
        die();
    }
}


/**
 * add to cart using form submit
 */
function wpdmpp_buynow(){
    if( isset( $_REQUEST['buynow']) && intval($_REQUEST['buynow']) > 0  && get_post_type($_REQUEST['buynow']) == 'wpdmpro') {
        global $wpdb, $post, $wp_query, $current_user;
        $settings = maybe_unserialize(get_option('_wpdmpp_settings'));

        $pid = $_REQUEST['buynow'];

        $pid = apply_filters("wpdmpp_buynow", $pid);

        if($pid <= 0) return;
        $sales_price = 0;
        $cart_data = wpdmpp_get_cart_data();

        $q = isset($_REQUEST['quantity']) ? intval($_REQUEST['quantity']) : 1;
        $sfiles = isset($_REQUEST['files']) ? explode(",", $_REQUEST['files']) : array();
        $license = isset($_REQUEST['license']) ? $_REQUEST['license'] : '';
        $license_req = get_post_meta($pid, "__wpdm_enable_license", true);
        $license_prices = get_post_meta($pid, "__wpdm_license", true);
        $license_prices = maybe_unserialize($license_prices);

        $pre_licenses = wpdmpp_get_licenses();
        $files = array();
        $fileinfo = get_post_meta($pid, '__wpdm_fileinfo', true);
        $fileinfo = maybe_unserialize($fileinfo);
        $files_price = 0;

        if(count($sfiles) > 0 && $sfiles[0] != '' && is_array($fileinfo)) {
            foreach ($sfiles as $findx) {
                $files[$findx] = $fileinfo[$findx]['price'];
                if ($license_req == 1 && $license != '' && $fileinfo[$findx]['license_price'][$license] > 0) {
                    $files[$findx] = $fileinfo[$findx]['license_price'][$license];
                }
            }
        }
        if($q < 1) $q = 1;

        $base_price = wpdmpp_product_price($pid);
        if($license_req == 1 && isset($license_prices[$license]['price']) && $license_prices[$license]['price'] > 0)
            $base_price = $license_prices[$license]['price'];

        if( ! isset( $_REQUEST['variation'] ) ) {
            $_REQUEST['variation'] = "";
        }

        // If product id already exist ( Product already added to cart )
        if( array_key_exists( $pid, $cart_data ) ) {
            if( isset( $cart_data[$pid]['multi'] ) && $cart_data[$pid]['multi'] == 1){
                $product_data = $cart_data[$pid]['item'];
                $check = false;
                foreach ($product_data as $key => $item):

                    //Check same variation exist or not
                    if(wpdmpp_array_diff($item['variation'], $_REQUEST['variation']) == true){

                        //just incremnet qunatity value
                        $cart_data[$pid]['item'][$key]['quantity'] += $q;
                        $cart_data[$pid]['quantity'] += $q;
                        $check = true;
                        break;
                    }
                endforeach;

                if($check == false){

                    //Same variation does not exist. Add this item as new item
                    $cart_data[$pid]['item'][] = array(
                        'quantity'=>$q,
                        'variation'=> isset($_POST['variation'])?$_POST['variation']:array()
                    );
                    $cart_data[$pid]['quantity'] += $q;
                }

                if(isset($cart_data[$pid]['files'])){
                    $cart_data[$pid]['files'] = maybe_unserialize($cart_data[$pid]['files']);
                    $cart_data[$pid]['files'] += $files;
                } else
                    $cart_data[$pid]['files'] = $files;
                $files_price = array_sum($cart_data[$pid]['files']);
                //dd($files);
                $base_price = $files_price > 0 ? $files_price:$base_price;
            }
            else {

                if( ! isset( $_REQUEST['variation'] ) || $_REQUEST['variation'] == '' )
                    $_REQUEST['variation'] = array();

                if( wpdmpp_array_diff( $cart_data[$pid]['variation'], $_REQUEST['variation'] ) == true ) {
                    //no change in variation

                    if(isset($cart_data[$pid]['files'])){
                        $cart_data[$pid]['files'] = maybe_unserialize($cart_data[$pid]['files']);
                        $cart_data[$pid]['files'] += $files;
                    } else
                        $cart_data[$pid]['files'] = $files;
                    $files_price = array_sum($cart_data[$pid]['files']);
                    //$cart_data[$pid]['quantity'] += $q;
                    if(!isset($cart_data[$pid]['price']) || $cart_data[$pid]['price'] == 0) $cart_data[$pid]['price'] = $files_price;
                    else
                        $cart_data[$pid]['price'] = $cart_data[$pid]['price'] > $files_price && $files_price > 0?$files_price:$cart_data[$pid]['price'];
                }
                else {
                    //change in variation
                    $old_qty = $cart_data[$pid]['quantity'];
                    $old_variation = $cart_data[$pid]['variation'];
                    $old_files = isset($cart_data[$pid]['files'])?$cart_data[$pid]['files']:array();
                    $coupon = isset($cart_data[$pid]['coupon']) ? $cart_data[$pid]['coupon'] : '';
                    $coupon_amount = isset($cart_data[$pid]['coupon_amount']) ? $cart_data[$pid]['coupon_amount'] : '';
                    $discount_amount = isset($cart_data[$pid]['discount_amount']) ? $cart_data[$pid]['discount_amount'] : '';
                    $prices = isset($cart_data[$pid]['prices']) ? $cart_data[$pid]['prices'] : '';
                    $variations = isset($cart_data[$pid]['variations']) ? $cart_data[$pid]['variations'] : '';
                    $new_data = array(
                        'quantity'  => $q,
                        'files'  => $files,
                        'variation' => isset($_POST['variation'])?$_POST['variation']:array(),
                    );

                    $cart_data[$pid] = array();
                    $cart_data[$pid]['multi'] = 1;
                    $cart_data[$pid]['quantity'] = $q+$old_qty;
                    $cart_data[$pid]['price'] = $base_price;
                    $cart_data[$pid]['coupon'] = $coupon;
                    $cart_data[$pid]['item'][] = array(
                        'quantity'  => $old_qty,
                        'variation' => $old_variation,
                        'files' => $old_files,
                    );
                    $cart_data[$pid]['item'][] = $new_data;
                }
            }
        } else {
            // product id does not exist in cart. Add to cart as new item
            $variation = isset( $_POST['variation'] ) ? $_POST['variation'] : array();
            $files_price = array_sum($files);
            $base_price = $files_price > 0 && $files_price < $base_price ? $files_price:$base_price;
            $cart_data[$pid] = array( 'quantity' => $q,'variation' => $variation, 'price' => $base_price, 'files' => $files );
        }

        // Update cart data
        wpdmpp_update_cart_data($cart_data);

        // Calculate all discounts (role based, coupon codes, sales price discount )
        wpdmpp_calculate_discount();

        $settings = get_option('_wpdmpp_settings');

        /* Check if current request is AJAX  */
        if( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
            echo wpdmpp_cart_page();
            die();
        }

        if( $settings['wpdmpp_after_addtocart_redirect'] == 1 ) {
            header( "location: ".wpdmpp_cart_page() );
        }
        else header( "location: ".$_SERVER['HTTP_REFERER'] );
        die();
    }
}

/**
 * add to cart from url call
 */
function wpdmpp_add_to_cart_ucb(){

    if( isset( $_REQUEST['addtocart']) && intval($_REQUEST['addtocart']) > 0 && get_post_type($_REQUEST['addtocart']) == 'wpdmpro' ) {

        global $wpdb, $post, $wp_query, $current_user;
        $settings = maybe_unserialize(get_option('_wpdmpp_settings'));
        $pid = $_REQUEST['addtocart'];
        $pid = apply_filters("wpdmpp_add_to_cart", $pid);
        if($pid <= 0) return;

        $sales_price = 0;
        $cart_data = wpdmpp_get_cart_data();

        $q = $_REQUEST['quantity'] ? intval($_REQUEST['quantity']) : 1;
        if($q < 1) $q = 1;

        $base_price = wpdmpp_product_price($pid);

        if( ! isset( $_REQUEST['variation'] ) || $_REQUEST['variation'] == '' )
            $_REQUEST['variation'] = array();

        // If product id already exist ( Product already added to cart )
        if( array_key_exists( $pid, $cart_data ) ) {
            if( isset( $cart_data[$pid]['multi'] ) && $cart_data[$pid]['multi'] == 1 ) {
                $product_data = $cart_data[$pid]['item'];
                $check = false;

                foreach ($product_data as $key => $item):

                    //check same variation exist or not
                    if( wpdmpp_array_diff( $item['variation'], $_REQUEST['variation'] ) == true ) {

                        //just incremnet qunatity value
                        $cart_data[$pid]['item'][$key]['quantity'] += $q;
                        $cart_data[$pid]['quantity'] += $q;
                        $check = true;
                        break;
                    }
                endforeach;

                if($check == false){
                    //Same variation does not exist. Add this item as new item
                    $cart_data[$pid]['item'][] = array(
                        'quantity'  => $q,
                        'variation' => $_POST['variation']
                    );
                    $cart_data[$pid]['quantity'] += $q;
                }
            }
            else {



                if( wpdmpp_array_diff($cart_data[$pid]['variation'] , $_REQUEST['variation']) == true ){
                    //wow just increment product
                    $cart_data[$pid]['quantity'] += $q;
                }
                else {
                    //badluck implement new method
                    $old_qty = $cart_data[$pid]['quantity'];
                    $old_variation = $cart_data[$pid]['variation'];
                    $coupon = isset($cart_data[$pid]['coupon']) ? $cart_data[$pid]['coupon'] : '';
                    $coupon_amount = isset($cart_data[$pid]['coupon_amount']) ? $cart_data[$pid]['coupon_amount'] : '';
                    $discount_amount = isset($cart_data[$pid]['discount_amount']) ? $cart_data[$pid]['discount_amount'] : '';
                    $prices = isset($cart_data[$pid]['prices']) ? $cart_data[$pid]['prices'] : '';
                    $variations = isset($cart_data[$pid]['variations']) ? $cart_data[$pid]['variations'] : '';
                    $new_data = array(
                        'quantity'  => $q,
                        'variation' => $_POST['variation'],
                    );
                    $cart_data[$pid] = array();
                    $cart_data[$pid]['multi'] = 1;
                    $cart_data[$pid]['quantity'] = $q+$old_qty;
                    $cart_data[$pid]['price'] = $base_price;
                    $cart_data[$pid]['coupon'] = $coupon;
                    $cart_data[$pid]['item'][] = array(
                        'quantity'  => $old_qty,
                        'variation' => $old_variation,
                    );
                    $cart_data[$pid]['item'][] = $new_data;
                }
            }
        } else {
            // product id does not exist in cart. Add to cart as new item
            $cart_data[$pid] = array( 'quantity' => $q, 'variation' => $_POST['variation'], 'price' => $base_price );
        }

        wpdmpp_update_cart_data($cart_data);

        wpdmpp_calculate_discount();

        $settings = get_option('_wpdmpp_settings');

        /* Check if current request is AJAX  */
        if( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
            echo get_permalink($settings['page_id']);
            die();
        }

        if( $settings['wpdmpp_after_addtocart_redirect'] == 1 ){
            header( "location: ".get_permalink( $settings['page_id'] ) );
        }
        else header("location: ".$_SERVER['HTTP_REFERER']);
        die();
    }
}

/**
 * Remove a cart entry
 */
function wpdmpp_remove_cart_item(){

    if( !isset($_REQUEST['wpdmpp_remove_cart_item']) || $_REQUEST['wpdmpp_remove_cart_item'] <= 0 ) return;
    $cart_data = wpdmpp_get_cart_data();
    if( isset( $_REQUEST['item_id'] ) ){
        unset($cart_data[$_REQUEST['wpdmpp_remove_cart_item']]['item'][$_REQUEST['item_id']]);
        if( empty($cart_data[$_REQUEST['wpdmpp_remove_cart_item']]['item']) ) {
            unset($cart_data[$_REQUEST['wpdmpp_remove_cart_item']]);
        }
    }
    else{
        unset($cart_data[$_REQUEST['wpdmpp_remove_cart_item']]);
    }
    wpdmpp_update_cart_data($cart_data);

    wpdmpp_calculate_discount();

    $ret['cart_subtotal'] = wpdmpp_get_cart_subtotal();
    $ret['cart_data'] = wpdmpp_get_cart_data();
    $cart_id = wpdmpp_cart_id();
    $coupon = get_option($cart_id."_coupon");
    if(isset($coupon['code']) && $coupon['code'] != ''){
        delete_option($cart_id."_coupon");
        $discount = WPDMPPCouponCodes::validate_coupon($coupon['code']);
        $ret['cart_coupon'] = $coupon['code'];
        $ret['cart_discount'] = $ret['cart_coupon_discount'] = $discount;
        if($discount > 0) update_option($cart_id."_coupon", array('code' => $coupon['code'], 'discount' => $discount));
    }

    $ret['cart_discount'] = wpdmpp_get_cart_discount();
    $ret['cart_total'] = wpdmpp_get_cart_total();

    $ret['cart_tax'] = number_format((double)str_replace(',', '', wpmpp_get_cart_tax()), 2);

    die(json_encode($ret));
}

/**
 * Update Cart items
 */
function wpdmpp_update_cart(){


    if (!isset($_REQUEST['wpdmpp_update_cart']) || (isset($_REQUEST['wpdmpp_update_cart']) && $_REQUEST['wpdmpp_update_cart'] <= 0)) return;


    $data = $_POST['cart_items'];
    $cart_data = wpdmpp_get_cart_data(); //get previous cart data


    foreach ( $cart_data as $pid => $cdt ){
        if( ! $pid || get_post_type($pid) != 'wpdmpro' ) {
            unset( $cart_data[$pid] );
            continue;
        }
        if(isset($data[$pid]['coupon']) && trim($data[$pid]['coupon']) != '') {
            $cart_data[$pid]['coupon'] = stripslashes($data[$pid]['coupon']);
        }
        else {
            unset($cart_data[$pid]['coupon']);
        }

        if( isset( $data[$pid]['item'] ) ) {

            foreach ($data[$pid]['item'] as $key => $val){

                if(isset($val['quantity'])) {
                    if($val['quantity'] < 1 ) $val['quantity'] = 1;
                    $cart_data[$pid]['item'][$key]['quantity'] = $val['quantity'];
                }

                if(isset($cart_data[$pid]['item'][$key]['coupon_amount'])) {
                    unset($cart_data[$pid]['item'][$key]['coupon_amount']);
                }

                if(isset($cart_data[$pid]['item'][$key]['discount_amount'])) {
                    unset($cart_data[$pid]['item'][$key]['discount_amount']);
                }
            }
        } else {

            if( isset($data[$pid]['quantity'] ) ) {

                if( $data[$pid]['quantity'] < 1 ) $data[$pid]['quantity'] = 1;
                $cart_data[$pid]['quantity'] = $data[$pid]['quantity'];
            }

            if(isset($cart_data[$pid]['coupon_amount'])) {
                unset($cart_data[$pid]['coupon_amount']);
            }
        }
    }

    wpdmpp_update_cart_data($cart_data);

    wpdmpp_calculate_discount();

    $ret['cart_subtotal'] = wpdmpp_get_cart_subtotal();
    $ret['cart_discount'] = wpdmpp_get_cart_discount();
    $ret['cart_total'] = wpdmpp_get_cart_total();
    $ret['cart_data'] = wpdmpp_get_cart_data();
    $cart_id = wpdmpp_cart_id();
    delete_option($cart_id."_coupon");
    if(wpdm_query_var('coupon_code') != ''){
        $discount = WPDMPPCouponCodes::validate_coupon(wpdm_query_var('coupon_code'));
        $ret['cart_coupon'] = wpdm_query_var('coupon_code');
        $ret['cart_coupon_discount'] = $discount;
        if($discount > 0) update_option($cart_id."_coupon", array('code' => wpdm_query_var('coupon_code'), 'discount' => $discount));
    }
    if( wpdm_is_ajax() ) {
        die(json_encode($ret));
    }
    header("location: ".wpdmpp_cart_page());
}

/**
 * @return mixed|string|void
 */
function wpdmpp_get_cart_coupon(){
    $cart_id = wpdmpp_cart_id();
    $cart_coupon = get_option($cart_id."_coupon", null);
    if(!isset($cart_coupon['code']) || $cart_coupon['code'] == '') { delete_option($cart_id."_coupon"); $cart_coupon = null; }
    return $cart_coupon;
}

/**
 * Returns Cart ID
 * @return null|string
 */
function wpdmpp_cart_id(){
    global $current_user;
    $cart_id = null;
    if(is_user_logged_in()){
        $cart_id = $current_user->ID."_cart";
    } else {
        $cart_id = md5($_SERVER['REMOTE_ADDR'])."_cart";
    }

    return $cart_id;
}


/**
 * Returns cart data
 * @return array|mixed
 */
function wpdmpp_get_cart_data(){

    global $current_user;

    $cart_id = wpdmpp_cart_id();

    $cart_data = maybe_unserialize(get_option($cart_id));

    //adjust cart id after user log in
    if( is_user_logged_in() && !$cart_data ){
        $cart_id = md5($_SERVER['REMOTE_ADDR'])."_cart";
        $cart_data = maybe_unserialize(get_option($cart_id));
        delete_option($cart_id);
        $cart_id = $current_user->ID."_cart";
        update_option($cart_id, $cart_data);
    }

    return $cart_data ? $cart_data : array();
}

/**
 * @usage Update cart data
 * @param $cart_data
 * @return bool
 */
function wpdmpp_update_cart_data($cart_data){
    global $current_user;

    $cart_id = wpdmpp_cart_id();

    $cart_data = update_option($cart_id, $cart_data);
    return $cart_data;
}

/**
 * Returns cart items
 * @return array|mixed
 */
function wpdmpp_get_cart_items(){
    global $current_user, $wpdb;
    $cart_data = wpdmpp_get_cart_data();
    return ($cart_data);
}

/**
 * Calculate total cart discounts (rolse,sales,coupons)
 */
function wpdmpp_calculate_discount(){
    global $current_user;
    $role                   = is_user_logged_in() ? $current_user->roles[0] : 'guest';
    $discount_r             = 0;
    $cart_items             = wpdmpp_get_cart_items();
    $total                  = 0;
    $currency_sign          = wpdmpp_currency_sign();
    $currency_sign_before   = wpdmpp_currency_sign_position() == 'before' ? $currency_sign : '';
    $currency_sign_after    = wpdmpp_currency_sign_position() == 'after' ? $currency_sign : '';

    if(is_array($cart_items)){
        foreach($cart_items as $pid => $item)    {

            if(!is_array($cart_items[$pid])) $cart_items[$pid] = array();
            $cart_items[$pid]['ID'] = $pid;
            $cart_items[$pid]['post_title'] = get_the_title($pid);
            $prices = 0;
            $variations = "";
            $svariation = array();
            $lvariation = array();
            $lvariations = array();
            $lprices = array();
            $discount = get_post_meta($pid,"__wpdm_discount",true);
            $base_price = get_post_meta($pid,"__wpdm_base_price",true);
            $sales_price = wpdmpp_sales_price($pid);
            $price_variation = get_post_meta($pid,"__wpdm_price_variation",true);
            $variation = get_post_meta($pid,"__wpdm_variation",true);

            if(is_array($variation) && count($variation)>0){
            foreach($variation as $key=>$value){
                foreach($value as $optionkey=>$optionvalue){
                    if($optionkey!="vname" && $optionkey != 'multiple'){

                        if(isset($item['multi']) && ($item['multi'] == 1)){

                            foreach ($item['item'] as $a => $b) { //different variations, $b is single variation contain variation and quantity

                                $lprices[$a] = isset($lprices[$a])?$lprices[$a]:0;
                                if(is_array($b['variation'])):
                                    foreach ($b['variation'] as $c):
                                        if($c == $optionkey) {
                                            $lprices[$a] += $optionvalue['option_price'];
                                            $lvariation[$a][] = $optionvalue['option_name'].": ".($optionvalue['option_price']>0?'+':'').$currency_sign_before.number_format(floatval($optionvalue['option_price']),2,".","").$currency_sign_after;
                                        }
                                    endforeach;
                                endif;
                            }
                        }
                        else{
                            if(isset($item['variation']))
                                foreach($item['variation'] as $var){
                                    if($var==$optionkey){
                                        $prices+=$optionvalue['option_price'];
                                        $svariation[] = $optionvalue['option_name'].": ".($optionvalue['option_price']>0?'+':'').$currency_sign_before.number_format(floatval($optionvalue['option_price']),2,".","").$currency_sign_after;
                                    }
                                }
                        }
                    }
                }
            }
            }

            //if(isset($item['coupon']) && trim($item['coupon'])!='') $valid_coupon = check_coupon($pid,$item['coupon']);
            //else $valid_coupon = false;

            $coupon_discount = (isset($item['coupon']) && trim($item['coupon'])!='' && $pid > 0) ? WPDMPPCouponCodes::validate_coupon(trim($item['coupon']), $pid) : 0;
            $role_discount = isset($discount[$role]) && $discount[$role] > 0?$discount[$role]:0;

            if(!isset($item['multi'])){
                $cart_items[$pid]['prices'] = $prices;
                $cart_items[$pid]['variations'] = $svariation;
                if($coupon_discount) {
                    $cart_items[$pid]['coupon_amount'] =  $coupon_discount;
                    $cart_items[$pid]['discount_amount'] = (((($item['price']+$prices)*$item['quantity'] ) - $coupon_discount ) * $role_discount)/100 ;

                }
                else {

                    $cart_items[$pid]['discount_amount'] = ((($item['price']+$prices)*$item['quantity'] )  * $role_discount)/100;
                }
                if(!$coupon_discount) {
                    if(isset($item['coupon']) && trim($item['coupon'])!='')
                    $cart_items[$pid]['error'] = __('Invalid or Expired Coupon Code','wpdm-premium-packages');
                }
                else {
                    unset($cart_items[$pid]['error']);
                }

            }
            elseif(isset($item['multi']) && $item['multi'] == 1) {

                foreach ($lprices as $key => $value):
                    if(!isset($cart_items[$pid]['item']) || !is_array($cart_items[$pid]['item'])) $cart_items[$pid]['item'] = array();
                    $cart_items[$pid]['item'][$key]['prices'] = $value;
                    $cart_items[$pid]['item'][$key]['variations'] = isset($lvariation[$key])?$lvariation[$key]:array();

                    if($coupon_discount) {
                        $cart_items[$pid]['item'][$key]['coupon_amount'] =   (($item['price']+$value)*$item['item'][$key]['quantity']*$valid_coupon)/100;
                        $cart_items[$pid]['item'][$key]['discount_amount'] =   (((($item['price']+$value)*$item['item'][$key]['quantity']) - $cart_items[$pid]['item'][$key]['coupon_amount'])* $discount[$role])/100 ;
                    }
                    else {
                        $discount[$role] = isset($discount[$role])?$discount[$role]:0;
                        $cart_items[$pid]['item'][$key]['discount_amount'] =   ((($item['price']+$value)*$item['item'][$key]['quantity'])* $discount[$role])/100 ;
                    }

                    if(!$coupon_discount) {
                        if(isset($item['coupon']) && trim($item['coupon'])!='')
                        $cart_items[$pid]['item'][$key]['error'] = __('Invalid or Expired Coupon Code','wpdm-premium-packages');
                    }

                endforeach;
            }
        }
        wpdmpp_update_cart_data($cart_items);
    }
}

/**
 * Return cart total excluding discounts
 * @return string
 */
function wpmpp_get_cart_total(){
    $cart_items = wpdmpp_get_cart_items();

    $total = 0;
    if(is_array($cart_items)){

        foreach($cart_items as $pid=>$item)    {
            if(isset($item['item'])){
                foreach ($item['item'] as $key => $val){
                    $role_discount = isset($val['discount_amount']) ? $val['discount_amount']: 0;
                    $coupon_discount = isset($val['coupon_amount']) ? $val['coupon_amount']: 0;
                    $val['prices'] = isset($val['prices']) ? $val['prices']: 0;
                    $total += (($item['price'] + $val['prices']) * $val['quantity']) - $role_discount - $coupon_discount;
                }
            }
            else {
                $role_discount = isset($item['discount_amount']) ? $item['discount_amount']: 0;
                $coupon_discount = isset($item['coupon_amount']) ? $item['coupon_amount']: 0;
                $total += (($item['price'] + $item['prices'])* $item['quantity']) - $role_discount - $coupon_discount;
            }
        }
    }

    $total = apply_filters('wpdmpp_cart_subtotal',$total);

    return number_format($total, 2, ".", "");
}

function wpmpp_get_cart_tax(){
    return wpdmpp_calculate_tax();
}

function wpdmpp_get_cart_subtotal(){
    $cart_items = wpdmpp_get_cart_items();

    $total = 0;
    if(is_array($cart_items)){
        foreach($cart_items as $pid => $item){
            if(isset($item['item'])){
                foreach ($item['item'] as $key => $val){
                    $role_discount = isset($val['discount_amount']) ? $val['discount_amount']: 0;
                    $coupon_discount = isset($val['coupon_amount']) ? $val['coupon_amount']: 0;
                    $val['prices'] = isset($val['prices']) ? $val['prices']: 0;
                    //$total += (($item['price'] + $val['prices'] - $role_discount - $coupon_discount)*$item['quantity']);
                    $total += ( ( $item['price'] + $val['prices'] - $role_discount ) * $val['quantity'] - $coupon_discount );
                }
            }
            else {
                $role_discount = isset($item['discount_amount']) ? $item['discount_amount']: 0;
                $coupon_discount = isset($item['coupon_amount']) ? $item['coupon_amount']: 0;
                //$total += (($item['price'] + $item['prices'] - $role_discount - $coupon_discount)*$item['quantity']);
                $total += ( ( $item['price'] + $item['prices'] - $role_discount ) * $item['quantity'] - $coupon_discount );
            }
        }
    }

    $total = apply_filters('wpdmpp_cart_subtotal',$total);

    return number_format($total, 2, ".", "");
}

/**
 * Calculating discount
 * @return string
 */
function wpdmpp_get_cart_discount(){
    global $current_user;

    $role = is_user_logged_in() ? $current_user->roles[0] : 'guest';
    $cart_items = wpdmpp_get_cart_items();
    $discount_r = 0;

    foreach($cart_items as $pid => $item){
        $opt = get_post_meta($pid,'wpdmpp_list_opts',true);
        $prices = 0;
        $lprices = array();
        $discount = get_post_meta($pid,"__wpdm_discount",true);
        $base_price = get_post_meta($pid,"__wpdm_base_price",true);
        $sales_price = wpdmpp_sales_price($pid);
        $price_variation = get_post_meta($pid,"__wpdm_price_variation",true);
        $variation = get_post_meta($pid,"__wpdm_variation",true);

        if(is_array($variation) && count($variation) > 0){
        foreach($variation as $key => $value){
            foreach($value as $optionkey => $optionvalue){
                if($optionkey!="vname" && $optionkey != 'multiple'){
                    if(isset($item['variation']) && is_array($item['variation'])){
                        foreach($item['variation'] as $var){
                            if($var == $optionkey){
                                $prices += $optionvalue['option_price'];
                            }
                        }
                    }
                    elseif(isset($item['item']) && !empty ($item['item'])){

                        foreach ($item['item'] as $a => $b) { //different variations, $b is single variation contain variation and quantity
                            if($b['variation']):
                                $lprices[$a] = isset($lprices[$a])?$lprices[$a]:0;
                                foreach ($b['variation'] as $c):
                                    if($c == $optionkey) {
                                        $lprices[$a] += $optionvalue['option_price'];
                                    }
                                endforeach;
                            endif;

                        }
                    }
                }
            }
        }}

        if( ! isset( $discount[$role] ) || ! is_numeric( $discount[$role] ) ) $discount[$role] = 0;

        if(!empty($lprices)):
            foreach($lprices as $key => $val):
                $discount_r += ((($item['price']+$val)*$item['item'][$key]['quantity'])*$discount[$role])/100;
            endforeach;
        else:
            $discount_r +=  ((($item['price']+$prices)*$item['quantity']) * $discount[$role] ) / 100;
        endif;
    }

    $cart_coupon = wpdmpp_get_cart_coupon();
    $discount_r += $cart_coupon['discount'];
    return number_format( $discount_r, 2, ".", "" );
}

/**
 * Calculating subtotal by subtracting discount
 * @return string
 */
function wpdmpp_get_cart_total(){
    $coupon = wpdmpp_get_cart_coupon();
    $subTotal = wpdmpp_get_cart_subtotal();
    $total = $coupon?$subTotal - $coupon['discount']:$subTotal;
    return number_format( $total, 2, ".", "" );
}

function wpdmpp_grand_total(){
    $tax = wpdmpp_calculate_tax();
    return number_format((wpdmpp_get_cart_subtotal() + $tax - wpdmpp_get_cart_discount()),2,".","");
}

//tax calculation
function wpdmpp_calculate_tax($orderid = ''){
    $cartsubtotal = wpdmpp_get_cart_subtotal();
    $tax_total = 0;
    $order = new Order();

    //echo '<pre>';print_r($_SESSION['orderid']);echo '</pre>';

    if($orderid == '' && !isset($_SESSION['orderid'])) return 0;
    if($orderid == '' && isset($_SESSION['orderid'])) $orderid = $_SESSION['orderid'];
    $order_info = $order->GetOrder($orderid);
    $bdata = unserialize($order_info->billing_info);
    $settings = maybe_unserialize(get_option('_wpdmpp_settings'));

    if( isset( $settings['tax']['enable'] ) && $settings['tax']['enable'] == 1 ){
        $rate = wpdmpp_tax_rate($bdata['country'], $bdata['state']);
        $tax_total = ( ( $cartsubtotal * $rate ) / 100 );
    }

    return $tax_total;
}


// Calculate Tax on Cart Sub-total
function wpdmpp_calculate_tax2(){
    $tax_total = 0;
    $cartsubtotal = wpdmpp_get_cart_subtotal();
    $cart_id = wpdmpp_cart_id();
    $coupon = get_option($cart_id."_coupon", array());
    $cartdiscount = isset($coupon['discount'])?$coupon['discount']:0;
    $cartsubtotal -= $cartdiscount;
    $order = new Order();

    $order_info = $order->GetOrder($_SESSION['orderid']);
    $settings = maybe_unserialize(get_option('_wpdmpp_settings'));

    if($settings['tax']['enable'] == 1){
        $rate = wpdmpp_tax_rate($_GET['country'], $_GET['state']);
        $tax_total = ( ( $cartsubtotal * $rate ) / 100 );
    }

    return $tax_total;
}

//tax calculation
function wpdmpp_tax_rate($country, $state = ''){

    $settings = maybe_unserialize(get_option('_wpdmpp_settings'));
    $txrate = 0;
    if(isset($settings['tax']['tax_rate']) && is_array($settings['tax']['tax_rate'])){
        foreach($settings['tax']['tax_rate'] as $key => $rate){

            if($rate['country'] == $country && ( $rate['state'] == $state || $rate['state'] == 'ALL-STATES' ) ){
                $txrate = $rate['rate'];
                break;
            }
        }
    }
    return $txrate;
}


/**
 * Resets the cart data
 */
function wpdmpp_empty_cart(){
    global $current_user;
    $cart_id = wpdmpp_cart_id();
    delete_option($cart_id);
    delete_option($cart_id."_coupon");
    if(isset($_SESSION['orderid'])){
        $_SESSION['last_order'] = $_SESSION['orderid'];
        $_SESSION['orderid'] = '';

        $_SESSION['tax'] = '';
        $_SESSION['subtotal'] = '';

        unset($_SESSION['orderid']);
        unset($_SESSION['tax']);
        unset($_SESSION['subtotal']);
    }
}

function wpdmpp_addtocart_js(){
    if( get_option( 'wpdmpp_ajaxed_addtocart', 0 ) == 0) return;
    ?>
    <script>
        jQuery(function(){
            jQuery('.wpdm-pp-add-to-cart-link').click(function(){
                if(this.href!=''){
                    var lbl;
                    var obj = jQuery(this);
                    lbl = jQuery(this).html();
                    jQuery(this).html('<img src="<?php echo plugins_url();?>/wpdm-premium-packages/assets/images/wait.gif"/> adding...');
                    jQuery.post(this.href,function(){
                        obj.html('added').unbind('click').click(function(){ return false; });
                    })

                }
                return false;
            });

            jQuery('.wpdm-pp-add-to-cart-form').submit(function(){

                var form = jQuery(this);
                var fid = this.id;
                form.ajaxSubmit({
                    'beforeSubmit':function(){
                        jQuery('#submit_'+fid).val('adding...').attr('disabled','disabled');
                    },
                    'success':function(res){
                        jQuery('#submit_'+fid).val('added').attr('disabled','disabled');
                    }
                });

                return false;
            });
        });
    </script>
<?php
}


function update_os(){
    global $wpdb;

    if(!current_user_can(WPDMPP_MENU_ACCESS_CAP)) return;

    $order = new Order();
    $order->Update(array('order_status'=>$_POST['status']),$_POST['order_id']);

    $settings = maybe_unserialize(get_option('_wpdmpp_settings'));
    $siteurl = home_url("/");

    //email to customer of that order
    $userid = $wpdb->get_var("select uid from {$wpdb->prefix}mp_orders where order_id='".$_POST['order_id']."'");
    $user_info = get_userdata($userid);
    $admin_email = get_bloginfo("admin_email");
    $email = array();
    $subject = "Order Status Changed";
    $message = "The order {$_POST['order_id']} is changed to {$_POST['status']}";
    $email['subject'] = $subject;
    $email['body'] = $message;
    $email['headers'] = 'From:  <'.$admin_email.'>' . "\r\n";
    $email = apply_filters("order_status_change_email", $email);

    //wp_mail($user_info->user_email,$email['subject'],$email['body'],$email['headers']);
    //wp_mail($admin_email,$email['subject'],$email['body'],$email['headers']);

    die(__('Order status updated',"wpdm-premium-packages"));
}

function update_ps(){
    if(!current_user_can(WPDMPP_MENU_ACCESS_CAP)) return;
    $order = new Order();
    $order->Update(array('payment_status'=>$_POST['status']),$_POST['order_id']);
    die(__('Payment status updated',"wpdm-premium-packages"));
}

function PayNow($post_data){
    global $wpdb,$current_user;
    
    $order = new Order();
    $corder = $order->GetOrder($post_data['order_id']);
    $payment = new Payment();
    if(!isset($post_data['payment_method']) || $post_data['payment_method']=='')  $post_data['payment_method'] = $corder->payment_method;
    $post_data['payment_method'] = $post_data['payment_method']?$post_data['payment_method']:'PayPal';
    $payment->InitiateProcessor($post_data['payment_method']);
    $payment->Processor->OrderTitle = 'WPMP Order# '.$corder->order_id;
    $payment->Processor->InvoiceNo = $corder->order_id;
    $payment->Processor->Custom = $corder->order_id;
    $payment->Processor->Amount = number_format($corder->total,2,".","");
    echo $payment->Processor->ShowPaymentForm(1);
}

function ProcessOrder(){
    global $current_user;
    
    $order = new Order();

    if(preg_match("@\/payment\/([^\/]+)\/([^\/]+)@is", $_SERVER['REQUEST_URI'], $process)){
        $gateway = $process[1];
        $page = $process[2];
        $_POST['invoice'] = array_shift(explode("_",$_POST['invoice']));
        $odata = $order->GetOrder($_POST['invoice']);
        $current_user = get_userdata($odata->uid);
        $uname = $current_user->display_name;
        $uid = $current_user->ID;
        $email = $current_user->user_email;

        $myorders = get_option('_wpdmpp_users_orders',true);
        if($page == 'notify'){
            if(!$uid) {
                $uname = str_replace(array("@",'.'),'',$_POST['payer_email']);
                $password = $_POST['invoice'];
                $email = $_POST['payer_email'];
                $uid = wp_create_user($uname,$password,$_POST['payer_email']);
                $logininfo = "Username: $uname<br/>Password: $password<br/>";
            }

            $order->Update(array('order_status'=>$_POST['payment_status'],'payment_status'=>$_POST['payment_status'],'uid'=>$uid), $_POST['invoice']);

            $sitename = get_option('blogname');

            $settings = get_option('_wpdmpp_settings');
            $logo = isset($settings['logo_url'])&&$settings['logo_url']!=""?"<img src='{$settings['logo_url']}' alt='".get_bloginfo('name')."'/>":get_bloginfo('name');


            //wp_mail( $email, "You order on ".get_option('blogname'), $message, $headers, $attachments );
            $params = array(
                'date' => date(get_option('date_format'),time()),
                'homeurl' => home_url('/'),
                'sitename' => get_bloginfo('name'),
                'order_link' => "<a href='".wpdmpp_orders_page('id='.$id)."'>".wpdmpp_orders_page('id='.$id)."</a>",
                'register_link' => "<a href='".wpdmpp_orders_page('orderid='.$id)."'>".wpdmpp_orders_page('orderid='.$id)."</a>",
                'name' => $uname,
                'to_email' => get_option('admin_email'),
                'orderid' => $id,
                'order_url' => wpdmpp_orders_page('id='.$id),
                'order_url_admin' => admin_url('edit.php?post_type=wpdmpro&page=orders&task=vieworder&id='.$id),
                'img_logo' => $logo
            );
             
            // to buyer
            //wp_mail($buyer_email,$email['subject'],$email['body'],$email['headers']);
            \WPDM\Email::send("sale-notification", $params);
            die("OK");
        }

        if($page == 'return' && $_POST['payment_status'] == 'Completed'){
            if(!$current_user->ID){
                $uname = str_replace(array("@",'.'),'',$_POST['payer_email']);
                $password = $_POST['invoice'];
                $creds = array();
                $creds['user_login'] = $uname;
                $creds['user_password'] = $password;
                $creds['remember'] = true;
                $user = wp_signon( $creds, false );
            }
            die("<script>location.href='$myorders';</script>");
        }

        die();
    }
}

/**
 * @param $data
 * @return int
 */
function get_all_coupon( $data ){

    if( ! is_array($data) ) return 0;

    $total = 0;

    foreach($data as $pid => $item){
        $valid_coupon = isset($item['coupon']) ? check_coupon($pid, $item['coupon']) : 0;

        if($valid_coupon != 0) {
            $total += ($item['price']*$item['quantity']*($valid_coupon/100));
        }
    }

    return $total;
}

function wpdmpp_product_price_html($product_id){

    $sales_price = wpdmpp_sales_price($product_id);
    $currency_sign = wpdmpp_currency_sign();
    $discount = get_post_meta($product_id,"__wpdm_discount",true);
    $base_price = get_post_meta($product_id,"__wpdm_base_price",true);

    $price_variation = get_post_meta($product_id,"__wpdm_price_variation",true);
    $variation = get_post_meta($product_id,"__wpdm_variation",true);

    $price_html = number_format($base_price, 2, ".", "");

    if($base_price == 0) $price_html = __('Free', 'wpdm-premium-packages');

    ob_start();
    include \WPDM\Template::locate("add-to-cart/price.php", WPDMPP_BASE_DIR.'templates/');
    return ob_get_clean();

}

function wpdmpp_product_license_options_html($product_id){

    //License
    $sales_price = wpdmpp_sales_price($product_id);
    $currency_sign = wpdmpp_currency_sign();
    $pre_licenses = wpdmpp_get_licenses();
    $base_price = get_post_meta($product_id,"__wpdm_base_price",true);
    $license_req = get_post_meta($product_id, "__wpdm_enable_license", true);
    $license_infs = get_post_meta($product_id, "__wpdm_license", true);
    $license_infs = maybe_unserialize($license_infs);
    $active_lics = array();
    $prices = "";
    if($license_req == 1){
        foreach ($pre_licenses as $licid => $lic){
            if(isset($license_infs[$licid]) && $license_infs[$licid]['active'] == 1){
                $lic['price'] = isset($license_infs[$licid]['price'])?$license_infs[$licid]['price']:$base_price;
                $active_lics[$licid] = $lic;
            }
        }

        if(count($active_lics) > 1){
            $license_count  = 0;
            $prices .= '';
        }
    }
    ob_start();
    include \WPDM\Template::locate("add-to-cart/select-license.php", WPDMPP_BASE_DIR.'templates/');
    return ob_get_clean();
}

function wpdmpp_product_gigs_options_html($product_id){
    return include(\WPDM\Template::locate("add-to-cart/extra-gigs.php", WPDMPP_BASE_DIR.'templates/'));
}

/**
 * Build and returns add to cart form
 * @param $product_id
 * @return string
 */
function wpdmpp_add_to_cart_form( $product_id , $template = ''){
    global $current_user, $wpdmpp_settings;
    $discount = get_post_meta($product_id,"__wpdm_discount",true);
    $base_price = get_post_meta($product_id,"__wpdm_base_price",true);
    $sales_price = wpdmpp_sales_price($product_id);
    $price_variation = get_post_meta($product_id,"__wpdm_price_variation",true);
    $variation = get_post_meta($product_id,"__wpdm_variation",true);

    $settings = $wpdmpp_settings;
    $cart_enable = "";
    $cart_enable = apply_filters("wpdmpp_cart_enable", $cart_enable, $product_id);
    $currency_sign = wpdmpp_currency_sign();
    $discount = is_user_logged_in() && isset($discount[$current_user->roles[0]]) ? $discount[$current_user->roles[0]] : 0;
    $role = is_user_logged_in()?$current_user->roles[0]:'';
    $base_price = (double)$base_price;
    $prices_text = apply_filters('price_text',__('Price','wpdm-premium-packages'));
    $add_to_cart_button_label = "<i class='fa fa-shopping-cart'></i> &nbsp;".__("Add to Cart","wpdm-premium-packages");
    $add_to_cart_button_label = apply_filters('add_to_cart_button_label', $add_to_cart_button_label, $product_id);
    $add_to_cart_button_class = apply_filters('add_to_cart_button_class', 'btn btn-primary btn-addtocart', $product_id);

    $price_html = number_format($base_price, 2, ".", "");

    if($base_price == 0) $price_html = __('Free', 'wpdm-premium-packages');

    ob_start();
    if((int)get_post_meta($product_id, '__wpdm_pay_as_you_want', true) == 1)
        include \WPDM\Template::locate("add-to-cart/pay-as-you-want-form.php", WPDMPP_BASE_DIR.'templates');
    else
        include \WPDM\Template::locate("add-to-cart/form.php", WPDMPP_BASE_DIR.'templates');
    $cart_form = ob_get_clean();
    return $cart_form;
}


function wpdmpp_add_to_cart_html( $product_id , $template = ''){

    $form = wpdmpp_add_to_cart_form($product_id);
    if((int)get_post_meta($product_id, '__wpdm_pay_as_you_want', true) == 1)
        return $form;

    ob_start();
    include \WPDM\Template::locate("add-to-cart/price.php", WPDMPP_BASE_DIR.'templates');
    $price = ob_get_clean();
    $html = $price.$form;
    return $html;


}

/**
 * @param $post
 * @param string $btnclass
 * @return string
 */
function wpdmpp_waytocart($post, $btnclass = 'btn-info'){

    $post = (array) $post;
    $price_variation = get_post_meta($post['ID'], '__wpdm_price_variation', true);

    $pre_licenses = wpdmpp_get_licenses();

    $license_req = get_post_meta($post['ID'], "__wpdm_enable_license", true);
    $license_infs = get_post_meta($post['ID'], "__wpdm_license", true);
    $license_infs = maybe_unserialize($license_infs);
    $active_lics = array();
    $currency_sign = wpdmpp_currency_sign();
    $base_price = wpdmpp_product_price($post['ID']);
    /*
    if($license_req == 1) {
        foreach ($pre_licenses as $licid => $lic) {
            if (isset($license_infs[$licid]) && $license_infs[$licid]['active'] == 1) {
                $lic['price'] = isset($license_infs[$licid]['price']) ? $license_infs[$licid]['price'] : $base_price;
                $active_lics[$licid] = $lic;
            }
        }

        if (count($active_lics) > 1) {
            $vcount = 0;
            $license_html = '<div class="btn-group"><a href="#" class="btn '.$btnclass.'  btn-addtocart">' . __('Add to Cart', 'wpdm-premium-packages') . '</a><button type="button" class="btn '.$btnclass.'  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu">';
            foreach ($active_lics as $licid => $lic) {
                $vari = (floatval($lic['price']) != 0) ? " ( {$currency_sign}" . number_format($lic['price'], 2, ".", "") . " )" : "";
                $license_html .= '<li><a href="#">' . " " . $lic['name'] . $vari . "</a></li>";
                $vcount++;
            }
            $license_html .= '</div>';
            return $license_html;
        }
    }
    */

    // Product is FREE
    if( ! $price_variation && wpdmpp_product_price($post['ID']) == 0 )
        return '<a href="'.get_permalink($post['ID']).'" class="btn '.$btnclass.'  btn-addtocart" ><i class="fa fa-download icon-white"></i> '.__("Download","wpdm-premium-packages").'</a>';

    // Product is Premium
    if( $price_variation ) {
        $html = "<a href='" . get_permalink($post['ID']) . "' class='btn $btnclass' ><i class='fa fa-shopping-cart icon-white'></i> " . __("Add to Cart", "wpdm-premium-packages") . "</a>";
    }else{
        $html = <<<PRICE
<form method="post" action="" name="cart_form" class="wpdm_cart_form" id="wpdm_cart_form_{$post['ID']}">
    <input type="hidden" name="addtocart" value="{$post['ID']}">
PRICE;

        $html .= '<div class="btn-group"><button class="btn '.$btnclass.'  btn-addtocart" type="submit" ><i class="fa fa-shopping-cart icon-white"></i> '.__("Add to Cart","wpdm-premium-packages").'</button></div></form>';
    }

    return $html;
}

/**
 * @param $user_login
 * @param $user
 */
function wpdmpp_clear_user_cartdata($user_login, $user = null) {
    delete_option($user->ID."_cart");
}
add_action('wp_login', 'wpdmpp_clear_user_cartdata', 10, 2);

/**
 * @usage Finds if two arrays are same. Used in WPDMPP to check if same variation of product exist in cart or not
 * @param $a
 * @param $b
 * @return bool
 */
function wpdmpp_array_diff($a, $b){

    if( is_array( $a ) && is_array( $b ) ) {
        if( count( $a ) != count( $b ) ) {
            return false;
        }
        else {
            sort( $a );
            sort( $b );
            return $a == $b  ;
        }
    }
    else if( $a == "" && $b == "" ){
        return true;
    }
}