<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

//number of total sales
function wpdmpp_total_purchase($pid = '')
{
    global $wpdb;
    if (!$pid) $pid = get_the_ID();
    $sales = $wpdb->get_var("select count(*) from {$wpdb->prefix}ahm_orders o, {$wpdb->prefix}ahm_order_items oi where oi.oid=o.order_id and oi.pid='$pid' and o.payment_status='Completed'");

    return $sales;
}

//number of total sales
function wpdmpp_total_sales($uid = '', $pid = '', $sdate = '', $edate = '')
{
    global $wpdb;

    $pid_cond = ($pid > 0)?"and oi.pid='$pid'":"";
    $uid_cond = ($uid > 0)?"and oi.sid='$uid'":"";

    $sdate = $sdate == ''?date("Y-m-01"):$sdate;
    $edate = $edate == ''?date("Y-m-d", strtotime("last day of this month")):$edate;
    $sdate_cond = $sdate != ''? " and o.date >= '".strtotime($sdate)."'":"and o.date >= '".strtotime(date("Y-m-01"))."'";
    $edate_cond = $sdate != ''? " and o.date <= '".strtotime($edate)."'":"and o.date <= '".strtotime(date("Y-m-d", strtotime("last day of this month")))."'";

    if($pid_cond != '' || $uid_cond != '')
        $sales = $wpdb->get_var("select sum(oi.price) from {$wpdb->prefix}ahm_orders o, {$wpdb->prefix}ahm_order_items oi where oi.oid=o.order_id {$pid_cond} {$uid_cond} {$sdate_cond} {$edate_cond} and o.payment_status='Completed'");
    else
        $sales = $wpdb->get_var("select sum(o.total) from {$wpdb->prefix}ahm_orders o where o.payment_status='Completed' {$sdate_cond} {$edate_cond}");

    return number_format($sales, 2, '.', '');
}


function wpdmpp_daily_sales($uid = '', $pid = '', $sdate = '', $edate = ''){
    global $wpdb;

    $pid_cond = ($pid > 0)?"and oi.pid='$pid'":"";
    $uid_cond = ($uid > 0)?"and oi.sid='$uid'":"";
    $sdate = $sdate == ''?date("Y-m-01"):$sdate;
    $edate = $edate == ''?date("Y-m-d", strtotime("last day of this month")):$edate;
    $sdate_cond = $sdate != ''? " and o.date >= '".strtotime($sdate)."'":"and o.date >= '".strtotime(date("Y-m-01"))."'";
    $edate_cond = $sdate != ''? " and o.date <= '".strtotime($edate)."'":"and o.date <= '".strtotime(date("Y-m-d", strtotime("last day of this month")))."'";

    $sales = $wpdb->get_results("select sum(oi.price) as daily_sale,  sum(oi.quantity) as quantities, oi.date, oi.year, oi.month, oi.day from {$wpdb->prefix}ahm_orders o, {$wpdb->prefix}ahm_order_items oi where oi.oid=o.order_id {$pid_cond} {$uid_cond} {$sdate_cond} {$edate_cond} and o.payment_status='Completed' group by oi.date");

    $diff = date_diff(date_create($edate), date_create($sdate))->days;
    $sdata = array();
    $i = 0;
    do{
        $i++;
        $sdata['sales'][$sdate] = 0;
        $sdata['quantities'][$sdate] = 0;
        $sdate = date('Y-m-d', strtotime('+1 day', strtotime($sdate)));
    }while($i <= $diff);

    foreach ($sales as $sale){
        $sdata['sales'][$sale->date] = $sale->daily_sale;
        $sdata['quantities'][$sale->date] = $sale->quantities;
    }

    return $sdata;
}

function wpdmpp_top_sellings_products($uid = '', $sdate = '', $edate = '', $s = 0, $e = 1000){
    global $wpdb;

    $uid_cond = ($uid > 0)?"and oi.sid='$uid'":"";
    //$sdate = $sdate == ''?date("Y-m-01"):$sdate;
    //$edate = $edate == ''?date("Y-m-31"):$edate;
    $sdate_cond = $sdate != ''? " and o.date >= '".strtotime($sdate)."'":"";
    $edate_cond = $sdate != ''? " and o.date <= '".strtotime($edate)."'":"";

    $tsp = $wpdb->get_results("select p.post_title, sum(oi.price) as sales,  sum(oi.quantity) as quantities, oi.date, oi.year, oi.month, oi.day from {$wpdb->prefix}ahm_orders o, {$wpdb->prefix}posts p, {$wpdb->prefix}ahm_order_items oi where oi.oid=o.order_id  {$uid_cond} {$sdate_cond} {$edate_cond} and o.payment_status='Completed' and oi.pid = p.ID group by oi.pid ORDER BY quantities DESC limit $s, $e");
    return $tsp;
}

