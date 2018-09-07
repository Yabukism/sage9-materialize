<?php
/*
Plugin Name: WPDM - Download Limits
Description: Enable WPDM Pro to set download limit / * day(s) for single user and user roles
Plugin URI: http://www.wpdownloadmanager.com/
Author: Shaon
Version: 2.2.1
Author URI: http://www.wpdownloadmanager.com/
*/



function wpdm_dd_limit_html(){
 global $post;
 if(get_post_type()!='wpdmpro') return;
?>

<script language="JavaScript">
<!--
jQuery(function(){
  //jQuery('#access_row').before('<tr id="daily_download_limit_row"><td><a name="new-set"></a>Download Limit:</td><td><input type="text" value="<?php echo get_post_meta($post->ID,'__wpdm_daily_download_limit',true); ?>" name="file[daily_download_limit]" style="width: 80px" size="10"> / day / user<span title="Set/Reset Download Limit per day for a user" id="new-set-i" class="info infoicon">&nbsp;</span></td></tr>');
});  

<?php if(!get_option('global_wpdm_dd_option',0)){ ?>
        jQuery(document).ready( function($) {
            var options = {"content":"<h3>User Download Limit<\/h3><p>Global option for user download limit, you can set any numeric value here. Users will not be able to download anymore after their daily download count exceed this value. Set 0 for unlimited <a href='admin.php?page=file-manager/add-new-package#new-set'><b>next &#187;</b></a><\/p>","position":{"edge":"left","align":"center"}};

            if ( ! options )
                return;

            options = $.extend( options, {
                close: function() {
                    $.post( ajaxurl, {
                        pointer: 'global_wpdm_dd_option',
                        action: 'dismiss-wpdm-dd-pointer'
                    }); 
                }
            });

            $('#new-set').pointer( options ).pointer('open');
        });
        
<?php } ?>

<?php if(!get_option('indv_wpdm_dd_option',0)){ ?>
        jQuery(document).ready( function($) {
            var options = {"content":"<h3>User Download Limit<\/h3><p>Individual package option for user download limit, you can set any numeric value here. Users will not be able to download anymore after their daily download count exceed this value. Set 0 for unlimited. This option will work only if it is lower then global limit <a href='admin.php?page=file-manager/settings#new-set'>here</a> <a href='admin.php?page=file-manager/add-new-package#new-set'></a><\/p>","position":{"edge":"left","align":"center"}};

            if ( ! options )
                return;

            options = jQuery.extend( options, {
                close: function() {
                    jQuery.post( ajaxurl, {
                        pointer: 'indv_wpdm_dd_option',
                        action: 'dismiss-wpdm-dd-pointer'
                    }); 
                }
            });

            jQuery('#new-set-i').pointer( options ).pointer('open');
        });
        
<?php } ?>


//-->
</script>

<?php    
    
}


