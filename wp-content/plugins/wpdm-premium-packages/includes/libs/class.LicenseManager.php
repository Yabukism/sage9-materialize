<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if( ! class_exists( 'LicenseManager' ) ):

    class LicenseManager{

        function __construct()
        {
            add_action( 'init', array( $this, 'wpdmpp_getlicensekey' ) );
            add_action( 'init', array( $this, 'add_new_license' ) );
            add_action( 'init', array( $this, 'update_license' ) );
            add_action( 'init', array( $this, 'validate_license_key' ) );
            add_action( 'init', array( $this, 'unlock_license_key' ) );
        }

        public static function generate_licensekey(){
            $licenseno = strtoupper(substr(uniqid(rand()), 3, 5) . '-' . substr(uniqid(rand()), 3, 5) . '-' . substr(uniqid(rand()), 3, 5) . '-' . substr(uniqid(rand()), 3, 5));
            return $licenseno;
        }

        function unlock_license_key(){
            if(current_user_can(WPDMPP_ADMIN_CAP) && isset($_REQUEST['unlock_license']) && isset( $_POST['__suc'] ) && wp_verify_nonce( $_POST['__suc'], NONCE_KEY ) ){
                global $wpdb;
                $wpdb->update("{$wpdb->prefix}ahm_licenses", array('domain' => ''), array('id' => (int)$_REQUEST['unlock_license']));
                die('ok');
            }
        }

        function validate_license_key(){
            global $wpdb;
            //print_r($_REQUEST);die('ok');
            if(!isset($_REQUEST['wpdmLicense'],$_REQUEST['licenseKey'], $_REQUEST['domain'], $_REQUEST['productId'])) return;

            $licenseKey = $_REQUEST['licenseKey'];
            $domain = $_REQUEST['domain'];
            $productId = $_REQUEST['productId'];
            $license = $wpdb->get_row("select * from {$wpdb->prefix}ahm_licenses where licenseno = '$licenseKey'");
            if($license){
                $domains = maybe_unserialize($license->domain);
                if(!is_array($domains)) $domains = array();
                if($license->oid != '') {
                    $order = new Order($license->oid);
                    if($order->order_id != '' && !in_array($order->order_status, array('Completed', 'Expired', 'Gifted'))){
                        header("Content-type: application/json");
                        echo json_encode(array('status' => 'INVALID', 'error' => 'ORDER_ISSUE', 'order_status' => $order->order_status));
                    }
                }

                if($license->status == 0) $validity = array('status' => 'INACTIVE', 'error' => 'NOT_ACTIVE');
                else if($productId != $license->pid) $validity = array('status' => 'INVALID', 'error' => 'INVALID_PRODUCT');
                else if(count($domains) >= $license->domain_limit && $license->domain_limit > 0 && !in_array($domain, $domains)) $validity = array('status' => 'INVALID', 'error' => 'USAGE_LIMIT_REACHED');
                else if( (count($domains) < $license->domain_limit || $license->domain_limit == 0 ) && !in_array($domain, $domains)) {
                    $domains[] = $domain;
                    $wpdb->update("{$wpdb->prefix}ahm_licenses", array('domain' => serialize($domains)), array('id' => $license->id));
                    $validity = array('status' => 'VALID', 'expire_date' => $license->expire_date, 'activation_date' => $license->activation_date);
                }
                else if(in_array($domain, $domains)){
                    $status = ($license->expire_date > time() || $license->expire_date == 0)?'VALID':'EXPIRED';
                    $validity = array('status' => $status, 'expire_date' => $license->expire_date, 'activation_date' => $license->activation_date);
                } else {
                    $validity = array('status' => 'INVALID', 'error' => 'USAGE_LIMIT_REACHED');
                }
            }
            header("Content-type: application/json");
            echo json_encode($validity);
            die();
        }

        function add_new_license(){
            if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'addlicense' && current_user_can(WPDMPP_ADMIN_CAP) && isset( $_POST['__suc'] ) && wp_verify_nonce( $_POST['__suc'], NONCE_KEY ) ){
                global $wpdb;
                $license = $_REQUEST['license'];
                if(trim($license['domain']) != '') {
                    $license['domain'] = str_replace("\r", "", $license['domain']);
                    $license['domain'] = explode("\n", $license['domain']);
                    $license['domain'] = array_unique($license['domain']);
                    $license['domain'] = serialize($license['domain']);
                }
                $license['activation_date'] = strtotime($license['activation_date']);
                if($license['expire_date'] != '')
                    $license['expire_date'] = strtotime($license['expire_date']);
                else
                    $license['expire_date'] = 0;
                $wpdb->insert("{$wpdb->prefix}ahm_licenses", $license);
                header("location: edit.php?post_type=wpdmpro&page=pp-license");
                die();
            }
        }

        function update_license(){
            if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'updatelicense' && current_user_can(WPDMPP_ADMIN_CAP) && isset( $_POST['__suc'] ) && wp_verify_nonce( $_POST['__suc'], NONCE_KEY ) ){
                global $wpdb;
                $license = $_REQUEST['license'];
                if(trim($license['domain']) != '') {
                    $license['domain'] = str_replace("\r", "", $license['domain']);
                    $license['domain'] = explode("\n", $license['domain']);
                    $license['domain'] = array_unique($license['domain']);
                    $license['domain'] = serialize($license['domain']);
                }
                $license['activation_date'] = strtotime($license['activation_date']);
                if($license['expire_date'] != '')
                    $license['expire_date'] = strtotime($license['expire_date']);
                else
                    $license['expire_date'] = 0;
                $wpdb->update("{$wpdb->prefix}ahm_licenses", $license, array('id' => $_REQUEST['id']));
                header("location: edit.php?post_type=wpdmpro&page=pp-license");
                die();
            }
        }

        function wpdmpp_getlicensekey()
        {
            if (!isset($_REQUEST['execute']) || $_REQUEST['execute'] != 'getlicensekey' || !is_user_logged_in()) return;
            global $wpdb, $current_user;
            $oid = esc_attr($_REQUEST['orderid']);
            $pid = intval($_REQUEST['fileid']);
            $order = new Order();
            $odata = $order->GetOrder($oid);
            $items = unserialize($odata->items);

            if (in_array($pid, $items) && $odata->order_status == 'Completed' && $current_user->ID == $odata->uid) {
                $licenseno = $wpdb->get_var("select licenseno from {$wpdb->prefix}ahm_licenses where oid='{$oid}' and pid='{$pid}'");
                if (!$licenseno) {
                    $licenseno = strtoupper(substr(uniqid(rand()), 3, 5) . '-' . substr(uniqid(rand()), 3, 5) . '-' . substr(uniqid(rand()), 3, 5) . '-' . substr(uniqid(rand()), 3, 5));
                    $wpdb->insert("{$wpdb->prefix}ahm_licenses", array('licenseno' => $licenseno, 'status' => 0, 'oid' => $oid, 'pid' => $pid));
                    die($licenseno);
                } else
                    die($licenseno);

            } else die('error!');
        }

        function wpdm_pp_add_domain()
        {
            if (!$_POST || !$_GET['id']) return;
            global $current_user, $wpdb;
            
            $order = new Order();
            $item = (int)$_GET['item'];
            $ord = $order->GetOrder($_GET['id']);
            $cart_data = unserialize($ord->cart_data);
            $mxd = $cart_data[$item] ? $cart_data[$item] : 1;
            if ($ord->uid != $current_user->ID || $_POST['domain'] == '' || !$current_user->ID || $ord->uid == '') return false;
            $oid = mysqli_real_escape_string($_GET['id']);
            $lic = $wpdb->get_row("select * from {$wpdb->prefix}ahm_licenses where oid='$oid' and pid='$item'");

            $domain = is_array(unserialize($lic->domain)) ? unserialize($lic->domain) : array($lic->domain);
            $licenseno = strtoupper(substr(uniqid(rand()), 3, 5) . '-' . substr(uniqid(rand()), 3, 5) . '-' . substr(uniqid(rand()), 3, 5) . '-' . substr(uniqid(rand()), 3, 5));
            if (count($domain) == 1 && $domain[0] == '') $domain = array();

            if (count($domain) < $mxd) {
                $domain[] = str_replace(array("http://", "https://", "www."), "", strtolower($_POST['domain']));
                $domain = array_unique($domain);

                if ($lic->id > 0)
                    $wpdb->update("{$wpdb->prefix}ahm_licenses", array('domain' => serialize($domain)), array('oid' => $oid, 'pid' => $item));
                else
                    $wpdb->insert("{$wpdb->prefix}ahm_licenses", array('domain' => serialize($domain), 'licenseno' => $licenseno, 'oid' => $oid, 'pid' => $item));
            }

            header("location: $_SERVER[HTTP_REFERER]");
            die();
        }

        /**
         * Update License information
         */
        function wpdm_pp_update_license()
        {
            global $wpdb;
            if ($_GET['task'] != 'editlicense' || !is_array($_POST['license'])) return;
            $id = (int)$_POST['lid'];
            $lic = $_POST['license'];
            $lic = explode("\n", str_replace(array("\r", "http://", "www."), "", $lic['domain']));
            $lic['domain'] = trim(implode("",$lic))!=""?serialize($lic):"";
            $lic['activation_date'] = strtotime($lic['activation_date']);
            $lic['expire_date'] = $lic['activation_date'] + ($lic['expire_period'] * 86400);
            $wpdb->update("{$wpdb->prefix}ahm_licenses", $lic, array('id' => $id));
            header("location: edit.php?post_type=wpdmpro&page=pp-license");
            die();
        }

    }

endif;

new LicenseManager();