function wpdmpp_recent_sales($uid = '', $count = 10){
    global $wpdb;

    $uid_cond = ($uid > 0)?"and oi.sid='$uid'":"";

    $tsp = $wpdb->get_results("select p.post_title, oi.price, o.date as time_stamp,  oi.date, oi.year, oi.month, oi.day from {$wpdb->prefix}ahm_orders o, {$wpdb->prefix}posts p, {$wpdb->prefix}ahm_order_items oi where oi.oid=o.order_id  {$uid_cond} and o.payment_status='Completed' and oi.pid = p.ID ORDER BY o.date DESC limit 0, $count");
    return $tsp;
}

function wpdmpp_get_licenses(){
    $pre_licenses = get_wpdmpp_option('licenses',array(
        'statdard' => array( 'name' => 'Standard', 'use' => 1),
        'extended' => array( 'name' => 'Extended', 'use' => 5),
        'unlimited' => array( 'name' => 'Unlimited', 'use' => 99),
    ));
    $pre_licenses = maybe_unserialize($pre_licenses);
    return $pre_licenses;

}

function get_wpdmpp_option($name, $default = '')
{
    global $wpdmpp_settings;

    $name = explode('/', $name);

    if (count($name) == 1)
        return isset($wpdmpp_settings[$name[0]]) ? $wpdmpp_settings[$name[0]] : $default;
    else if (count($name) == 2)
        return isset($wpdmpp_settings[$name[0]]) && isset($wpdmpp_settings[$name[0]][$name[1]]) ? $wpdmpp_settings[$name[0]][$name[1]] : $default;
    else if (count($name) == 3)
        return isset($wpdmpp_settings[$name[0]]) && isset($wpdmpp_settings[$name[0]][$name[1]]) && isset($wpdmpp_settings[$name[0]][$name[1]][$name[2]]) ? $wpdmpp_settings[$name[0]][$name[1]][$name[2]] : $default;
    else
        return $default;
}

