<?php
/**
 * Orders List Template User Dashboard >> Purchases tab
 *
 * This template can be overridden by copying it to yourtheme/download-manager/user-dashboard/purchased-items.php.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$sap                    = ( isset( $params['flaturl'] ) && $params['flaturl'] == 0 ) ? "?udb_page=" : "";
$currency_sign          = wpdmpp_currency_sign();
$currency_sign_before   = wpdmpp_currency_sign_position() == 'before' ? $currency_sign : '';
$currency_sign_after    = wpdmpp_currency_sign_position() == 'after' ? $currency_sign : '';
?>
<div class="panel panel-default dashboard-panel">
    <div class="panel-heading">
        <a href="<?php the_permalink();echo $sap; ?>purchases/orders/" class="pull-right"><?php _e('All Orders', 'wpdm-premium-packages'); ?></a><?php _e('Purchased Items', 'wpdm-premium-packages'); ?>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th><?php _e('Product Name','wpdm-premium-packages'); ?></th>
                <th><?php _e('Price','wpdm-premium-packages'); ?></th>
                <th><?php _e('Order ID','wpdm-premium-packages'); ?></th>
                <th><?php _e('Purchase Date','wpdm-premium-packages'); ?></th>
                <th><?php _e('Download','wpdm-premium-packages'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach($purchased_items as $item){ ?>

        <tr>
            <td><?php $title = get_the_title($item->pid); echo $title ? $title : '<span class="text-danger"><i class="fa fa-warning"></i> '.__('Product Deleted','wpdm-premium-packages').'</span>'; ?></td>
            <td><?php echo $currency_sign_before.number_format($item->price,2).$currency_sign_after; ?></td>
            <td><a href="<?php the_permalink(); echo $sap; ?>purchases/order/<?php echo $item->oid; ?>/"><?php echo $item->oid; ?></a></td>
            <td><?php echo date(get_option('date_format'),$item->odate); ?></td>
            <td>
                <?php if($item->order_status == 'Completed'){ ?>
                    <a href="<?php the_permalink();echo $sap; ?>purchases/order/<?php echo $item->oid; ?>/" class="btn btn-xs btn-primary btn-block"><?php _e('Download','wpdm-premium-packages'); ?></a>
                <?php } else { ?>
                    <a href="<?php the_permalink();echo $sap; ?>purchases/order/<?php echo $item->oid; ?>/" class="btn btn-xs btn-danger btn-block"><?php _e('Expired','wpdm-premium-packages'); ?></a>
                <?php } ?>
            </td>
        </tr>

        <?php } ?>
        </tbody>
    </table>
    <div class="panel-footer">
        <?php _e('If you are not seeing your purchased item:','wpdm-premium-packages'); ?> <a class="btn btn-warning btn-xs" style="color: #ffffff !important;" href="<?php the_permalink(); ?>?udb_page=purchases/orders/"><?php _e('Fix It Here','wpdm-premium-packages'); ?></a>
    </div>
</div>