<?php
/**
 * Add To Cart form shown after pacakge price range
 *
 * This template can be overridden by copying it to yourtheme/download-manager/add-to-cart/form.php.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $current_user;
do_action("wpdmpp_before_add_to_cart_form"); ?>

    <form method="post" action="" name="cart_form" class="wpdm_cart_form wpdm_cart_form_<?php echo $product_id; ?>" id="wpdm_cart_form_<?php echo $product_id; ?>">
        <input type="hidden" name="addtocart" value="<?php echo $product_id; ?>">
        <input type="hidden" name="files" id="files_<?php echo $product_id; ?>" class="files_<?php echo $product_id; ?>" value="">
        <div data-curr="<?php echo $currency_sign; ?>" id="total-price-<?php echo $product_id; ?>"></div>

        <?php do_action('wpdmpp_before_add_to_cart_button', $product_id); ?>
        <?php echo wpdmpp_product_license_options_html($product_id); ?>
        <?php echo wpdmpp_product_gigs_options_html($product_id); ?>
        <?php
        $role_discount = wpdmpp_role_discount($product_id);
        if ($role_discount > 0) { ?>
            <div class="alert alert-info">
                <?php echo sprintf(__("%s %s discount will be applied in the cart", "wpdm-premium-packages"), $role_discount . '%', ucfirst($current_user->roles[0])); ?>
            </div>
        <?php } ?>
        <span class="add-to-cart-button">
            <button <?php echo $cart_enable; ?> class="<?php echo $add_to_cart_button_class; ?> btn-addtocart-<?php echo $product_id; ?>"
                                                data-cart-redirect="<?php echo(isset($settings['wpdmpp_after_addtocart_redirect']) ? 'on' : 'off'); ?>"
                                                type="submit"
                                                id="cart_submit"><?php echo $add_to_cart_button_label; ?> <span
                        class="price-<?php echo $product_id; ?> label label-price"></span></button>
        </span>
        <?php do_action('wpdmpp_after_add_to_cart_button', $product_id); ?>
    </form>
<?php do_action('wpdmpp_after_add_to_cart_form', $product_id); ?>