function wpdmpp_countries(){
    return array ( 'AF' => 'AFGHANISTAN', 'AL' => 'ALBANIA', 'DZ' => 'ALGERIA', 'AS' => 'AMERICAN SAMOA', 'AD' => 'ANDORRA', 'AO' => 'ANGOLA', 'AI' => 'ANGUILLA', 'AQ' => 'ANTARCTICA', 'AG' => 'ANTIGUA AND BARBUDA', 'AR' => 'ARGENTINA', 'AM' => 'ARMENIA', 'AW' => 'ARUBA', 'AU' => 'AUSTRALIA', 'AT' => 'AUSTRIA', 'AZ' => 'AZERBAIJAN', 'BS' => 'BAHAMAS', 'BH' => 'BAHRAIN', 'BD' => 'BANGLADESH', 'BB' => 'BARBADOS', 'BY' => 'BELARUS', 'BE' => 'BELGIUM', 'BZ' => 'BELIZE', 'BJ' => 'BENIN', 'BM' => 'BERMUDA', 'BT' => 'BHUTAN', 'BO' => 'BOLIVIA', 'BA' => 'BOSNIA AND HERZEGOVINA', 'BW' => 'BOTSWANA', 'BV' => 'BOUVET ISLAND', 'BR' => 'BRAZIL', 'IO' => 'BRITISH INDIAN OCEAN TERRITORY', 'BN' => 'BRUNEI DARUSSALAM', 'BG' => 'BULGARIA', 'BF' => 'BURKINA FASO', 'BI' => 'BURUNDI', 'KH' => 'CAMBODIA', 'CM' => 'CAMEROON', 'CA' => 'CANADA', 'CV' => 'CAPE VERDE', 'KY' => 'CAYMAN ISLANDS', 'CF' => 'CENTRAL AFRICAN REPUBLIC', 'TD' => 'CHAD', 'CL' => 'CHILE', 'CN' => 'CHINA', 'CX' => 'CHRISTMAS ISLAND', 'CC' => 'COCOS (KEELING) ISLANDS', 'CO' => 'COLOMBIA', 'KM' => 'COMOROS', 'CG' => 'CONGO', 'CD' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'CK' => 'COOK ISLANDS', 'CR' => 'COSTA RICA', 'CI' => 'COTE DIVOIRE', 'HR' => 'CROATIA', 'CU' => 'CUBA', 'CY' => 'CYPRUS', 'CZ' => 'CZECH REPUBLIC', 'DK' => 'DENMARK', 'DJ' => 'DJIBOUTI', 'DM' => 'DOMINICA', 'DO' => 'DOMINICAN REPUBLIC', 'EC' => 'ECUADOR', 'EG' => 'EGYPT', 'SV' => 'EL SALVADOR', 'GQ' => 'EQUATORIAL GUINEA', 'ER' => 'ERITREA', 'EE' => 'ESTONIA', 'ET' => 'ETHIOPIA', 'FK' => 'FALKLAND ISLANDS (MALVINAS)', 'FO' => 'FAROE ISLANDS', 'FJ' => 'FIJI', 'FI' => 'FINLAND', 'FR' => 'FRANCE', 'GF' => 'FRENCH GUIANA', 'PF' => 'FRENCH POLYNESIA', 'TF' => 'FRENCH SOUTHERN TERRITORIES', 'GA' => 'GABON', 'GM' => 'GAMBIA', 'GE' => 'GEORGIA', 'DE' => 'GERMANY', 'GH' => 'GHANA', 'GI' => 'GIBRALTAR', 'GR' => 'GREECE', 'GL' => 'GREENLAND', 'GD' => 'GRENADA', 'GP' => 'GUADELOUPE', 'GU' => 'GUAM', 'GT' => 'GUATEMALA', 'GN' => 'GUINEA', 'GW' => 'GUINEA-BISSAU', 'GY' => 'GUYANA', 'HT' => 'HAITI', 'HM' => 'HEARD ISLAND AND MCDONALD ISLANDS', 'VA' => 'HOLY SEE (VATICAN CITY STATE)', 'HN' => 'HONDURAS', 'HK' => 'HONG KONG', 'HU' => 'HUNGARY', 'IS' => 'ICELAND', 'IN' => 'INDIA', 'ID' => 'INDONESIA', 'IR' => 'IRAN, ISLAMIC REPUBLIC OF', 'IQ' => 'IRAQ', 'IE' => 'IRELAND', 'IL' => 'ISRAEL', 'IT' => 'ITALY', 'JM' => 'JAMAICA', 'JP' => 'JAPAN', 'JO' => 'JORDAN', 'KZ' => 'KAZAKHSTAN', 'KE' => 'KENYA', 'KI' => 'KIRIBATI', 'KP' => 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF', 'KR' => 'KOREA, REPUBLIC OF', 'KW' => 'KUWAIT', 'KG' => 'KYRGYZSTAN', 'LA' => 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC', 'LV' => 'LATVIA', 'LB' => 'LEBANON', 'LS' => 'LESOTHO', 'LR' => 'LIBERIA', 'LY' => 'LIBYAN ARAB JAMAHIRIYA', 'LI' => 'LIECHTENSTEIN', 'LT' => 'LITHUANIA', 'LU' => 'LUXEMBOURG', 'MO' => 'MACAO', 'MK' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'MG' => 'MADAGASCAR', 'MW' => 'MALAWI', 'MY' => 'MALAYSIA', 'MV' => 'MALDIVES', 'ML' => 'MALI', 'MT' => 'MALTA', 'MH' => 'MARSHALL ISLANDS', 'MQ' => 'MARTINIQUE', 'MR' => 'MAURITANIA', 'MU' => 'MAURITIUS', 'YT' => 'MAYOTTE', 'MX' => 'MEXICO', 'FM' => 'MICRONESIA, FEDERATED STATES OF', 'MD' => 'MOLDOVA, REPUBLIC OF', 'MC' => 'MONACO', 'MN' => 'MONGOLIA', 'MS' => 'MONTSERRAT', 'MA' => 'MOROCCO', 'MZ' => 'MOZAMBIQUE', 'MM' => 'MYANMAR', 'NA' => 'NAMIBIA', 'NR' => 'NAURU', 'NP' => 'NEPAL', 'NL' => 'NETHERLANDS', 'AN' => 'NETHERLANDS ANTILLES', 'NC' => 'NEW CALEDONIA', 'NZ' => 'NEW ZEALAND', 'NI' => 'NICARAGUA', 'NE' => 'NIGER', 'NG' => 'NIGERIA', 'NU' => 'NIUE', 'NF' => 'NORFOLK ISLAND', 'MP' => 'NORTHERN MARIANA ISLANDS', 'NO' => 'NORWAY', 'OM' => 'OMAN', 'PK' => 'PAKISTAN', 'PW' => 'PALAU', 'PS' => 'PALESTINIAN TERRITORY, OCCUPIED', 'PA' => 'PANAMA', 'PG' => 'PAPUA NEW GUINEA', 'PY' => 'PARAGUAY', 'PE' => 'PERU', 'PH' => 'PHILIPPINES', 'PN' => 'PITCAIRN', 'PL' => 'POLAND', 'PT' => 'PORTUGAL', 'PR' => 'PUERTO RICO', 'QA' => 'QATAR', 'RE' => 'REUNION', 'RO' => 'ROMANIA', 'RU' => 'RUSSIAN FEDERATION', 'RW' => 'RWANDA', 'SH' => 'SAINT HELENA', 'KN' => 'SAINT KITTS AND NEVIS', 'LC' => 'SAINT LUCIA', 'PM' => 'SAINT PIERRE AND MIQUELON', 'VC' => 'SAINT VINCENT AND THE GRENADINES', 'WS' => 'SAMOA', 'SM' => 'SAN MARINO', 'ST' => 'SAO TOME AND PRINCIPE', 'SA' => 'SAUDI ARABIA', 'SN' => 'SENEGAL', 'CS' => 'SERBIA AND MONTENEGRO', 'SC' => 'SEYCHELLES', 'SL' => 'SIERRA LEONE', 'SG' => 'SINGAPORE', 'SK' => 'SLOVAKIA', 'SI' => 'SLOVENIA', 'SB' => 'SOLOMON ISLANDS', 'SO' => 'SOMALIA', 'ZA' => 'SOUTH AFRICA', 'GS' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'ES' => 'SPAIN', 'LK' => 'SRI LANKA', 'SD' => 'SUDAN', 'SR' => 'SURINAME', 'SJ' => 'SVALBARD AND JAN MAYEN', 'SZ' => 'SWAZILAND', 'SE' => 'SWEDEN', 'CH' => 'SWITZERLAND', 'SY' => 'SYRIAN ARAB REPUBLIC', 'TW' => 'TAIWAN, PROVINCE OF CHINA', 'TJ' => 'TAJIKISTAN', 'TZ' => 'TANZANIA, UNITED REPUBLIC OF', 'TH' => 'THAILAND', 'TL' => 'TIMOR-LESTE', 'TG' => 'TOGO', 'TK' => 'TOKELAU', 'TO' => 'TONGA', 'TT' => 'TRINIDAD AND TOBAGO', 'TN' => 'TUNISIA', 'TR' => 'TURKEY', 'TM' => 'TURKMENISTAN', 'TC' => 'TURKS AND CAICOS ISLANDS', 'TV' => 'TUVALU', 'UG' => 'UGANDA', 'UA' => 'UKRAINE', 'AE' => 'UNITED ARAB EMIRATES', 'GB' => 'UNITED KINGDOM', 'US' => 'UNITED STATES', 'UM' => 'UNITED STATES MINOR OUTLYING ISLANDS', 'UY' => 'URUGUAY', 'UZ' => 'UZBEKISTAN', 'VU' => 'VANUATU', 'VE' => 'VENEZUELA', 'VN' => 'VIET NAM', 'VG' => 'VIRGIN ISLANDS, BRITISH', 'VI' => 'VIRGIN ISLANDS, U.S.', 'WF' => 'WALLIS AND FUTUNA', 'EH' => 'WESTERN SAHARA', 'YE' => 'YEMEN', 'ZM' => 'ZAMBIA', 'ZW' => 'ZIMBABWE' );
}