function wpdm_dd_check_user_download($package){
    global $current_user;

    if(is_user_logged_in()){
    $time = get_user_meta($current_user->ID,'wpdm_reset_time',true);            
    if($time<time()) {
        update_user_meta($current_user->ID,'wpdm_reset_time',strtotime("+".get_option("_wpdm_ddl_period",1)." day"));
        update_user_meta($current_user->ID,'wpdm_dd_user_dlc',0);
    }} else {

        $time = get_option('wpdm_reset_time_'.str_replace(":","_",$_SERVER['REMOTE_ADDR']),true);
        if($time<time()) {
            update_option('wpdm_reset_time_'.str_replace(":","_",$_SERVER['REMOTE_ADDR']),strtotime("+".get_option("_wpdm_ddl_period",1)." day"));
            $gkey = 'wpdm_dd_user_dlc_'.str_replace(":","_",$_SERVER['REMOTE_ADDR']);
            update_option($gkey,0);
        }
    }

    //s
    if(!is_user_logged_in()){
        $gkey = 'wpdm_dd_user_dlc_'.str_replace(":","_",$_SERVER['REMOTE_ADDR']);
        $user_dlc = (int)get_option($gkey,0);
    } else
        $user_dlc = (int)get_user_meta($current_user->ID,'wpdm_dd_user_dlc',true);

    $global_ddl =  get_option('_wpdm_user_dl_per_day',0);
    $global_role_ddl = get_option('_wpdm_global_role_limit',array());

    $role = is_user_logged_in()?$current_user->roles[0]:'guest';
    $role_ddl = $global_role_ddl[$role];

    $user_ddl = $role_ddl?$role_ddl:$global_ddl;
    //update_user_meta($current_user->ID,'wpdm_dd_user_dlc',0);
    if($user_dlc>$user_ddl && $user_ddl>0) {
        $message=<<<LMT
Thanks for staying with us. Unfortunately your daily download limit ( $user_dlc ) already exceeded for this package. Please try again tomorrow.
LMT;
        header("Content-Description: File Transfer");
        header('Content-type: text/plain');
        header("Content-Disposition: attachment; filename=\"daily-download-limit-exceeded.txt\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . strlen($message));
        die($message);
    }  
     
    return $package;
}

function wpdm_dd_update_user_download($p){
    global $current_user;
    if(isset($_SESSION['dle_'.$p['ID']])) return;
    if(is_user_logged_in()){
    $user_dlc = (int)get_user_meta($current_user->ID,'wpdm_dd_user_dlc',true);
    $user_dlc++;
    update_user_meta($current_user->ID,'wpdm_dd_user_dlc',$user_dlc);
    } else {     
    $gkey = 'wpdm_dd_user_dlc_'.str_replace(":","_",$_SERVER['REMOTE_ADDR']);
     $user_dlc = (int)get_option($gkey,0);

        $user_dlc++;
    update_option($gkey,$user_dlc);
    }
    $_SESSION['dle_'.$p['ID']] = 1;
    //dd($user_dlc);
    //return $p;
}

function wpdm_dd_download_link($package){
    global $current_user;

    if(is_user_logged_in()){
        $time = get_user_meta($current_user->ID,'wpdm_reset_time',true);

        if($time<time()) {
            update_user_meta($current_user->ID,'wpdm_reset_time',strtotime("+".get_option("_wpdm_ddl_period",1)." day"));
            update_user_meta($current_user->ID,'wpdm_dd_user_dlc',0);
        }} else {

        $time = get_option('wpdm_reset_time_'.str_replace(":","_",$_SERVER['REMOTE_ADDR']),true);
        if($time<time()) {
            update_option('wpdm_reset_time_'.str_replace(":","_",$_SERVER['REMOTE_ADDR']),strtotime("+".get_option("_wpdm_ddl_period",1)." day"));
            $gkey = 'wpdm_dd_user_dlc_'.str_replace(":","_",$_SERVER['REMOTE_ADDR']);
            update_option($gkey,0);
        }
    }


    if(!is_user_logged_in()){
        $gkey = 'wpdm_dd_user_dlc_'.str_replace(":","_",$_SERVER['REMOTE_ADDR']);
        $user_dlc = (int)get_option($gkey,0);
    } else
        $user_dlc = (int)get_user_meta($current_user->ID,'wpdm_dd_user_dlc',true);
//echo $user_dlc; die();
    $global_ddl =  get_option('_wpdm_user_dl_per_day',0);
    $global_role_ddl = get_option('_wpdm_global_role_limit',array());

    $role = is_user_logged_in()?$current_user->roles[0]:'guest';
    $role_ddl = $global_role_ddl[$role];

    $user_ddl = $role_ddl?$role_ddl:$global_ddl;

    if($user_dlc>$user_ddl && $user_ddl>0) {
    $package['download_link'] = "<span class='text-danger'><i class='fa fa-exclamation-triangle'></i> Download Limit Exceeded</span>";
    }
    return $package;
}

function wpdm_dd_limit_by_roles(){
    global $post;
     
    ?>
    

<?php
    $role_ddl = get_post_meta($post->ID,'__wpdm_role_limit', true);
 
    
    ?>

<table class="table widefat fixed frm">
    <thead>
<tr><th style="width:200px">User Role</th><th>Download Limit</th></tr>
    </thead>
    <tr><td>Guests</td><td><input type="text" name="file[role_limit][guest]" value="<?php if(isset($role_ddl['guest'])) echo $role_ddl['guest']; ?>" /></td></tr>
    <?php
    global $wp_roles;
    $roles = array_reverse($wp_roles->role_names);    
    foreach( $roles as $role => $name ) { 
    ?>
    <tr><td><?php echo $name; ?></td><td><input type="text" name="file[role_limit][<?php echo $role; ?>]" value="<?php if(isset($role_ddl[$role])) echo $role_ddl[$role]; ?>"></td></tr>
    <?php } ?>
    </table>

        <div class="clear"></div>


    
    <?php
}

function wpdm_ddl_tab($tabs){
    $tabs['wpdm_dd_limit_by_roles'] = array('name' => __('Download Limit', "wpdmpro"), 'callback' => 'wpdm_dd_limit_by_roles');
    return $tabs;
}

function wpdm_dd_global_limit_by_roles(){
    global $current_user;
     
    ?>

 
<?php
    $global_role_ddl = get_option('_wpdm_global_role_limit',array());
 
    
    ?>
         <div class="panel panel-default">
             <div class="panel-heading">Download Limit</div>
         <table class="table">
             <tr>
                 <td><a name='new-set'></a>User Download Limit:</td><td><input class="form-control input-sm" style="width: 50px;display: inline" type="text" name="_wpdm_user_dl_per_day" value="<?php echo get_option('_wpdm_user_dl_per_day',0); ?>"> <span class="info infoicon" id="new-set" title="Download limit per user, `0` for unlimited">(?)</span></td>
             </tr>
             <tr><td>Limit Reset Period:</td><td><input type="text" name="_wpdm_ddl_period" class="form-control input-sm" style="width: 50px; display: inline" value="<?php echo get_option('_wpdm_ddl_period',1); ?>" /> day(s) </td></tr>

             <tr><th colspan="2">Limit Download By User Roles</th> </tr>
<tr><th>User Role</th><th>Download Limit</th></tr>
    <tr><td>Guests</td><td><input  class="form-control input-sm" style="width: 100px" type="text" name="_wpdm_global_role_limit[guest]" value="<?php echo isset($global_role_ddl['guest'])?$global_role_ddl['guest']:""; ?>" /></td></tr>
    <?php
    global $wp_roles;
    $roles = array_reverse($wp_roles->role_names);    
    foreach( $roles as $role => $name ) { 
    ?>
    <tr><td><?php echo $name; ?></td><td><input  class="form-control input-sm" style="width: 100px" type="text" size="5" name="_wpdm_global_role_limit[<?php echo $role; ?>]" value="<?php echo isset($global_role_ddl[$role])?$global_role_ddl[$role]:""; ?>"></td></tr>
    <?php } ?>
    </table>

         </div>

 

    
    <?php
}


add_filter('wpdm_after_prepare_package_data','wpdm_dd_download_link');
add_filter('before_download','wpdm_dd_check_user_download');
add_action('wpdm_onstart_download','wpdm_dd_update_user_download');

add_action('basic_settings_section','wpdm_dd_global_limit_by_roles');


