<?php
/**
 * Order details in Frontend User Dashboard >> Purchases >> Order details page
 *
 * This template can be overridden by copying it to yourtheme/download-manager/user-dashboard/order-details.php.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb, $sap, $wpdmpp_settings, $current_user;

$order_notes    = '';
$order          = new Order();
$orderurl       = get_permalink(get_the_ID());
$loginurl       = home_url("/wp-login.php?redirect_to=".urlencode($orderurl));
$csign          = wpdmpp_currency_sign();
$csign_before   = wpdmpp_currency_sign_position() == 'before' ? $csign : '';
$csign_after    = wpdmpp_currency_sign_position() == 'after' ? $csign : '';
$link           = get_permalink()."?udb_page=purchases/orders/";
$o              = $order;
$order          = $order->GetOrder($params[2]);
$extbtns        = "";
$extbtns        = apply_filters("wpdmpp_order_details_frontend", $extbtns, $order);

if( $order->uid == 0 ) {
    $order->uid = $current_user->ID;
    $o->update( array('uid' => $current_user->ID), $order->order_id );
}

if( $order->uid == $current_user->ID ) {

    $order->currency    = maybe_unserialize($order->currency);
    $csign              = isset($order->currency['sign']) ? $order->currency['sign'] : '$';
    $csign_before       = wpdmpp_currency_sign_position() == 'before' ? $csign : '';
    $csign_after        = wpdmpp_currency_sign_position() == 'after' ? $csign : '';
    $cart_data          = unserialize($order->cart_data);
    $items              = Order::GetOrderItems($order->order_id);

    if ( count( $items ) == 0 ) {
        foreach ( $cart_data as $pid => $noi ) {
            $newi = get_posts(array('post_type' => 'wpdmpro', 'meta_key' => '__wpdm_legacy_id', 'meta_value' => $pid));
            if(count($newi) > 0) {
                $new_cart_data[$newi[0]->ID] = array("quantity" => $noi, "variation" => "", "price" => get_post_meta($newi[0]->ID, "__wpdm_base_price", true));
                $new_order_items[] = $newi[0]->ID;
            }
        }

        Order::Update(array('cart_data' => serialize($new_cart_data), 'items' => serialize($new_order_items)), $order->order_id);
        Order::UpdateOrderItems($new_cart_data, $order->order_id);
        $items = Order::GetOrderItems($order->order_id);
    }

    $order->title = $order->title ? $order->title : sprintf(__('Order # %s', 'wpdm-premium-packages') , $order->order_id);
    ?>

    <div class="panel panel-default panel-purchases dashboard-panel">
        <div class="panel-heading">
            <?php if ( $order->order_status == 'Completed' ) { //Show invoice button ?>
                <span class="pull-right btn-group">
                    <button id="od-fullwidth-view" class="btn btn-xs btn-primary ttip" title="Toggle Full-Width"><i class="fa fa-arrows-h"></i></button>
                    <a class="btn btn-info btn-xs white btn-invoice" href="#"
                       onclick="window.open('?id=<?php echo $order->order_id; ?>&amp;wpdminvoice=1','Invoice','height=720, width = 750, toolbar=0'); return false;">
                        <i class="fa fa-file-text-o"></i> <?php _e('Invoice', 'wpdm-premium-packages'); ?>
                    </a>
                    <?php echo $extbtns; ?>
                </span>
                <a href="<?php echo $link; ?>" style="display:inline;with:auto;"><?php _e("All Orders","wpdm-premium-packages"); ?></a> &nbsp;<i class="fa fa-angle-double-right"></i>&nbsp;<?php echo $order->title; ?>
            <?php } else { ?>
                <span class="pull-right btn-group" style="margin-top: -5px;margin-right: -10px">
                    <button id="od-fullwidth-view" class="btn btn-xs btn-primary ttip" title="Toggle Full-Width"><i class="fa fa-arrows-h"></i></button>
                </span>
                <a href="<?php echo $link; ?>"><?php _e("All Orders","wpdm-premium-packages"); ?></a>&nbsp;<i class="fa fa-angle-double-right"></i>&nbsp;<?php echo $order->title; ?>
            <?php } ?>
        </div>
        <div class="panel-body1">
            <table class="table" style="margin:0">
                <thead>
                <tr>
                    <th><?php _e("Product","wpdm-premium-packages"); ?></th>
                    <th><?php _e("Quantity","wpdm-premium-packages"); ?></th>
                    <th><?php _e("Unit Price","wpdm-premium-packages"); ?></th>
                    <th><?php _e("Coupon Discount","wpdm-premium-packages"); ?></th>
                    <th><?php _e("Role Discount","wpdm-premium-packages"); ?></th>
                    <th><?php _e("License","wpdm-premium-packages"); ?></th>
                    <th class='text-right' align='right'><?php _e("Total","wpdm-premium-packages"); ?></th>
                    <th class='text-right' align='right'><?php _e("Download","wpdm-premium-packages"); ?></th>
                </tr>
                </thead>
                <?php
                $total = 0;

                foreach ($items as $item) {
                    $ditem = get_post($item['pid']);

                    if ( ! is_object($ditem) || get_post_type($item['pid']) != 'wpdmpro' ) {
                        $ditem              = new stdClass();
                        $ditem->ID          = 0;
                        $ditem->post_title  = "[Item Deleted]";
                    }

                    $meta           = get_post_meta($ditem->ID, 'wpdmpp_list_opts', true);
                    $price          = $item['price'] * $item['quantity'];
                    $discount_r     = $item['role_discount'];
                    $prices         = 0;
                    $variations     = "";
                    $discount       = $discount_r;
                    $_variations    = unserialize($item['variations']);

                    foreach ($_variations as $vr) {
                        $variations .= "{$vr['name']}: +{$csign_before}" . number_format(floatval($vr['price']), 2) . $csign_after;
                        $prices     += number_format(floatval($vr['price']), 2);
                    }

                    $itotal         = number_format(((($item['price'] + $prices) * $item['quantity']) - $discount - $item['coupon_discount']), 2, ".", "");
                    $total          += $itotal;
                    $download_link  = home_url("/?wpdmdl={$item['pid']}&oid={$order->order_id}");
                    $licenseurl     = home_url("/?task=getlicensekey&file={$item['pid']}&oid={$order->order_id}");
                    $order_item     = "";

                    if ($order->order_status == 'Completed') {
                        if (get_post_meta($item['pid'], '__wpdm_enable_license', true) == 1) {
                            $licenseg = <<<LIC
<a id="lic_{$item['pid']}_{$order->order_id}_btn" onclick="return getkey('{$item['pid']}','{$order->order_id}');" class="btn btn-primary btn-xs" data-placement="top" data-toggle="popover" href="#"><i class="fa fa-key white"></i></a>
LIC;
                        } else
                            $licenseg = "&mdash;";

                        $indf   = "";
                        $files  = maybe_unserialize(get_post_meta($item['pid'], '__wpdm_files', true));
                        $cart   = maybe_unserialize($order->cart_data);
                        $sfiles = $cart[$item['pid']]['files'];
                        $sfiles = is_array($sfiles) ? array_keys($sfiles): array();
                        $cfiles = array();

                        foreach ($sfiles as $fID){
                            $cfiles[$fID] = $files[$fID];
                        }
                        if(count($cfiles) > 0)
                            $files = $cfiles;

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
                        ?>

                        <tr class="item">
                        <td><?php echo $ditem->post_title.'<br>'.$variations; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo $csign_before.$item['price'].$csign_after; ?></td>
                        <td><?php echo $csign_before.$item['coupon_discount'].$csign_after; ?></td>
                        <td><?php echo $csign_before.$discount.$csign_after; ?></td>
                        <td id="lic_<?php echo $item['pid'].'_'.$order->order_id; ?>" ><?php echo $licenseg; ?></td>
                        <td class='text-right' align='right'><?php echo $csign_before.$itotal.$csign_after; ?></td>

                    <?php } else {

                        $discount = number_format(floatval($discount), 2);
                        $item['price'] = number_format($item['price'], 2);

                        ?>
                        <tr class="item">
                        <td><?php echo $ditem->post_title.'<br>'.$variations; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo $csign_before.$item['price'].$csign_after; ?></td>
                        <td><?php echo $csign_before.$item['coupon_discount'].$csign_after; ?></td>
                        <td><?php echo $csign_before.$discount.$csign_after; ?></td>
                        <td>&mdash;</td>
                        <td class='text-right' align='right'><?php echo $csign_before.$itotal.$csign_after; ?></td>
                        <?php
                    }

                    if ($order->order_status == 'Completed') {

                        $spec = "";
                        if (count($files) > 1)
                            $spec = <<<SPEC
<a class="btn btn-xs btn-success btn-group-item" href="#" data-toggle="modal" data-target="#dpop" onclick="jQuery('#dpop .modal-body').html(jQuery('#indvd-{$ditem->ID}').html());"><i class="fa fa-list"></i></a></div><div  id="indvd-{$ditem->ID}" style="display:none;"><ul class='list-group'>{$indf}</ul>
SPEC;
                        ?>

                        <td class='text-right' align='right'>
                            <div class="btn-group">
                                <a href="<?php echo $download_link; ?>" class="btn btn-xs btn-success btn-group-item"><i class="fa fa-download white"></i></a>
                                <?php echo $spec; ?>
                            </div>
                        </td>
                        </tr>
                    <?php } else { ?>
                        <td  class='text-right' align='right'>&mdash;</td>
                        </tr>
                    <?php }

                    $order_item = apply_filters("wpdmpp_order_item", "", $item);

                    if ($order_item != '') { ?>
                        <tr><td colspan='7'><?php echo $order_item; ?></td></tr>
                    <?php }
                }

                $vdlink         = __("If you still want to complete this order ", "wpdm-premium-packages");
                $vdlink_expired = sprintf(__("Get continuous support and update for another %d days", "wpdm-premium-packages"), $wpdmpp_settings['order_validity_period']);
                $pnow           = __("Pay Now", "wpdm-premium-packages");
                $pnow_expired   = __("Renew Now", "wpdm-premium-packages");
                $usermeta       = unserialize(get_user_meta($current_user->ID, 'user_billing_shipping', true));

                if(is_array($usermeta)) extract($usermeta);

                $order->cart_discount   = number_format($order->cart_discount, 2, ".", "");
                $order->total           = number_format($order->total, 2, ".", "");
                $total                  = number_format($total, 2, ".", "");
                ?>

                <tr class="item">
                    <td colspan="6" class='text-right' align='right'><b><?php _e("Subtotal","wpdm-premium-packages"); ?></b></td>
                    <td class='text-right' align='right'><b><?php echo $csign_before.$total.$csign_after; ?></b></td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="item">
                    <td colspan="6" class='text-right' align='right'><b><?php _e("Coupon Discount","wpdm-premium-packages"); ?></b></td>
                    <td class='text-right' align='right'><b>- <?php echo $csign_before.$order->coupon_discount.$csign_after; ?></b></td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="item">
                    <td colspan="6" class='text-right' align='right'><b><?php _e("Tax","wpdm-premium-packages"); ?></b></td>
                    <td class='text-right' align='right'><b>+ <?php echo $csign_before.$order->tax.$csign_after; ?></b></td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="item">
                    <td colspan="6" class='text-right' align='right'><b><?php _e("Total","wpdm-premium-packages"); ?></b></td>
                    <td class='text-right' align='right'><b><?php echo $csign_before.$order->total.$csign_after; ?></b></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
        <div class="modal fade" id="dpop">
            <div class="modal-dialog" style="margin-top:100px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close pull-right" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" style="display:inline;">
                            <strong><?php _e('Download Single File','wpdm-premium-packages'); ?></strong>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <p>One fine body&hellip;</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','wpdm-premium-packages'); ?></button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <?php

        if ($order->order_status != 'Completed') {
            if ($order->order_status == 'Expired') {
                $vdlink = $vdlink_expired;
                $pnow = $pnow_expired;
            }
            $purl = home_url('/?pay_now=' . $order->order_id);
            ?>

            <div class="panel-footer" style="line-height: 30px !important;">
                <b><?php _e("Order Status", "wpdm-premium-packages"); ?> : <span class='label label-danger'><?php echo $order->order_status; ?></span></b>&nbsp;&nbsp;
                <div class='pull-right'>
                    <?php echo $vdlink; ?>
                    <div class="pull-right" style="margin-left:10px" id="proceed_<?php echo $order->order_id; ?>">
                        <a class='btn btn-success white btn-sm' onclick="return proceed2payment_<?php echo $order->order_id; ?>(this)" href="#"><b><?php echo $pnow; ?></b></a>
                    </div>
                </div>
            </div>
            <script>
                function proceed2payment_<?php echo $order->order_id; ?>(ob){
                    jQuery('#proceed_<?php echo $order->order_id; ?>').html('Processing...');
                    jQuery.post('<?php echo $purl; ?>',{action:'wpdm_pp_ajax_call',execute:'PayNow',order_id:'<?php echo $order->order_id; ?>'},function(res){
                        jQuery('#proceed_<?php echo $order->order_id; ?>').html(res);
                    });
                    return false;
                }
            </script>

        <?php }

        $homeurl = home_url('/'); ?>
    </div>
    <script>
        function getkey(file, order_id){
            jQuery('#lic_'+file+'_'+order_id+'_btn').html("<i class='fa fa-spin fa-spinner white'></i>");
            jQuery.post('<?php echo $homeurl; ?>',{execute:'getlicensekey',fileid:file,orderid:order_id},function(res){
                res = "Copy the following key<br/><input class='form-control' style='cursor:text' onfocus='this.select()' type=text readonly=readonly value='"+res+"' />";
                jQuery('#lic_'+file+'_'+order_id+'_btn').popover({html: true, title: "License Key <button class='pull-right btn btn-danger btn-xs cpo' rel='#lic_"+file+"_"+order_id+"_btn' style='line-height:14px;margin-right:-5px;margin-top:-1px' id='cppo'>&times;</button>", content: res}).popover('show');
                jQuery('#lic_'+file+'_'+order_id+'_btn').html("<i class='fa fa-key white'></i>");

                jQuery('.cpo').on("click",function(e) {
                    jQuery(jQuery(this).attr("rel")).popover('destroy');
                    return false;
                });
            });
            return false;
        }

        jQuery(function($){
            var fullwidth = 0;
            $('body').on('click','#od-fullwidth-view', function(){
                fullwidth = fullwidth == 0 ? 1 : 0;
                $('#wpdm-dashboard-sidebar').toggle();
                $('#wpdm-dashboard-contents').toggleClass('col-md-9 col-md-12');
            });
        });

        //To show license TaCs
        function viewlic(file, order_id){
            var res = " You have to accept these Terms and Conditions before using this product.";
            jQuery('#lic_'+file+'_'+order_id+'_view').html("<i class='fa fa-spin fa-spinner white'></i>");
            jQuery('#lic_'+file+'_'+order_id+'_view').popover({html: true, title: "Terms and Conditions<button class='pull-right btn btn-danger btn-xs xx' rel='#lic_"+file+"_"+order_id+"_view' id='cppo'>&times;</button>", content: res}).popover('show');
            jQuery('#lic_'+file+'_'+order_id+'_view').html("<i class='fa fa-copy white'></i>");

            jQuery('.xx').on("click",function(e) {
                jQuery(jQuery(this).attr("rel")).popover('destroy');
                return false;
            });

            return false;
        }
    </script>
    <style>.white{ color: #ffffff !important; } </style>

    <?php
    include wpdm_tpl_path('partials/order-notes.php', WPDMPP_BASE_DIR . '/templates/');
} else { ?>
    <div class='alert alert-danger'><?php _e('Order does not belong to you!','wpdm-premium-packages'); ?></div>
<?php }