function wpdmpp_tax_active(){
    $settings = get_option('_wpdmpp_settings');
    return isset($settings['tax']) && isset($settings['tax']['enable'])?true:false;
}

function wpdmpp_show_tax(){
    $settings = get_option('_wpdmpp_settings');
    return isset($settings['tax']) && isset($settings['tax']['tax_on_cart'])?true:false;
}


//Send notification before delete product
add_action('wp_trash_post', 'notify_product_rejected');
function notify_product_rejected($post_id)
{
    global $post_type;
    if ($post_type != 'wpdmpro') return;

    $post = get_post($post_id);
    $post_meta = get_post_meta($post_id, "_z_user_review", true);

    if ($post_meta != ""):
        $author = get_userdata($post->post_author);
        $author_email = $author->user_email;
        $email_subject = "Your product has been rejected.";

        ob_start(); ?>
        <html>
        <head>
            <title>New post at <?php bloginfo('name') ?></title>
        </head>
        <body>
        <p>
            Hi <?php echo $author->user_firstname ?>,
        </p>

        <p>
            Your product <?php the_title() ?> has not been approved by team.
        </p>
        </body>
        </html>
        <?php
        $message = ob_get_contents();
        ob_end_clean();

        wp_mail($author_email, $email_subject, $message);
    endif;
}

// Product accept notification email
function notify_product_accepted($post_id)
{
    global $post_type;
    if ($post_type != 'wpdmpro') return;

    if (($_POST['post_status'] == 'publish') && ($_POST['original_post_status'] != 'publish')) {
        $post = get_post($post_id);
        $post_meta = get_post_meta($post_id, "_z_user_review", TRUE);
        if ($post_meta != ""):

            $author = get_userdata($post->post_author);
            $author_email = $author->user_email;
            $email_subject = "Your post has been published.";

            ob_start(); ?>
            <html>
            <head>
                <title>Your Product Status at <?php bloginfo('name') ?></title>
            </head>
            <body>
                <p>Hi <?php echo $author->user_firstname ?>,</p>
                <p>Your product <a href="<?php echo get_permalink($post->ID) ?>"><?php the_title_attribute() ?></a> has been published.</p>
            </body>
            </html>
            <?php
            $message = ob_get_clean();

            wp_mail($author_email, $email_subject, $message);
        endif;
    }
}

//for withdraw request
function wpdmpp_withdraw_request()
{
    global $wpdb, $current_user;

    $uid = $current_user->ID;

    if (isset($_POST['withdraw'], $_POST['withdraw_amount']) && $_POST['withdraw'] == 1 && $_POST['withdraw_amount'] > 0) {

        $wpdb->insert(
            "{$wpdb->prefix}ahm_withdraws",
            array(
                'uid' => $uid,
                'date' => time(),
                'amount' => $_POST['withdraw_amount'],
                'status' => 0
            ),
            array(
                '%d',
                '%d',
                '%f',
                '%d'
            )
        );
        if (wpdm_is_ajax()) {
            _e("Withdraw Request Sent!", "wpdm-premium-packages");
            die();
        }
        header("Location: " . $_SERVER['HTTP_REFERER']);
        die();
    }

}

