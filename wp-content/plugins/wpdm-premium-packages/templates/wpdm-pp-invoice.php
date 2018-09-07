<?php
/**
 * Invoice Template
 *
 * This template can be overridden by copying it to yourtheme/download-manager/wpdm-pp-invoice.php.
 *
 * @version     1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if ( ! is_user_logged_in() && ! isset( $_SESSION['guest_order'] ) ) {

    $orderid    = isset( $_GET['id'] ) ? $_GET['id'] : '';
    $orderurl   = wpdm_user_dashboard_url().'?udb_page=purchases/order/' . $orderid;

    ?> <div style="text-align: center;">Please <a href="<?php echo wp_login_url( $orderurl ); ?>"><b>Login or Register</b></a> to access this page.</div> <?php

    die();
} else {

    global $wpdb, $current_user;
    $settings           = get_option('_wpdmpp_settings');
    $csign              = wpdmpp_currency_sign();
    $_ohtml             = "";
    $order              = new Order();
    $oid                = is_user_logged_in() ? $_GET['id'] : $_SESSION['guest_order'];
    $order              = $order->GetOrder($_GET['id']);
    $order->currency    = maybe_unserialize($order->currency);
    $csign    = $order->currency['sign'];
    $csign_before       = wpdmpp_currency_sign_position() == 'before' ? $csign : '';
    $csign_after        = wpdmpp_currency_sign_position() == 'after' ? $csign : '';

    //echo '<pre>';print_r($order);echo '</pre>';die();

    $billing_defaults =  array
    (
        'first_name'    => '',
        'last_name'     => '',
        'company'       => '',
        'address_1'     => '',
        'address_2'     => '',
        'city'          => '',
        'postcode'      => '',
        'country'       => '',
        'state'         => '',
        'order_email'   => '',
        'phone'         => '',
        'taxid'         => ''
    );

    if ( isset( $settings['billing_address'] ) && $settings['billing_address'] == 1 || isset( $order->billing_info ) ){

        // Asked billing address in checkout, Here we use order specific billing info
        // Or guest order invoice. Billing info is linked to the order

        $billing_info_from_order    = unserialize($order->billing_info);
        $billing_info               = shortcode_atts($billing_defaults, $billing_info_from_order);
    }
    else{

        // Skiped billing address in checkout, get billing address from saved user info

        $saved_billing_info = maybe_unserialize(get_user_meta($current_user->ID, 'user_billing_shipping', true));
        $billing_info       = isset($saved_billing_info['billing']) ? $saved_billing_info['billing'] : $billing_defaults;

        // Due to index mismatch in order email and saved billing email
        $billing_info['order_email'] = $billing_info['email'];
    }

    $coup               = __("Coupon Discount","wpdm-premium-packages");
    $role_dis           = __("Role Discount","wpdm-premium-packages");
    $item_name_label    = __('Item Name', 'wpdm-premium-packages');
    $quantity_label     = __('Quantity', 'wpdm-premium-packages');
    $unit_price_label   = __('Unit Price', 'wpdm-premium-packages');
    $net_subtotal_label = __('Subtotal', 'wpdm-premium-packages');
    $discount_label     = __('Discount', 'wpdm-premium-packages');
    $nettotal_label     = __('Total', 'wpdm-premium-packages');
    $total_label        = __('Total', 'wpdm-premium-packages');
    $vat_label          = __('Tax', 'wpdm-premium-packages');

    $ordertotal         = number_format($order->total, 2);
    $unit_prices        = unserialize($order->unit_prices);
    $cart_discount      = number_format($order->discount, 2);
    $tax                = number_format($order->tax, 2);

    $item_table         = <<<OTH
<table class="table table-striped table-bordered" id="invoice-amount" width="100%" cellspacing="0">
<thead>
<tr id="header_row">
    <th>{$item_name_label}</th>
    <th>{$quantity_label}</th>
    <th class='item_r' style="text-align: right;">{$unit_price_label}</th>
    <th class='item_r' style="text-align: right;">{$coup}</th>
    <th class='item_r' style="text-align: right;">{$role_dis}</th>
    <th class='item_r' style="text-align: right;">{$net_subtotal_label}</th>
</tr>
</thead>
<tfoot> 
      <tr id="discount_tr">          
        <td colspan="5" class="item_r" style="text-align:right">{$discount_label}</td> 
        <td class="item_r text-right">{$csign_before}{$cart_discount}{$csign_after}</td>
      </tr> 
      <tr id="net_total_tr"> 
       
        <td  colspan="5" class="item_r" style="text-align:right" class="item_r">{$nettotal_label}</td> 
        <td class="item_r text-right">{$csign_before}{$ordertotal}{$csign_after}</td>
      </tr> 
      <tr id="vat_tr"> 
        
        <td  colspan="5" class="item_r" style="text-align:right" class="item_r">{$vat_label}</td> 
        <td class="item_r text-right">{$csign_before}{$tax}{$csign_after}</td>
      </tr> 
      <tr id="total_tr"> 
         
        <td  colspan="5" class="item_r" style="text-align:right" class="total" id="total_currency">{$total_label}</td> 
        <td class="total text-right">{$csign_before}{$ordertotal}{$csign_after}</td>
      </tr> 
    </tfoot>
    <tbody>
OTH;
    $items = Order::GetOrderItems($order->order_id);
    $total = 0;
    foreach ($items as $item) {

        $ditem = get_post($item['pid']);
        if (!is_object($ditem)) {
            $ditem = new stdClass();
            $ditem->ID = 0;
            $ditem->post_title = "[Item Deleted]";
        }
        $meta = get_post_meta($ditem->ID, 'wpdmpp_list_opts', true);
        $price = $item['price'] * $item['quantity'];

        $discount_r = $item['role_discount'];
        //$discount = $price*($discount_r/100);
        //$aprice = $price - $discount;


        $prices = 0;
        $variations = "";
        $discount = $discount_r;

        $_variations = unserialize($item['variations']);
        foreach ($_variations as $vr) {
            $variations .= "{$vr['name']}: +{$csign_before}" . number_format(floatval($vr['price']), 2) . $csign_after;
            $prices += number_format(floatval($vr['price']), 2);
        }

        $itotal = number_format(((($item['price'] + $prices) * $item['quantity']) - $discount - $item['coupon_discount']), 2, ".", "");
        $total += $itotal;
        $download_link = home_url("/?wpdmdl={$item['pid']}&oid={$order->order_id}");
        $licenseurl = home_url("/?task=getlicensekey&file={$item['pid']}&oid={$order->order_id}");
        $order_item = "";
        if ($order->order_status == 'Completed') {
            if (get_post_meta($item['pid'], '__wpdm_enable_license', true) == 1) {

                $licenseg = <<<LIC
<a id="lic_{$item['pid']}_{$order->order_id}_btn" onclick="return getkey('{$item['pid']}','{$order->order_id}');" class="btn btn-primary btn-xs" data-placement="top" data-toggle="popover" href="#"><i class="fa fa-key white"></i></a>
LIC;
            } else $licenseg = "&mdash;";

            $indf = "";
            $files = maybe_unserialize(get_post_meta($ditem->ID, '__wpdm_files', true));

            if (count($files) > 1 && $order->order_status == 'Completed') {
                $index = 0;

                foreach ($files as $ind => $ff) {
                    $data = get_post_meta($ditem->ID, '__wpdm_fileinfo', true);
                    $title = $data[$ind]['title'] ? $data[$ind]['title'] : basename($ff);
                    $index = WPDM_Crypt::Encrypt($ff);
                    $ff = "<li class='list-group-item' style='padding:10px 15px;'>" . $title . " <a class='pull-right' href=\"{$download_link}&ind={$index}\"><i class='fa fa-download'></i></a></li>";
                    $indf .= "$ff";
                }
            }
            $discount = number_format(floatval($discount), 2);
            $item['price'] = number_format($item['price'], 2);
            $_ohtml .= <<<ITEM
                    <tr class="item">
                        <td>{$ditem->post_title} <br> {$variations}</td>
                        <td>{$item['quantity']}</td>
                        <td class="text-right">{$csign_before}{$item['price']}{$csign_after}</td>
                        <td class="text-right">{$csign_before}{$item['coupon_discount']}{$csign_after}</td>
                        <td class="text-right">{$csign_before}{$discount}{$csign_after}</td>                         
                        <td class='text-right' align='right'>{$csign_before}{$itotal}{$csign_after}</td>
ITEM;
        } else {
            $discount = number_format(floatval($discount), 2);
            $item['price'] = number_format($item['price'], 2);
            $_ohtml .= <<<ITEM
                    <tr class="item">
                        <td>{$ditem->post_title} <br> {$variations}</td>
                        <td>{$item['quantity']}</td>
                        <td class="text-right">{$csign_before}{$item['price']}{$csign_after}</td>
                        <td class="text-right">{$csign_before}{$item['coupon_discount']}{$csign_after}</td>
                        <td class="text-right">{$csign_before}{$discount}{$csign_after}</td>                        
                        <td class='text-right' align='right'>{$csign_before}{$itotal}{$csign_after}</td>
ITEM;
        }

        $order_item = apply_filters("wpdmpp_order_item", "", $item);
        if ($order_item != '') $_ohtml .= "<tr><td colspan='7'>" . $order_item . "</td></tr>";
    }

    $item_table .= $_ohtml."</tbody></table>";

    $invoice['client_info'] = <<<CINF
    <div class="vcard" id="client-details"> 
        <div class="fn">{$billing_info['first_name']} {$billing_info['last_name']}</div>
        <div class="org"><h3>{$billing_info['company']}</h3></div>
        <div class="adr">
            <div class="street-address">
            {$billing_info['address_1']}
            {$billing_info['address_2']}
            </div>
            <!-- street-address -->
            <div class="locality">{$billing_info['postcode']}, {$billing_info['city']}, {$billing_info['state']}, {$billing_info['country']}</div>
            <div id="client-email"><span class="order-email">Email: {$billing_info['order_email']}</span></div>
        </div>
        <!-- adr -->
        <div id="phone">Phone: {$billing_info['phone']}</div>
        <div id="taxid">Tax ID: {$billing_info['taxid']}</div>
    </div>
CINF;
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href='https://fonts.googleapis.com/css?family=Varela|Montserrat:700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/css/front.css" />
    <style>
        .w3eden{
            font-family: Varela, serif;
            font-size: 9pt;
        }
        .text-right{
            text-align: right;
        }
        .w3eden th{
            border-bottom: 0 !important;
        }
        .w3eden th,
        .w3eden td{
            font-size: 9pt;
        }
        .w3eden .alert.alert-success:before{
            padding-top: 14px;
        }
        .w3eden h3{
            font-family: Montserrat, serif;
            margin: 0;
            font-size: 11pt;
        }
        .w3eden em{
            color: #888;
            margin-bottom: 8px;
        }
        .w3eden .panel{
            border-radius: 0;
        }
        .w3eden .panel.info-panel .panel-body{
            height: 145px;
        }
        .w3eden .panel .panel-heading{
            border-radius: 0;
        }
        .w3eden .panel-default .panel-heading{
            background: #fafafa;
            border-radius: 0;
        }
        .w3eden h3.invoice-no{
            font-family: Courier, monospace;
            font-size: 14pt;
            font-weight: bold;
            color: #349ADE;
        }
        .w3eden .frow .panel-body{
            height: 50px;
        }
        .w3eden .frow #btn-print{
            margin-top: -5px;
            margin-right: -8px;
        }
        @media print {
            #btn-print {
                display: none;
            }
        }
    </style>
</head>
<body class="w3eden" onload="window.print();">
<div class="container-fluid">
 <br/>
    <div class="row frow">
        <div class="col-xs-<?php echo isset($_GET['renew'])?4:6; ?>">
            <div class="panel panel-default"><div class="panel-heading">
                    <button class="btn btn-primary btn-xs pull-right" id="btn-print" type="button" onclick="window.print();"><i class="fa fa-print"></i> <?php _e('Print Invoice','wpdm-premium-packages'); ?></button><strong><?php _e('Invoice No','wpdm-premium-packages'); ?></strong>
                </div>
                <div class="panel-body">
                    <h3 class="text-info invoice-no"><?php echo $order->order_id; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-xs-<?php echo isset($_GET['renew'])?4:6; ?> text-right">
            <div class="panel panel-default"><div class="panel-heading">
                    <strong><?php _e('Order Date','wpdm-premium-packages'); ?></strong>
                </div>
                <div class="panel-body">
                    <?php echo date(get_option('date_format'),$order->date); ?>
                </div>
            </div>
        </div>
        <?php if(isset($_GET['renew'])){ ?>
        <div class="col-xs-4 text-right">
            <div class="panel panel-default"><div class="panel-heading">
                    <strong><?php _e('Order Renewed On','wpdm-premium-packages'); ?></strong>
                </div>
                <div class="panel-body">
                    <?php echo date(get_option('date_format'),(int)$_GET['renew']); ?>
                </div>
            </div>
        </div>
    <?php } ?>

    </div>

    <div class="row">
        <div class="col-xs-6">
            <div class="panel panel-default info-panel">
                <div class="panel-heading"><strong><?php _e('From:','wpdm-premium-packages'); ?></strong></div>
                <div class="panel-body">

                    <div class="media">
                        <div class="media-left">
                            <?php if($settings['invoice_logo'] != ""){ ?>
                                <img style="width: auto; height: 50px;" class="media-object" src="<?php echo $settings['invoice_logo']; ?>">
                            <?php } ?>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading"><?php bloginfo('sitename'); ?></h4>
                            <p><?php echo nl2br($settings['invoice_company_address']); ?></p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="panel panel-default info-panel">
                <div class="panel-heading"><strong><?php _e('To:','wpdm-premium-packages'); ?></strong></div>
                <div class="panel-body">
                    <?php echo $invoice['client_info']; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php echo $item_table; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <div class="panel panel-default"><div class="panel-heading">
                    <strong><?php _e('Payment Method','wpdm-premium-packages'); ?></strong>
                </div>
                <div class="panel-body">
                    <?php echo $order->payment_method; ?>
                </div>
            </div>
        </div>
        <div class="col-xs-6 text-right">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><?php _e('Payment Status','wpdm-premium-packages'); ?></strong>
                </div>
                <div class="panel-body">
                    <?php echo $order->payment_status; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php die();