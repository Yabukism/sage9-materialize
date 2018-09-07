<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $payment_methods;
$payment_methods = apply_filters('payment_method', $payment_methods);
$payment_methods = count(get_wpdmpp_option('pmorders', array())) == count($payment_methods) ? get_wpdmpp_option('pmorders') : $payment_methods;
$settings['currency_position'] = isset($settings['currency_position']) ? $settings['currency_position'] : 'before';
?>
<div style="clear: both;margin-top:20px ;"></div>
<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Payment Methods Configuration", "wpdm-premium-packages"); ?></div>
    <div id="paccordion" class="wpmppgac">
        <div class="panel-body">
            <div class="panel-group" id="wpdmpp-payment-methods" style="margin: 0">
                <?php
                foreach ($payment_methods as $payment_method) {
                    if (class_exists($payment_method)) {
                        $obj = new $payment_method();
                        $name = isset($obj->GatewayName)?$obj->GatewayName:$payment_method;
                        ?>
                        <div class="panel panel-default">
                            <?php
                            echo '<div class="panel-heading"><b><i title="'.__('Drag and Drop to re-order','wpdm-premium-packages').'" class="fa fa-arrows-v" style="color: #B27CD6;cursor: move"></i> &nbsp; <a data-toggle="collapse" data-parent="#wpdmpp-payment-methods" href="#'.$payment_method.'">' . ucwords($name) . '</a></b>';
                            echo '<div class="pull-right" id="pmstatus_'.$payment_method.'">';
                            if (isset($settings[$payment_method]['enabled']) && $settings[$payment_method]['enabled'] == 1)
                                echo "<span class='text-success'> <i class='fa fa-check-circle'></i> " . __("Active", "wpdm-premium-packages")."</span>";
                            else
                                echo '<span class="text-danger"> <i class="fa fa-times-circle"></i> '.__("Inactive", "wpdm-premium-packages").'</span>';

                            echo '</div>';
                            echo '</div>';
                            echo '<div id="'.$payment_method.'" class="panel-collapse collapse">';
                            echo '<div class="panel-body">';
                            echo Payment::GateWaySettings($payment_method);
                            echo '</div>';
                            echo '</div>';
                            ?>
                            <input type="hidden" name="_wpdmpp_settings[pmorders][]" value="<?php echo $payment_method; ?>">
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
 
<div style="clear: both;margin-top:20px ;"></div>

<div class="panel panel-default">
    <div class="panel-heading"><?php echo __("Currency Configuration", "wpdm-premium-packages"); ?></div>
    <div class="panel-body">
        <table class="table">
            <tr>
                <td style="border: none;"><?php _e('Currency:', 'wpdm-premium-packages'); ?></td>
                <td style="border: none;"><?php Currencies::CurrencyListHTML(array('name'=>'_wpdmpp_settings[currency]', 'selected'=> (isset($settings['currency'])?$settings['currency']:''))); ?></td>
            </tr>
            <tr>
                <td style="border: none;"><?php _e('Currency Sign Position:', 'wpdm-premium-packages'); ?></td>
                <td style="border: none;">
                    <select class='form-control wpdmpp-currecy-position-dropdown' name="_wpdmpp_settings[currency_position]">
                        <option value="before" <?php selected( $settings['currency_position'], 'before' ); ?>><?php _e('Before - $99', 'wpdm-premium-packages'); ?></option>
                        <option value="after" <?php selected( $settings['currency_position'], 'after' ); ?>><?php _e('After - 99$', 'wpdm-premium-packages'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
    </div>
</div>

<script>
    jQuery(function($) {
        $('#wpdmpp-payment-methods').sortable();
        $('.ttip').tooltip();
        jQuery('.wpdmpp-currecy-dropdown, .wpdmpp-currecy-position-dropdown').chosen({width:'300px'});
    });
</script>