function wpdmpp_redirect($url)
{
    if (!headers_sent())
        header("location: " . $url);
    else
        echo "<script>location.href='{$url}';</script>";
    die();
}

function wpdmpp_js_redirect($url)
{
    echo "&nbsp;Redirecting...<script>location.href='{$url}';</script>";
    die();
}

function wpdmpp_members_page()
{
    $settings = get_option('_wpdmpp_settings');
    return get_permalink($settings['members_page_id']);
}

function wpdmpp_orders_page($part = '')
{
    global $wpdmpp_settings;
    $settings = $wpdmpp_settings;

    $url = get_permalink($settings['orders_page_id']);
    if ($part != '') {
        if (strpos($url, '?')) $url .= "&" . $part;
        else $url .= "?" . $part;
    }

    $udbpage = get_option('__wpdm_user_dashboard', 0);
    if((int)$udbpage > 0) {

        $udbpage = get_permalink($udbpage);
        $sap = strstr($udbpage, '?')?"&udb_page=":"?udb_page=";
        $url = $udbpage.$sap."purchases/orders/";
        if($part != ''){
            $part = explode("=", $part);
            $url = $udbpage .$sap . "purchases/order/" . end($part) . "/";
        }
    }
    return $url;
}

function wpdmpp_guest_order_page($part = '')
{
    $settings = get_option('_wpdmpp_settings');
    $url = get_permalink($settings['guest_order_page_id']);
    if(!isset($settings['guest_download']) || $settings['guest_download'] == 0) return '';
    if ($part != '') {
        if (strpos($url, '?')) $url .= "&" . $part;
        else $url .= "?" . $part;
    }
    return $url;
}

function wpdmpp_cart_page($part = '', $product_id = null)
{
    global $wpdmpp_settings;
    if(!$wpdmpp_settings)
        $wpdmpp_settings = get_option('_wpdmpp_settings');

    $url = get_permalink($wpdmpp_settings['page_id']);

    if ($part != '') {
        if (strpos($url, '?')) $url .= "&" . $part;
        else $url .= "?" . $part;
    }
    return $url;
}

function wpdmpp_continue_shopping_url($part = '')
{
    $settings = get_option('_wpdmpp_settings');
    return $settings['continue_shopping_url'];
}

function wpdmpp_billing_info_form(){
    global $current_user;
    $billing = maybe_unserialize(get_user_meta($current_user->ID, 'user_billing_shipping', true));
    $store = maybe_unserialize(get_user_meta($current_user->ID, '__wpdm_store', true));
    $billing = isset($billing['billing']) ? $billing['billing'] : array();
    include wpdm_tpl_path('user-dashboard/billing-info.php', WPDMPP_BASE_DIR.'/templates/');
}

function wpdmpp_save_billing_info(){
    global $current_user;
    if(isset($_POST['__wpdm_store_owner']))
     $__wpdm_store_owner = isset($_POST['__wpdm_store_owner'])?1:0;
    update_user_meta($current_user->ID, '__wpdm_store_owner', $__wpdm_store_owner);
    if(isset($_POST['__wpdm_store'])){
        update_user_meta($current_user->ID, '__wpdm_store', $_POST['__wpdm_store']);
    }
    if(isset($_POST['checkout']) && isset($_POST['checkout']['billing'])){
        update_user_meta($current_user->ID, 'user_billing_shipping', serialize($_POST['checkout']));
    }
}

function wpdmpp_get_purchased_items(){
    if(!isset($_GET['wpdmppaction']) || $_GET['wpdmppaction'] != 'getpurchaseditems') return;
    $user = wp_signon(array('user_login' => $_GET['user'], 'user_password' => $_GET['pass']));
    if($user->ID) wp_set_current_user($user->ID);
    if(is_user_logged_in())
        echo json_encode(Order::getPurchasedItems());
    else
        echo json_encode(array('error' => '<a href="https://www.wpdownloadmanager.com/user-dashboard/?redirect_to=[redirect]">You need to login first!</a>'));
    die();
}

/**
 * Retrienve Site Commissions on User's Sales
 * @param null $uid
 * @return mixed
 */
function wpdmpp_site_commission($uid = null)
{
    global $current_user;
    $user = $current_user;
    if ($uid) $user = get_userdata($uid);
    $comission = get_option("wpdmpp_user_comission");
    $comission = $comission[$user->roles[0]];
    return $comission;
}

function wpdmpp_get_user_earning()
{

}

function wpdmpp_product_price($pid)
{
    $base_price = get_post_meta($pid, "__wpdm_base_price", true);
    $sales_price = wpdmpp_sales_price($pid);
    $price = floatval($sales_price) > 0 && $sales_price < $base_price ? $sales_price : $base_price;
    if (floatval($price) == 0) return number_format(0, 2, ".", "");
    return number_format($price, 2, ".", "");
}

function wpdmpp_is_ajax()
{
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) return TRUE;
    return false;
}

