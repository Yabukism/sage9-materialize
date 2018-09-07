<?php
/**
 * User: shahnuralam
 * Date: 6/24/18
 * Time: 10:47 PM
 */
if (!defined('ABSPATH')) die();
?>
<div class="panel panel-default dashboard-panel">
    <div class="panel-heading">
        <?php _e( "Public Profile Info" , "download-manager" ); ?>
    </div>
    <div class="panel-body">

        <div class="form-group">
            <label><?php _e( "Title" , "download-manager" ); ?></label>
            <input type="text" value="<?php if (isset($store['title'])) echo $store['title']; ?>" placeholder="" id="" name="__wpdm_public_profile[title]" class="form-control">
        </div>
        <div class="form-group">
            <label><?php _e( "Short Intro" , "download-manager" ); ?></label>
            <input type="text" value="<?php if (isset($store['intro'])) echo $store['intro']; ?>" placeholder="" id="" name="__wpdm_public_profile[intro]" class="form-control">
        </div>
        <div class="form-group">
            <label for="store-logo"><?php _e( "Logo URL" , "download-manager" ); ?></label>
            <div class="input-group">
                <input type="text" name="__wpdm_public_profile[logo]" id="store-logo" class="form-control" value="<?php echo isset($store['logo']) ? $store['logo'] : ''; ?>"/>
                <span class="input-group-btn">
                        <button class="btn btn-default wpdm-media-upload" type="button" rel="#store-logo"><i class="far fa-image"></i></button>
                    </span>
            </div>
        </div>
        <div class="form-group">
            <label for="store-banner"><?php _e( "Banner URL" , "download-manager" ); ?></label>
            <div class="input-group">
                <input type="text" name="__wpdm_public_profile[banner]" id="store-banner" class="form-control" value="<?php echo isset($store['banner']) ? $store['banner'] : ''; ?>"/>
                <span class="input-group-btn">
                        <button class="btn btn-default wpdm-media-upload" type="button" rel="#store-banner"><i class="far fa-image"></i></button>
                    </span>
            </div>
        </div>
        <div class="form-group">
            <label for="store-banner"><?php _e( "Profile Header Text Color" , "download-manager" ); ?></label>

            <input type="text" name="__wpdm_public_profile[txtcolor]" id="store-banner" class="form-control color-picker" value="<?php echo isset($store['txtcolor']) ? $store['txtcolor'] : '#333333'; ?>"/>

        </div>
        <div class="form-group">
            <label><?php _e( "Description" , "download-manager" ); ?></label>
            <textarea type="text" data-placeholder="<?php _e( "Description" , "download-manager" ); ?>" id="" name="__wpdm_public_profile[description]" class="form-control"><?php if (isset($store['description'])) echo $store['description']; ?></textarea>
        </div>
    </div>

</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php _e( "Payment Settings" , "download-manager" ); ?>
    </div>
    <div class="panel-body">
        <label><?php _e( "PayPal Email" , "download-manager" ); ?></label>
        <input type="email" value="<?php if (isset($store['paypal'])) echo $store['paypal']; ?>" placeholder="" id="" name="__wpdm_public_profile[paypal]" class="form-control">
    </div>
</div>

<script>
    jQuery(document).ready(function($){
        $('.color-picker').wpColorPicker();
    });
</script>
