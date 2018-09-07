<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;
$order->items = unserialize($order->items);
$oitems = $wpdb->get_results("select * from {$wpdb->prefix}ahm_order_items where oid='{$order->order_id}'");
    $role = '';
    $currency = maybe_unserialize($order->currency);
    $currency_sign = is_array($currency) && isset($currency['sign'])?$currency['sign']:'$';
    if($order->uid > 0){
        $user = new WP_User( $order->uid );
        $role = $user->roles[0];
    }
    $tax = $order1->wpdmpp_calculate_tax($order->order_id);
    $settings = maybe_unserialize(get_option('_wpdmpp_settings'));
    $total_coupon = get_all_coupon(unserialize($order->cart_data));

    $sbilling =  array
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
        'email' => '',
        'order_email' => '',
        'phone' => ''
    );
    $billing = unserialize($order->billing_info);
    $billing = shortcode_atts($sbilling, $billing);

?>
<?php ob_start(); ?>

<table width="100%" cellspacing="0" class="table">
    <thead>
    <tr><th align="left"><?php _e("Item Name","wpdm-premium-packages");?></th>
        <th align="left"><?php _e("Unit Price","wpdm-premium-packages");?></th>
        <th align="left"><?php _e("Quantity","wpdm-premium-packages");?></th>
        <th align="left"><?php _e("Discount","wpdm-premium-packages");?></th>
        <th align="left"><?php _e("Coupon Code","wpdm-premium-packages");?></th>
        <th align="left"><?php _e("Coupon Discount","wpdm-premium-packages");?></th>
        <th align="left"><?php _e("Total","wpdm-premium-packages");?></th>
        <th align="left"><?php _e("Subtotal","wpdm-premium-packages");?></th>
    </tr>
    </thead>
    <?php
    $cart_data = unserialize($order->cart_data);

    if(is_array($cart_data) && !empty($cart_data)):
        $coupon_discount = 0;
        $role_discount = 0;
        $shipping = 0;
        $order_total = 0;
        foreach ($cart_data as $pid => $item):
            if(isset($item['item'])):
                foreach ($item['item'] as $id => $var):
                    if(!isset($var['coupon_amount']) || $var['coupon_amount'] == "") {
                        $var['coupon_amount'] = 0;
                    }

                    if(!isset($var['discount_amount']) || $var['discount_amount'] == "") {
                        $var['discount_amount'] = 0;
                    }
                    if(!isset($var['prices']) || $var['prices']==""){
                        $var['prices'] = 0;
                    }

                    if(!isset($item['post_title'])) $item['post_title'] = '---';

                    $coupon_discount += $var['coupon_amount'];
                    $role_discount += $var['discount_amount'];
                    $order_total += (($item['price'] + $var['prices']) * $var['quantity']) - $var['coupon_amount'] - $var['discount_amount'];
                    $vari = isset($var['variations']) && !empty($var['variations']) ? implode(', ', $var['variations']) : ''
                    ?>
                    <tr>
                        <td><?php echo $item['post_title'] . '<br>' . $vari; ?></td>
                        <td><?php echo $currency_sign . $item['price']; ?></td>
                        <td><?php echo $var['quantity']; ?></td>
                        <td><?php echo $currency_sign . $var['discount_amount']; ?></td>
                        <td><?php echo isset($item['coupon'])?$item['coupon']:'&mdash;'; ?></td>
                        <td><?php echo $currency_sign . $var['coupon_amount']; ?></td>
                        <td><?php echo $currency_sign ; echo ($item['price'] + $var['prices']) * $var['quantity']; ?></td>
                        <td><?php echo $currency_sign ; echo (($item['price'] + $var['prices']) * $var['quantity']) - $var['discount_amount'] - $var['coupon_amount']; ?></td>
                    </tr>
                <?php
                endforeach;
            else:
                if(!isset($item['coupon_amount']) || $item['coupon_amount'] == "") {
                    $item['coupon_amount'] = 0;
                }

                if(!isset($item['discount_amount']) || $item['discount_amount'] == "") {
                    $item['discount_amount'] = 0;
                }

                if(!isset($item['prices']) || $item['prices'] == "") {
                    $item['prices'] = 0;
                }

                $coupon_discount += $item['coupon_amount'];
                $role_discount += $item['discount_amount'];
                $order_total += (($item['price'] + $item['prices']) * (int)$item['quantity']) - $item['coupon_amount'] - $item['discount_amount'];
                $vari = isset($item['variations']) && !empty($item['variations']) ? implode(', ', $item['variations']) : '';
                if(!isset($item['post_title'])) $item['post_title'] = '---';
                $item['price'] = (double)$item['price'];
                //echo "<pre>";print_r($item['quantity']);
                ?>
                <tr>
                    <td><?php echo $item['post_title'] . '<br>' . $vari; ?></td>
                    <td><?php echo $currency_sign . $item['price']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo $currency_sign . $item['discount_amount']; ?></td>
                    <td><?php echo isset($item['coupon'])?$item['coupon']:''; ?></td>
                    <td><?php echo $currency_sign . $item['coupon_amount']; ?></td>
                    <td><?php echo $currency_sign; echo ($item['price'] + $item['prices']) * $item['quantity']; ?></td>
                    <td><?php echo $currency_sign; echo (($item['price'] + $item['prices']) * $item['quantity']) - $item['discount_amount'] - $item['coupon_amount']; ?></td>
                </tr>
            <?php
            endif;
        endforeach;
    endif;
    ?>
    <tr>
        <td colspan="7" class="text-right"><?php _e('Cart Discount', 'wpdm-premium-packages'); ?></td>
        <td><?php echo $currency_sign.number_format($order->cart_discount, 2); ?></td>
    </tr><tr>
        <td colspan="7" class="text-right"><?php _e('Tax', 'wpdm-premium-packages'); ?></td>
        <td><?php echo $currency_sign.number_format($order->tax, 2); ?></td>
    </tr>