//delete product from front-end
function wpdmpp_delete_product()
{
    if (is_user_logged_in() && isset($_GET['dproduct'])) {
        global $current_user;
        $pid = intval($_GET['dproduct']);
        $pro = get_post($pid);

        if ($current_user->ID == $pro->post_author) {
            wp_update_post(array('ID' => $pid, 'post_status' => 'trash'));
            $settings = get_option('_wpdmpp_settings');
            if ($settings['frontend_product_delete_notify'] == 1) {
                wp_mail(get_option('admin_email'), "I had to delete a product", "Hi, Sorry, but I had to delete following product for some reason:<br/>{$pro->post_title}", "From: {$current_user->user_email}\r\nContent-type: text/html\r\n\r\n");
            }
            $_SESSION['dpmsg'] = 'Product Deleted';
            header("location: " . $_SERVER['HTTP_REFERER']);
            die();
        }
    }
}

function wpdmpp_order_completed_mail()
{

}

function wpdmpp_head()
{
    ?>
    <script>
        var wpdmpp_base_url = '<?php echo plugins_url('/wpdm-premium-packages/'); ?>';
        var wpdmpp_currency_sign = '<?php echo wpdmpp_currency_sign(); ?>';
        var wpdmpp_csign_before = '<?php echo wpdmpp_currency_sign_position() == 'before' ? wpdmpp_currency_sign() : ''; ?>';
        var wpdmpp_csign_after = '<?php echo wpdmpp_currency_sign_position() == 'after' ? wpdmpp_currency_sign() : ''; ?>';
        var wpdmpp_currency_code = '<?php echo wpdmpp_currency_code(); ?>';
        var wpdmpp_cart_url = '<?php echo wpdmpp_cart_page("savedcart="); ?>';
    </script>
    <?php
}

add_action("wp_ajax_wpdmpp_delete_frontend_order", "wpdmpp_delete_frontend_order");
add_action("wp_ajax_nopriv_wpdmpp_delete_frontend_order", "wpdmpp_delete_frontend_order");

function wpdmpp_delete_frontend_order()
{
    if (!wp_verify_nonce($_REQUEST['nonce'], "delete_order")) {
        exit("No naughty business please");
    }

    $result['type'] = 'failed';
    global $wpdb;
    $order_id = esc_attr($_REQUEST['order_id']);

    $ret = $wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}ahm_orders WHERE order_id = %s", $order_id));

    if ($ret) {
        $ret = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}ahm_order_items WHERE oid = %s", $order_id));

        if ($ret) $result['type'] = 'success';
    }

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = json_encode($result);
        echo $result;
    } else {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }

    die();
}

add_action("wp_ajax_nopriv_update_guest_billing", "wpdmpp_update_guest_billing");
function wpdmpp_update_guest_billing(){
    $billinginfo = array
    (
        'first_name' => '',
        'last_name' => '',
        'company' => '',
        'address_1' => '',
        'address_2' => '',
        'city' => '',
        'postcode' => '',
        'country' => '',
        'state' => '',
        'order_email' => '',
        'email' => '',
        'phone' => '',
        'taxid' => ''
    );
    $sbillinginfo  = $_POST['billing'];
    $billinginfo = shortcode_atts($billinginfo, $sbillinginfo);
    Order::Update(array('billing_info'=> serialize($billinginfo)), $_SESSION['guest_order'] );
    die('Saved!');
}

function wpdmpp_recalculate_sales()
{
    if (!isset($_POST['id'])) return;
    global $wpdb;
    $id = (int)$_POST['id'];
    $sql = "select sum(quantity*price) as sales_amount, sum(quantity) as sales_quantity from {$wpdb->prefix}ahm_order_items oi, {$wpdb->prefix}ahm_orders o where oi.oid = o.order_id and oi.pid = {$id} and o.order_status IN ('Completed', 'Expired')";
    $data = $wpdb->get_row($sql);

    header('Content-type: application/json');
    update_post_meta($id, '__wpdm_sales_amount', $data->sales_amount);
    update_post_meta($id, '__wpdm_sales_count', $data->sales_quantity);
    $data->sales_amount = wpdmpp_currency_sign() . floatval($data->sales_amount);
    $data->sales_quantity = intval($data->sales_quantity);
    echo json_encode($data);
    die();
}

function wpdmpp_sales_price($pid){
    $sales_price = get_post_meta($pid, "__wpdm_sales_price" , true);
    $sales_price_expire = get_post_meta($pid, "__wpdm_sales_price_expire", true);
    if($sales_price_expire != '') {
        $sales_price_expire = strtotime($sales_price_expire);
        if (time() > $sales_price_expire && $sales_price_expire > 0) $sales_price = 0;
    }
    return number_format((double)$sales_price, 2, ".", "");
}

function wpdmpp_sales_price_info($product_id){
    $sales_price_expire = get_post_meta($product_id, '__wpdm_sales_price_expire', true);
    if($sales_price_expire != '')
        $sales_price_expire = strtotime($sales_price_expire);
    $sales_price_info = $sales_price_expire != ''?sprintf(__("Sales price will expire on %s", "wpdm-premium-packages"), date(get_option("date_format")." H:i", $sales_price_expire)):__("This is a discounted price for a limited time", "wpdm-premium-packages");
    $sales_price_info = apply_filters("wpdmpp_sales_price_info", $sales_price_info, $product_id, $sales_price_expire);
    return $sales_price_info;

}

