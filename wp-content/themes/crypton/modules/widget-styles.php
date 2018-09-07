<?php
 

$plugin_dir = basename(dirname(__FILE__));
 
global $wl_options;
$wl_load_points=array(  'plugins_loaded'    =>    __( 'when plugin starts (default)', 'newspress' ),
                        'after_setup_theme' =>    __( 'after theme loads', 'newspress' ),
                        'wp_loaded'         =>    __( 'when all PHP loaded', 'newspress' ),
                        'wp_head'           =>    __( 'during page header', 'newspress' )
                    );

if((!$wl_options = get_option('widget_style')) || !is_array($wl_options) ) $wl_options = array();

if (is_admin())
{
    add_filter( 'widget_update_callback', 'widget_styles_ajax_update_callback', 10, 3);                 // widget changes submitted by ajax method
    add_action( 'sidebar_admin_setup', 'widget_styles_expand_control');                                // before any HTML output save widget changes and add controls to each widget on the widget admin page

}
else
{
    if (    isset($wl_options['widget_style-options-load_point']) &&
            ($wl_options['widget_style-options-load_point']!='plugins_loaded') &&
            array_key_exists($wl_options['widget_style-options-load_point'],$wl_load_points )
        )
        add_action ($wl_options['widget_style-options-load_point'],'widget_styles_sidebars_widgets_filter_add');
    else
        widget_styles_sidebars_widgets_filter_add();
        
    if ( isset($wl_options['widget_style-options-filter']) && $wl_options['widget_style-options-filter'] == 'checked' )
        add_filter( 'dynamic_sidebar_params', 'widget_styles_widget_display_callback', 10);             // redirect the widget callback so the output can be buffered and filtered
}

function widget_styles_sidebars_widgets_filter_add()
{
    add_filter( 'sidebars_widgets', 'widget_styles_filter_sidebars_widgets', 10);                    // actually remove the widgets from the front end depending on Widget Style provided
}
// wp-admin/widgets.php explicitly checks current_user_can('edit_theme_options')
// which is enough security, I believe. If you think otherwise please contact me


// CALLED VIA 'widget_update_callback' FILTER (ajax update of a widget)
function widget_styles_ajax_update_callback($instance, $new_instance, $this_widget)
{    global $wl_options;
    $widget_id=$this_widget->id;
    if ( isset($_POST[$widget_id.'-widget_style']))
    {    $wl_options[$widget_id]=trim($_POST[$widget_id.'-widget_style']);
        update_option('widget_style', $wl_options);
    }
    return $instance;
}


// CALLED VIA 'sidebar_admin_setup' ACTION
// adds in the admin control per widget, but also processes import/export
function widget_styles_expand_control()
{    global $wp_registered_widgets, $wp_registered_widget_controls, $wl_options;


    // EXPORT ALL OPTIONS
    if (isset($_GET['wl-options-export']))
    {
        header("Content-Disposition: attachment; filename=widget_styles_options.txt");
        header('Content-Type: text/plain; charset=utf-8');
        
        echo "[START=WIDGET STYLES OPTIONS]\n";
        foreach ($wl_options as $id => $text)
            echo "$id\t".json_encode($text)."\n";
        echo "[STOP=WIDGET STYLES OPTIONS]";
        exit;
    }


    // IMPORT ALL OPTIONS
    if ( isset($_POST['wl-options-import']))
    {    if ($_FILES['wl-options-import-file']['tmp_name'])
        {    $import=split("\n",file_get_contents($_FILES['wl-options-import-file']['tmp_name'], false));
            if (array_shift($import)=="[START=WIDGET STYLES OPTIONS]" && array_pop($import)=="[STOP=WIDGET STYLES OPTIONS]")
            {    foreach ($import as $import_option)
                {    list($key, $value)=split("\t",$import_option);
                    $wl_options[$key]=json_decode($value);
                }
                $wl_options['msg']= __('Success! Options file imported','newspress');
            }
            else
            {    $wl_options['msg']= __('Invalid options file','newspress');
            }
            
        }
        else
            $wl_options['msg']= __('No options file provided','newspress');
        
        update_option('widget_style', $wl_options);
        wp_redirect( admin_url('widgets.php') );
        exit;
    }


    // ADD EXTRA Widget Style FIELD TO EACH WIDGET CONTROL
    // pop the widget id on the params array (as it's not in the main params so not provided to the callback)
    foreach ( $wp_registered_widgets as $id => $widget )
    {    // controll-less widgets need an empty function so the callback function is called.
        if (!$wp_registered_widget_controls[$id])
            wp_register_widget_control($id,$widget['name'], 'widget_styles_empty_control');
        $wp_registered_widget_controls[$id]['callback_wl_redirect']=$wp_registered_widget_controls[$id]['callback'];
        $wp_registered_widget_controls[$id]['callback']='widget_styles_extra_control';
        array_push($wp_registered_widget_controls[$id]['params'],$id);    
    }


    // UPDATE Widget Style WIDGET OPTIONS (via accessibility mode?)
    if ( 'post' == strtolower($_SERVER['REQUEST_METHOD']) )
    {    foreach ( (array) $_POST['widget-id'] as $widget_number => $widget_id )
            if (isset($_POST[$widget_id.'-widget_style']))
                $wl_options[$widget_id]=trim($_POST[$widget_id.'-widget_style']);
        
        // clean up empty options (in PHP5 use array_intersect_key)
        $regd_plus_new=array_merge(array_keys($wp_registered_widgets),array_values((array) $_POST['widget-id']),
            array('widget_style-options-filter', 'widget_style-options-wp_reset_query', 'widget_style-options-load_point'));
        foreach (array_keys($wl_options) as $key)
            if (!in_array($key, $regd_plus_new))
                unset($wl_options[$key]);
    }

    // UPDATE OTHER WIDGET STYLES OPTIONS
    // must update this to use http://codex.wordpress.org/Settings_API
    if ( isset($_POST['widget_style-options-submit']) )
    {    $wl_options['widget_style-options-filter']=$_POST['widget_style-options-filter'];
        $wl_options['widget_style-options-wp_reset_query']=$_POST['widget_style-options-wp_reset_query'];
        $wl_options['widget_style-options-load_point']=$_POST['widget_style-options-load_point'];
    }


    update_option('widget_style', $wl_options);

}


 

