<?php
/**
 * Template for Checkout Billing Info, Customer Name and Email, and Payment Gateway Options
 *
 * This template can be overridden by copying it to yourtheme/download-manager/checkout-cart/checkout.php.
 *
 * @version     1.0.0
 */

if (!defined('ABSPATH')) die('!');

global $current_user;
$billing = $sbilling = array
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
    'phone' => ''
);

if (is_user_logged_in())
    $sbilling = maybe_unserialize(get_user_meta(get_current_user_id(), 'user_billing_shipping', true));

$sbilling = is_array($sbilling) && isset($sbilling['billing']) ? $sbilling['billing'] : array();
$billing = shortcode_atts($billing, $sbilling);

// If email and name is not available from { Edit Profile >> Billing info } get current user email and name
if ($billing['order_email'] == '' && is_user_logged_in()) $billing['order_email'] = $current_user->user_email;
if ($billing['first_name'] == '' && is_user_logged_in()) $billing['first_name'] = $current_user->display_name;

?>
<div id="select-payment-method">
    <form action="" name="payment_form" id="payment_form" method="post">
        <div class="panel panel-default">

            <?php
            if (get_wpdmpp_option('billing_address') == 1) {
                // Ask Billing Address When Checkout
                include wpdm_tpl_path('checkout-cart/checkout-billing-info.php', WPDMPP_BASE_DIR . '/templates/');
            } else {
                // Ask only Name and Email When Checkout
                include wpdm_tpl_path('checkout-cart/checkout-name-email.php', WPDMPP_BASE_DIR . '/templates/');
            }
            ?>

            <?php
            // Show active payment methods
            include wpdm_tpl_path('checkout-cart/checkout-payment-methods.php', WPDMPP_BASE_DIR . '/templates/');
            ?>

            <div class="panel-footer text-right">
                <div class="pull-left hide cart-total-final btn color-green">
                    <?php echo apply_filters("wpdmpp_checkout_footer_tax_label", __('Total Including Tax:', 'wpdm-premium-packages') ); ?>
                    <span class="badge"></span>
                </div>
                <button id="pay_btn" class="button btn btn-success" type="submit">
                    <i class="fa fa-credit-card"></i>&nbsp;<?php echo apply_filters("wpdmpp_checkout_pay_button_label", __('Pay Now', 'wpdm-premium-packages') ); ?>
                </button>
                <div class="hide pull-right" id="payment_w8"><img src='<?php echo admin_url('/images/loading.gif'); ?>'/></div>
            </div>

        </div>

    </form><br />
    <div id="paymentform"></div>
</div>