</table>
<?php $content = ob_get_clean(); ?>


    <div class="w3eden admin-orders">
        <div class="panel panel-default" id="wpdm-wrapper-panel">
            <div class="panel-heading">
                <b><i class="fa fa-bars color-purple"></i> &nbsp; <?php _e("View Order","wpdm-premium-packages");?></b>
            </div>
            <div class="panel-body">
                <br/><br/><br/>

        <div class="well" style="background-image: none">
        <b><?php _e("Order Status:", "wpdm-premium-packages"); ?>
            <select id="osv" name="order_status" style="width: 150px;display: inline" >
                <option <?php if ($order->order_status == 'Pending') echo 'selected="selected"'; ?> value="Pending">Pending</option>
                <option <?php if ($order->order_status == 'Processing') echo 'selected="selected"'; ?> value="Processing">Processing</option>
                <option <?php if ($order->order_status == 'Completed') echo 'selected="selected"'; ?> value="Completed">Completed</option>
                <option <?php if ($order->order_status == 'Expired') echo 'selected="selected"'; ?> value="Expired">Expired</option>
                <option <?php if ($order->order_status == 'Cancelled') echo 'selected="selected"'; ?> value="Cancelled">Cancelled</option>
                <option value="Renew" class="text-success text-renew">Renew Order</option>
            </select>
        </b>   <input type="button" id="update_os" class="btn btn-default" value="Update">
        &nbsp;
        <b><?php _e("Payment Status:", "wpdm-premium-packages"); ?>
            <select id="psv" name="payment_status"  style="width: 150px;display: inline" >
                <option <?php if ($order->payment_status == 'Pending') echo 'selected="selected"'; ?> value="Pending">Pending</option>
                <option <?php if ($order->payment_status == 'Processing') echo 'selected="selected"'; ?> value="Processing">Processing</option>
                <option <?php if ($order->payment_status == 'Completed') echo 'selected="selected"'; ?> value="Completed">Completed</option>
                <option <?php if ($order->payment_status == 'Bonus') echo 'selected="selected"'; ?> value="Bonus">Bonus</option>
                <option <?php if ($order->payment_status == 'Gifted') echo 'selected="selected"'; ?> value="Gifted">Gifted</option>
                <option <?php if ($order->payment_status == 'Cancelled') echo 'selected="selected"'; ?> value="Cancelled">Cancelled</option>
                <option <?php if ($order->payment_status == 'Disputed') echo 'selected="selected"'; ?> value="Disputed">Disputed</option>
                <option <?php if ($order->payment_status == 'Refunded') echo 'selected="selected"'; ?> value="Refunded">Refunded</option>
            </select>
        </b><input id="update_ps" type="button" class="btn btn-default" value="Update">
        </div>
        <div id="msg" style="border-radius: 3px;display: none;" class="alert alert-success"><?php _e("Message", "wpdm-premium-packages"); ?></div>
        <div class="row">
            <div class=" col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e("Order ID", "wpdm-premium-packages"); ?></div>
                <div class="panel-body">
                    <span class="lead"><?php echo $order->order_id; ?></span>
                </div>
            </div>
            </div>
            <div class=" col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading"><?php _e("Order Date", "wpdm-premium-packages"); ?></div>
                    <div class="panel-body">
                        <span class="lead"><?php echo date("M d, Y h:i a", $order->date); ?></span>
                    </div>
                </div>
            </div>
            <div class=" col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading"><?php _e("Order Total", "wpdm-premium-packages"); ?></div>
                    <div class="panel-body">
                        <span class="lead"><?php echo $currency_sign . number_format($order->total, 2); ?></span> via <?php echo $order->payment_method; ?>
                    </div>
                </div>
            </div>


    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading"><?php _e("Order Summary", "wpdm-premium-packages"); ?></div>
        <table class="table">
            <tr><td><?php _e("Coupon Discount:", "wpdm-premium-packages"); ?></td><td><?php echo $currency_sign . $coupon_discount; ?></td></tr>
            <tr><td><?php _e("Role Discount:", "wpdm-premium-packages"); ?></td><td><?php echo $currency_sign . $role_discount; ?></td></tr>
            <?php
            if (count($tax) > 0) {
                foreach ($tax as $taxrow) {
                    ?>
                    <tr><td><?php echo $taxrow['label']; ?></td><td><?php echo $currency_sign . $taxrow['rates']; ?></td></tr>
                <?php
                }
            }

            $ret = '';
            $ret = apply_filters('wpdmpp_admin_order_details',$ret,$order->order_id);
            if($ret != '') echo $ret;
            ?>


        </table>
            </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading"><?php _e("Customer Info", "wpdm-premium-packages"); ?></div>
    <?php if($order->uid>0){ ?>
        <table class="table">
            <tbody>
            <tr><td><?php _e("Customer Name:", "wpdm-premium-packages"); ?></td><td><a href='user-edit.php?user_id=<?php echo $user->ID; ?>'><?php echo $user->display_name; ?></a></td></tr>
            <tr><td><?php _e("Customer Email:", "wpdm-premium-packages"); ?></td><td><a href='mailto:<?php echo $user->user_email; ?>'><?php echo $user->user_email; ?></a></td></tr>
            </tbody>
        </table>

    <?php } else { ?><b></b>
        <table class="table">

            <tbody>

            <tr><td><?php _e("Customer Name:", "wpdm-premium-packages"); ?></td><td><?php echo $billing['first_name'].' '.$billing['last_name']; ?></td></tr>
            <tr><td><?php _e("Customer Email:", "wpdm-premium-packages"); ?></td><td><a href="mailto:<?php echo $billing['order_email']; ?>"><?php echo $billing['order_email']; ?></a></td></tr>
            </tbody>
        </table>
    <table class="table">
        <thead>
        <tr><th align="left"><?php echo __("This order is not associated with any registered user", "wpdm-premium-packages"); ?></th></tr>
        </thead>
        <tr><td align="left" id="ausre" ><div class="input-group"><input placeholder="Username" type="text" class="form-control input-sm" id="ausr"><span class="input-group-btn"><input type="button" id="ausra" class="btn btn-primary btn-sm" value="<?php echo __("Assign User", "wpdm-premium-packages"); ?>"></span></div></td></tr>
     </table>
    <?php } ?>
            </div>
    </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading"><?php _e("IP Information", "wpdm-premium-packages"); ?></div>
                    <table class="table">
                        <tr><td><?php _e("IP Address:", "wpdm-premium-packages"); ?></td><td><?php echo $order->IP; ?></td></tr>
                        <tr><td><?php _e("Location:", "wpdm-premium-packages"); ?></td><td><div id="iploc">
                                    <script>
                                        jQuery(function(){
                                            jQuery.getJSON("http://ip-api.com/json/<?php echo $order->IP; ?>?callback=?", function(data) {
                                                var table_body = "";
                                                if(data.status!='fail'){
                                                 table_body += data.city+", ";
                                                 table_body += data.regionName+", ";
                                                 table_body += data.country;
                                                jQuery("#iploc").html(table_body);
                                                } else {
                                                    jQuery("#iploc").html('Private');
                                                }
                                            });
                                        });
                                    </script>
                        </div></td></tr>
                        <?php
                        if (count($tax) > 0) {
                            foreach ($tax as $taxrow) {
                                ?>
                                <tr><td><?php echo $taxrow['label']; ?></td><td><?php echo $currency_sign . $taxrow['rates']; ?></td></tr>
                            <?php
                            }
                        }

                        $ret = '';
                        $ret = apply_filters('wpdmpp_admin_order_details',$ret,$order->order_id);
                        if($ret != '') echo $ret;
                        ?>
                    </table>
                </div>
            </div>
            <div style="clear: both"></div>
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e("Order Items", "wpdm-premium-packages"); ?></div>
                <?php echo $content; ?>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading"><?php _e("Order Notes", "wpdm-premium-packages"); ?></div>
            </div>

        </div>
        </div>

        <?php include(dirname(__FILE__) . '/order-notes.php'); ?>
    </div>