// added to widget functionality in 'widget_styles_expand_control' (above)
function widget_styles_empty_control() {}



// added to widget functionality in 'widget_styles_expand_control' (above)
function widget_styles_extra_control()
{    global $wp_registered_widget_controls, $wl_options;

    $params=func_get_args();
    $id=array_pop($params);

    // go to the original control function
    $callback=$wp_registered_widget_controls[$id]['callback_wl_redirect'];
    if (is_callable($callback))
        call_user_func_array($callback, $params);        
    
    $value = !empty( $wl_options[$id ] ) ? htmlspecialchars( stripslashes( $wl_options[$id ] ),ENT_QUOTES ) : '';

    // dealing with multiple widgets - get the number. if -1 this is the 'template' for the admin interface
    $number=$params[0]['number'];
    if ($number==-1) {$number="__i__"; $value="";}
    $id_disp=$id;
    if (isset($number)) $id_disp=$wp_registered_widget_controls[$id]['id_base'].'-'.$number;

    // output our extra Widget Style field
    echo "<p><label for='".$id_disp."-widget_style'>CSS Class Name: <input class='widefat' type='text' name='".$id_disp."-widget_style' id='".$id_disp."-widget_style' value='$value' /></label></p>";
}




// FRONT END FUNCTIONS...



// CALLED ON 'sidebars_widgets' FILTER
function widget_styles_filter_sidebars_widgets($sidebars_widgets)
{    global $wp_reset_query_is_done, $wl_options;

    //print_r($sidebars_widgets); die();
    // loop through every widget in every sidebar (barring 'wp_inactive_widgets') checking WL for each one
    foreach($sidebars_widgets as $widget_area => $widget_list)
    {    if ($widget_area=='wp_inactive_widgets' || empty($widget_list)) continue;

        foreach($widget_list as $pos => $widget_id)
        {    if (empty($wl_options[$widget_id]))  continue;
            $wl_value=stripslashes(trim($wl_options[$widget_id]));
            if (empty($wl_value))  continue;

            $wl_value=apply_filters( "widget_styles_eval_override", $wl_value );
            if ($wl_value===false)
            {    unset($sidebars_widgets[$widget_area][$pos]);
                continue;
            }
            if ($wl_value===true) continue;

            if (stristr($wl_value,"return")===false)
                $wl_value="return (" . $wl_value . ");";

            //if (!eval($wl_value))
            //    unset($sidebars_widgets[$widget_area][$pos]);
        }
    }
    return $sidebars_widgets;
}


function widget_style_apply($params){
    global $wl_options;
    for($i=0;$i<count($params); $i++){
        $style = isset($params[$i]['widget_id'])&&isset($wl_options[$params[$i]['widget_id']])&&$wl_options[$params[$i]['widget_id']]!=''?$wl_options[$params[$i]['widget_id']]:'default';
        if(isset($params[$i]['before_widget']))
        $params[$i]['before_widget'] = str_replace('[#widget_style#]',$style,$params[$i]['before_widget']);
    }
    return $params;
}

add_filter("dynamic_sidebar_params","widget_Style_apply");




// If 'widget_style-options-filter' is selected the widget_content filter is implemented...



// CALLED ON 'dynamic_sidebar_params' FILTER - this is called during 'dynamic_sidebar' just before each callback is run
// swap out the original call back and replace it with our own
function widget_styles_widget_display_callback($params)
{    global $wp_registered_widgets;
    $id=$params[0]['widget_id'];
    $wp_registered_widgets[$id]['callback_wl_redirect']=$wp_registered_widgets[$id]['callback'];
    $wp_registered_widgets[$id]['callback']='widget_styles_redirected_callback';
    return $params;
}


// the redirection comes here
function widget_styles_redirected_callback()
{    global $wp_registered_widgets, $wp_reset_query_is_done;

    // replace the original callback data
    $params=func_get_args();
    $id=$params[0]['widget_id'];
    $callback=$wp_registered_widgets[$id]['callback_wl_redirect'];
    $wp_registered_widgets[$id]['callback']=$callback;

    // run the callback but capture and filter the output using PHP output buffering
    if ( is_callable($callback) ) 
    {    ob_start();
        call_user_func_array($callback, $params);
        $widget_content = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'widget_content', $widget_content, $id);
    }
}



?>