/**
 * @param $pid
 * @return string
 */
function wpdmpp_effective_price($pid)
{
    global  $current_user;
    if(get_post_type($pid) !='wpdmpro') return 0;
    $base_price = get_post_meta($pid, "__wpdm_base_price", true);
    $sales_price = wpdmpp_sales_price($pid);
    $price = intval($sales_price) > 0 ? $sales_price : $base_price;

    $role = is_user_logged_in()?$current_user->roles[0]:'guest';
    $discount = maybe_unserialize(get_post_meta($pid,'__wpdm_discount', true));
    if(!is_array($discount) || count($discount) == 0) return number_format((float)$price, 2, ".", "");

    $discount[$role] = isset($discount[$role])?$discount[$role]:0;
    $discount[$role] = floatval($discount[$role]);
    $user_discount = (($price*$discount[$role])/100);
    $price -= $user_discount;

    if(!$price) $price = 0;
    return number_format($price, 2, ".", "");
}

/**
 * @param $pid
 * @return int|mixed
 */
function wpdmpp_role_discount($pid){
    global $current_user;
    $role = is_user_logged_in()?$current_user->roles[0]:'guest';
    $discount = maybe_unserialize(get_post_meta($pid,'__wpdm_discount', true));
    if(!is_array($discount) || count($discount) == 0) return 0;
    $role_discount = isset($discount[$role])?$discount[$role]:0;
    return $role_discount;
}

function wpdmpp_price_range($pid){
    $pre_licenses = wpdmpp_get_licenses();
    $license_infs = get_post_meta($pid, "__wpdm_license", true);
    $license_infs = maybe_unserialize($license_infs);
    $licprices = array();

    $base_price = get_post_meta($pid, "__wpdm_base_price", true);
    $sales_price = wpdmpp_sales_price($pid);
    $base_price = intval($sales_price) > 0 ? $sales_price : $base_price;

    foreach ($pre_licenses as $licid => $lic){
        if(isset($license_infs[$licid]) && $license_infs[$licid]['active'] == 1){
            $licprices[] = isset($license_infs[$licid]['price']) ?$license_infs[$licid]['price']:$base_price;
        }
    }

    if(wpdmpp_currency_sign_position() == 'before')
        $price_range = wpdmpp_currency_sign().number_format((float)$base_price,2);
    else
        $price_range = number_format((float)$base_price,2).wpdmpp_currency_sign();

    if(count($licprices) > 1 && get_post_meta($pid, "__wpdm_enable_license", true)==1){
        sort($licprices);
        $fromprice = $licprices[0];
        $sales_price = wpdmpp_sales_price($pid);
        if($sales_price < $fromprice && $sales_price > 0) $fromprice = $sales_price;

        if(wpdmpp_currency_sign_position() == 'before')
            $price_range = wpdmpp_currency_sign().number_format($fromprice,2)." &mdash; ".wpdmpp_currency_sign().number_format(end($licprices), 2);
        else
            $price_range = number_format($fromprice,2).wpdmpp_currency_sign()." &mdash; ".number_format(end($licprices), 2).wpdmpp_currency_sign();
    }
    return $price_range;
}

function wpdmpp_order_id(){
    return isset($_SESSION['orderid'])?$_SESSION['orderid']:false;
}

function wpdmpp_currency_sign()
{
    $settings = get_option('_wpdmpp_settings');
    $currency = isset($settings['currency']) ? $settings['currency'] : 'USD';
    $cdata = Currencies::GetCurrency($currency);
    $sign = is_array($cdata) ? $cdata['symbol'] : '$';
    return $sign;
}

function wpdmpp_currency_sign_position()
{
    $settings = get_option('_wpdmpp_settings');
    $currency_position = isset($settings['currency_position']) ? $settings['currency_position'] : 'before';
    return $currency_position;
}

function wpdmpp_currency_code()
{
    $settings = get_option('_wpdmpp_settings');
    $currency = isset($settings['currency']) ? $settings['currency'] : 'USD';
    return $currency;
}

/**
 * Validating download request using 'wpdm_onstart_download' WPDM hook
 * @param $package
 * @return mixed
 */
function wpdmpp_validate_download($package)
{
    global $current_user, $wpdb;

    
    $order = new Order();

    $price = wpdmpp_effective_price($package['ID']);
    if (floatval($price) == 0) return $package;
    $oid = isset($_GET['oid']) ? $_GET['oid'] : "";
    $ord = $order->getOrder($oid);
    if(isset($_GET['customerkey'])){
        //$customerkey =
    }
    if (($oid == "" || !is_object($ord)) && $price > 0) wp_die('You do not have permission to download this file');

    $settings = get_option('_wpdmpp_settings');
    $order = new Order();
    $odata = $order->GetOrder($_GET['oid']);
    $items = unserialize($odata->items);

    if (@in_array($_GET['wpdmdl'], $items)
        && isset($_GET['oid'])
        && $_GET['oid'] != ''
        && !is_user_logged_in()
        && $odata->uid == 0
        && $odata->order_status == 'Completed'
        && isset($settings['guest_download'])
        && isset($_SESSION['guest_order'])) {
        //for guest download
        return $package;
    }

    if ((is_user_logged_in() && $current_user->ID != $ord->uid && $price > 0) || (!is_user_logged_in() && $price > 0)) wp_die('You do not have permission to download this file');
    return $package;
}

