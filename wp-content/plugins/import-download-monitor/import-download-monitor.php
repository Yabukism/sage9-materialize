<?php
/*
Plugin Name: WPDM - Import Download Monitor Data
Description: Import Download Monitor Data to Download Manager
Plugin URI: http://www.wpdownloadmanager.com/
Author: Shaon
Version: 1.0.0
Author URI: http://www.wpdownloadmanager.com/
*/


if(function_exists('add_wdm_settings_tab')){



    function wpdm_import_download_monitor(){
        global $wpdb, $wp_roles;
        set_time_limit(0);
        if(isset($_POST['__wpdm_dlm_import_data'])){
              $allposts = $wpdb->get_results("select * from {$wpdb->prefix}posts where post_type='dlm_download'");
            $wpdb->query("update {$wpdb->prefix}posts set post_type='wpdmpro' where post_type='dlm_download'");
            $wpdb->query("update {$wpdb->prefix}term_taxonomy set `taxonomy`='wpdmcategory' where `taxonomy`='dlm_download_category'");
            $wpdb->query("update {$wpdb->prefix}postmeta set meta_key='__wpdm_download_count' where meta_key='_download_count'");
            $guests = serialize(array('guest'));
            $roles = $wp_roles->role_names;
            $roles = array_keys($roles);
            $members = serialize($roles);
            $wpdb->query("update {$wpdb->prefix}postmeta set meta_key='__wpdm_access', meta_value='{$guests}' where meta_key='_members_only' and meta_value='no'");
            $wpdb->query("update {$wpdb->prefix}postmeta set meta_key='__wpdm_access', meta_value='{$members}' where meta_key='_members_only' and meta_value='yes'");
            foreach($allposts as $dlmp){
                $dlmfv = $wpdb->get_results("select * from {$wpdb->prefix}posts where post_type='dlm_download_version' and post_parent='{$dlmp->ID}'");
                    $files = array();
                    foreach($dlmfv as $dlmfv1) {
                        $tfiles = get_post_meta($dlmfv1->ID, '_files', true);
                        $tfiles = json_decode($tfiles);
                        $files = array_merge($files, $tfiles);
                    }
                    $files = array_unique($files);
                    update_post_meta($dlmp->ID, '__wpdm_files', $files);
            }
            die('All Download Monitor Data Imported Successfully!');
        }
        $wpdm_lazy_download = get_option('_wpdm_lazy_download',array());
        $data = wp_count_posts('dlm_download');

        $total = array_sum((array)$data);

        ?>
        <div class="panel panel-default">
            <div class="panel-heading"><b><?php _e('Set Waiting Time Before Start Download','wpdmpro'); ?></b></div>

            <div class="panel-body">
                <input type="hidden" name="__wpdm_dlm_import_data" value="1" />
                <div class="alert alert-info"><?php echo $total; ?> Download Monitor Post(s) Found</div>
                Just Click Save Settings Button To Import All Download Monitor Data!<br/>
               &nbsp;
            </div>

        </div>



    <?php
    }

    add_wdm_settings_tab("dlmonitor-import","DL Monitor Import", "wpdm_import_download_monitor");
}