</div>
</div>


<script>
    jQuery(function(){
        <?php
        $style = array(
            'Pending'=>'btn-warning',
            'Expired'=>'btn-danger',
            'Processing' => 'btn-info',
            'Completed'=>'btn-success',
            'Bonus' => 'btn-success',
            'Gifted' => 'btn-success',
            'Cancelled' => 'btn-danger',
            'Disputed' => 'btn-danger',
            'Refunded' => 'btn-danger'
        );
        ?>
        jQuery('select#osv').selectpicker({style: '<?php echo isset($style[$order->order_status])?$style[$order->order_status]:'btn-default'; ?>'});
        jQuery('select#psv').selectpicker({style: '<?php echo $style[$order->payment_status]; ?>'});

        jQuery('#update_os').click(function(){
            jQuery('#lng').fadeIn();
            jQuery.post(ajaxurl,{action:'wpdmpp_ajax_call',execute:'UpdateOS',order_id:'<?php echo $_GET['id']; ?>',status:jQuery('#osv').val()},function(res){
                jQuery('#msg').html(res).fadeIn();
                jQuery('#lng').fadeOut();
            });
        });

        jQuery('#update_ps').click(function(){
            jQuery('#lng').fadeIn();
            jQuery.post(ajaxurl,{action:'wpdmpp_ajax_call',execute:'UpdatePS',order_id:'<?php echo $_GET['id']; ?>',status:jQuery('#psv').val()},function(res){
                jQuery('#msg').html(res).fadeIn();
                jQuery('#lng').fadeOut();
            });
        });

        jQuery('#ausra').click(function(){
            jQuery.post(ajaxurl, {action: 'assign_user_2order', order: '<?php echo esc_attr($_GET['id']); ?>', assignuser: jQuery('#ausr').val()}, function(){
                jQuery('#ausre').html('Done!');
            });
        });
    });
</script>
<style>
    .chzn-search input{ display: none; }.chzn-results{ padding-top: 5px !important; }
    .btn-group.bootstrap-select .btn{ border-radius: 3px !important; }
    a:focus{ outline: none !important; }
    .panel-heading{ font-weight: bold; }
    .text-renew *{ font-weight: 800; color: #1e9460; }
    .w3eden .dropdown-menu > li{ margin-bottom: 0; }
    .w3eden .dropdown-menu > li > a{ padding: 5px 20px; }
</style>