/**
 * Assign an order to specific user
 */
function wpdmpp_assign_user_2order()
{
    if (isset($_REQUEST['assignuser']) && isset($_REQUEST['order'])) {
        $u = get_user_by('login', $_REQUEST['assignuser']);
        $order = new Order();
        $order->Update(array('uid' => $u->ID), $_REQUEST['order']);
        die('Done!');
    }
}

function wpdmpp_download_order_note_attachment()
{
    global $current_user;
    if (!isset($_GET['_atcdl']) || !is_user_logged_in()) return false;
    $key = WPDM_Crypt::Decrypt($_GET['_atcdl']);
    $key = explode("|||", $key);
    $order = new Order($key[0]);
    if ($order->uid != $current_user->ID && !current_user_can('manage_options')) wp_die('Unauthorized Access');
    $files = $order->order_notes['messages'][$key[1]]['file'];
    $filename = preg_replace("/^[0-9]+?wpdm_/", "", wpdm_basename($key[2]));
    if (in_array($key[2], $files)) {
        wpdm_download_file(UPLOAD_DIR . $key[2], $filename);
        die();
    }
}

/**
 * Return array of country objects
 * @return array
 */
function wpdmpp_get_countries(){
    global $wpdb;
    $countries = $wpdb->get_results("select * from {$wpdb->prefix}ahm_country order by country_name");

    return $countries;
}

/**
 * Return Premium Package Template Directory
 * @return string
 */
function wpdmpp_tpl_dir(){
    return WPDMPP_BASE_DIR."/templates/";
}

function wpdmpp_email_templates($templates){
    $templates['purchase-confirmation-guest'] = array(
                'label' =>  __('Purchase Confirmation - Guest', 'wpdmpro'),
                'for' => 'customer',
                'default' => array( 'subject' => __('Thanks For Your Purchase', 'wpdmpro'),
                                    'from_name' => get_option('blogname'),
                                    'from_email' => get_option('admin_email'),                                    
                                    'message' => 'Hello ,<br/>Thanks for your order at [#sitename#].<br/>Your Order ID: [#orderid#]<br/>You need to create an account to access your order and to get future updates.<br/>Please click on the following link to create your account:<br/>[#order_url#]<br/>If you already have account simply click the above url and login<br/><br/>Best Regards,<br/>Sales Team<br/><b>[#sitename#]</b>'
                )
            );
    
    $templates['purchase-confirmation'] = array(
                'label' =>  __('Purchase Confirmation', 'wpdmpro'),
                'for' => 'customer',
                'default' => array( 'subject' => __('Thanks For Your Purchase', 'wpdmpro'),
                                    'from_name' => get_option('blogname'),
                                    'from_email' => get_option('admin_email'),
                                    'message' => 'Hello ,<br/>Thanks for your order at [#sitename#].<br/>Your Order ID: [#orderid#]<br/>. You can download your purchased item(s) from the following URL:<br/>[#order_url#]<br/><br/>Best Regards,<br/>Sales Team<br/><b>[#sitename#]</b>'
                )
            );

    $templates['sale-notification'] = array(
                'label' =>  __('New Sale Notification', 'wpdmpro'),
                'for' => 'Seller & Admin',
                'default' => array( 'subject' => __('Congratulation! You have a sale.', 'wpdmpro'),
                                    'from_name' => get_option('blogname'),
                                    'from_email' => get_option('admin_email'),  
                                    'to_email' => get_option('admin_email'),
                                    'message' => 'Hello ,<br/>Congratulations! You have a sale just now.<br/>Order ID: [#orderid#]<br/>Sold Items:<br/>[#items#]<br/>Review Order: [#order_url_admin#]'
                )
            );
    
    $templates['os-notification'] = array(
                'label' =>  __('Order Status Notification', 'wpdmpro'),
                'for' => 'customer',
                'default' => array( 'subject' => __('Order ([#orderid#]) Status Changed', 'wpdmpro'),
                                    'from_name' => get_option('blogname'),
                                    'from_email' => get_option('admin_email'),                                     
                                    'message' => 'Hello ,<br/>The order [#orderid#] is changed to [#order_status#]<br/>Review Order: [#order_url#]<br/><br/>Best Regards,<br/>Sales Team<br/><b>[#sitename#]</b>'
                )
            );
     
    return $templates;
}

function wpdmpp_reactivate(){
    return __("Database error detected. Please try deactivate and then reactivating plugin.", "wpdm-premium-packages");
}