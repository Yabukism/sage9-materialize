<?php

define("WPEDEN_THEME_DIR",dirname(dirname(__FILE__)));
define("WPEDEN_THEME_URL",get_stylesheet_directory_uri());

global $wpeden_wf_data;

error_reporting(0);
 
 
### SECTION
//Theme admin css & js
function wpeden_theme_admin_scripts($hook){   
    if($hook!='appearance_page_wpeden-themeopts') return;
    wp_enqueue_style('bootstrap-ui',WPEDEN_THEME_URL.'/admin/bootstrap/css/bootstrap.css');
    wp_enqueue_style('mCustomScrollbar',WPEDEN_THEME_URL.'/css/jquery.mCustomScrollbar.css');
    wp_enqueue_style('bootstrap-colorpicker',WPEDEN_THEME_URL.'/admin/bootstrap/colorpicker/css/colorpicker.css');
    wp_enqueue_style('chosen-ui',WPEDEN_THEME_URL.'/admin/css/chosen.css');
    wp_enqueue_style('admincss',WPEDEN_THEME_URL.'/admin/css/base-admin-style.css');
    wp_enqueue_script('jquery-form',array('jquery'));
    wp_enqueue_script('bootstrap-js',WPEDEN_THEME_URL.'/admin/bootstrap/js/bootstrap.min.js',array('jquery'));
    wp_enqueue_script('bootstrap-colorpicker-js',WPEDEN_THEME_URL.'/admin/bootstrap/colorpicker/js/bootstrap-colorpicker.js',array('jquery','bootstrap-js'));
    wp_enqueue_script('chosen-js',WPEDEN_THEME_URL.'/admin/js/chosen.jquery.js',array('jquery'));
    wp_enqueue_script('scrollbar-js',WPEDEN_THEME_URL.'/js/jquery.mCustomScrollbar.concat.min.js',array('jquery'));
    wp_enqueue_script('blockui-js',WPEDEN_THEME_URL.'/admin/js/jquery.blockUI.js',array('jquery'));
    wp_enqueue_script('wpeden-js',WPEDEN_THEME_URL.'/admin/js/wpeden.js',array('jquery','chosen-js','blockui-js','jquery-form'));
    wp_enqueue_script('media-upload');
    wp_enqueue_media();
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_style( 'wp-color-picker' );
}

add_action( 'admin_enqueue_scripts', 'wpeden_theme_admin_scripts');
//Theme admin css & js ends ^^
 
### SECTION
//Theme option menu function
function wpeden_theme_opt_menu(){                                                                                             /*Theme Option Menu*/
      add_theme_page( "WP Eden Theme Options", "Theme Options", 'edit_theme_options', 'wpeden-themeopts', 'wpeden_theme_options');
}

function wpeden_get_layout_type($default='wide'){
    echo wpeden_get_theme_opts('wpeden_layout_type',$default);
}

/**
* Generate theme option settings fields
* 
* @param mixed $data
*/

function wpeden_setting_field($data) {         
    switch($data['type']):
            case 'text':
                echo "<input type='text' name='$data[name]' class='input {$data['cssclass']}' value='$data[value]' placeholder='{$data['placeholder']}'  />";
                echo "<div class='note'>{$data['description']}</div>";
            break;
            case 'textarea':
                echo "<textarea name='$data[name]' class='input span12'>$data[value]</textarea>";
                echo "<div class='note'>{$data['description']}</div>";
            break;
            case 'callback':
                echo call_user_func($data['dom_callback'], $data['dom_callback_params']);
                echo "<div class='note'>{$data['description']}</div>";
            break;
            case 'heading':
                echo "<h3>".$data['label']."</h3>";
            break;
    endswitch;
}

 
global $wpede_data_fetched;
/**
* Get theme option value
* 
* @param mixed $index
* @param mixed $default
* @return mixed
*/
function wpeden_get_theme_opts($index = null, $default = null){
    global $wpeden_wf_data, $settings_sections_ex, $wpede_data_fetched;

    $settings_sections_ex = apply_filters("themeopt_section",$settings_sections_ex);

    if(!$wpede_data_fetched){
    $wpeden_wf_data = array(); 
     
    foreach($settings_sections_ex as $s => $settings_sections) {
    foreach($settings_sections as $section_id => $section_name) {
    $sdata = get_option($section_id,array());
    if(!is_array($sdata)) $sdata = array();
    $wpeden_wf_data = array_merge($wpeden_wf_data,$sdata); 
    }   
    }
    $wpede_data_fetched = 1;}
    if(!$index)
    return $wpeden_wf_data;
    else  {
        if(isset($wpeden_wf_data[$index])&&is_array($wpeden_wf_data[$index]))   return $wpeden_wf_data[$index];
        return isset($wpeden_wf_data[$index])&&$wpeden_wf_data[$index]!=''?stripcslashes($wpeden_wf_data[$index]):$default;
    }
}

/**
* Generate theme option section heading
* 
* @param mixed $data
*/

function wpeden_subsection_heading($data){
    return "<h3>{$data}</h3>";
}

/**
* Ppredefines bootstrap skins , specially thanks for bootswatch.com
*
* @param mixed $params
*/

function wpeden_bootstrap_skins($params){
    $html = "<select  name='{$params['name']}' id='{$params['id']}'>";
    $html .= "<option value='bootstrap.min.css'".($params['selected']=='bootstrap.css'?'selected=selected':'').">Default (Blue)</option>";
    $html .= "<option value='red.bootstrap.min.css'".($params['selected']=='red.bootstrap.min.css'?'selected=selected':'').">Red</option>";
    $html .= "<option value='green.bootstrap.min.css'".($params['selected']=='green.bootstrap.min.css'?'selected=selected':'').">Green</option>";
    $html .= "<option value='gray.bootstrap.min.css' ".($params['selected']=='gray.bootstrap.min.css'?'selected=selected':'').">Grey</option>";
    $html .= "<option value='dark-red.bootstrap.min.css'".($params['selected']=='dark-red.bootstrap.min.css'?'selected=selected':'').">Dark Red</option>";
    $html .= "</select>";
    return $html;
}

/**
* Theme Layout Selector ( wide / boxed)
* 
* @param mixed $params
*/
function wpeden_layout_type($params){
    $html = "<select  name='{$params['name']}' id='{$params['id']}'>";
    $html .= "<option value='wide'".($params['selected']=='wide'?'selected=selected':'').">Wide</option>";
    $html .= "<option value='boxed'".($params['selected']=='boxed'?'selected=selected':'').">Boxed</option>";
    $html .= "</select>";
    return $html;
}

/**
* Site Logo
* 
* @param mixed $params
*/
function wpeden_site_logo($params){
    extract($params);
    
    $html = "<div class='input-append'><input class='{$id}' type='text' name='{$name}' id='{$id}_image' value='{$selected}' /><button rel='#{$id}_image' class='btn btn-media-upload' type='button'><i class='icon icon-folder-open'></i></button></div>";
    $html .="<div style='clear:both'></div>";
    return $html;
}

function wpeden_get_site_logo(){
    $logourl = wpeden_get_theme_opts('site_logo');
    if($logourl) echo "<img src='{$logourl}' title='".get_bloginfo('sitename')."' alt='".get_bloginfo('sitename')."' />";
    else echo get_bloginfo('sitename'); 
}

/**
* Generate option field for custom background
*
* @param mixed $params
* @return mixed $html
*/
function wpeden_custom_background($params){
    extract($params);
    
    $html = "<div class='input-append' style='margin-right: 10px;'><input class='{$id}' type='text' name='{$name}[image]' id='{$id}_image' value='{$selected['image']}' /><button rel='#{$id}_image' class='btn btn-media-upload' type='button'><i class='icon icon-folder-open'></i></button></div>";
    $html .= "<div class='input-append' style='margin-top:9px;margin-right:10px'><select class='{$id}' style='width:90px' id='{$id}_position_h' name='{$name}[position_h]'><option value='left'>Left</option><option value='center' ".($selected['position_h']=='center'?'selected=selected':'').">Center</option><option value='right' ".($selected['position_h']=='right'?'selected=selected':'').">Right</option></select></div>";
    $html .= "<div class='input-append' style='margin-top:9px;margin-right:10px'><select class='{$id}' style='width:90px' id='{$id}_position_v' name='{$name}[position_v]'><option value='top'>Top</option><option value='center' ".($selected['position_v']=='center'?'selected=selected':'').">Center</option><option value='bottom' ".($selected['position_v']=='bottom'?'selected=selected':'').">Bottom</option></select></div>";
    $html .= "<div class='input-append' style='margin-top:9px;margin-right:10px'><select class='{$id}' style='width:150px;' id='{$id}_repeat' name='{$name}[repeat]'><option value='no-repeat'>No Repeat</option><option value='repeat' ".($selected['repeat']=='repeat'?'selected=selected':'').">Reepar</option><option value='repeat-x' ".($selected['repeat']=='repeat-x'?'selected=selected':'').">Repeat Horizontally</option><option value='repeat-y' ".($selected['repeat']=='repeat-y'?'selected=selected':'').">Repeat Vertically</option></select></div>";
    $html .= "<div class='input-append' style='margin-top:9px;margin-right:10px'><select class='{$id}' style='width:90px' id='{$id}_attachment' name='{$name}[attachment]'><option value='scroll'>Scroll</option><option value='fixed' ".($selected['attachment']=='fixed'?'selected=selected':'').">Fixed</option></select></div>";
    $html .= "<div style='clear:both'></div><div id='hfp' title='Monitor Preview' style='width:300px;height:65px;margin:0 0px 5px 0;' class='span6'></div>";
    $bgs = scandir(WPEDEN_THEME_DIR.'/images/bg/');
    $html .= "<div id='prebgs' style='margin:0 0 10px 0px;float:left;width:290px;height:55px;overflow:auto;background:#555;padding:5px;' class='row-fluid'>";
    foreach($bgs as $file){
    if($file!='.'&&$file!='..') {   
            $url = WPEDEN_THEME_URL.'/images/bg/'.$file;
            $html .="<div data-url='$url' class='prebg' style='cursor:pointer;background:#fff url($url) center center;height:30px;width:38px;margin:2px;float:left;'></div>";
        }
    }
    $html .="<div style='clear:both'></div></div><div style='clear:both'></div>";
    $params['value'] = $selected['color'];
    $html .= '<input class="'.$id.' colorpicker" type="text" name="'.$name.'[color]" id="'.$id.'_color" size=10 placeholder="Color" value="'.$selected['color'].'" >';
    return $html;
}

/**
* Generate color picker
* 
* @param mixed $params
*/
function wpeden_color_picker($params){
    extract($params);
    $html = '<input class="'.$id.' colorpicker input-small span2" type="text" name="'.$name.'" id="'.$id.'" size=10 placeholder="Color" value="'.$value.'" />';
    return $html;
}


/**
* Generate list of pre-defined portfolio page layout
* 
* @param mixed $params
*/

function wpeden_portfolio_style($params){
    extract($params);
    $html = "<select name='{$name}' id='{$id}'>";
    $html .= "<option value='1' ".($selected=='1'?'selected=selected':'').">Style 1</option>";
    $html .= "<option value='2' ".($selected=='2'?'selected=selected':'').">Style 2</option>";
    $html .= "<option value='3' ".($selected=='3'?'selected=selected':'').">Style 3</option>";
    $html .= "<option value='4' ".($selected=='4'?'selected=selected':'').">Style 4</option>";
    $html .= "<option value='5' ".($selected=='5'?'selected=selected':'').">Style 5</option>";
    $html .= "<option value='6' ".($selected=='6'?'selected=selected':'').">Style 6</option>";
    $html .= "</select>";
    return $html;
}

function wpeden_portfolio_cols($params){
    extract($params);
    $html = "<select name='{$name}' id='{$id}'>";
    $html .= "<option value='6' ".($selected=='6'?'selected=selected':'').">2 Cols</option>";
    $html .= "<option value='4' ".($selected=='4'?'selected=selected':'').">3 Cols</option>";
    $html .= "<option value='3' ".($selected=='3'?'selected=selected':'').">4 Cols</option>";
    $html .= "</select>";
    return $html;
}

function wpeden_sidebar_width($params){
    extract($params);
    $html = "<select name='{$name}' id='{$id}'>";
    $html .= "<option value='3' ".($selected=='3'?'selected=selected':'').">Small (1/4)</option>";
    $html .= "<option value='4' ".($selected=='4'?'selected=selected':'').">Regular (1/3)</option>";
    $html .= "<option value='5' ".($selected=='5'?'selected=selected':'').">Large (5/12)</option>";
    $html .= "</select>";
    return $html;
}

function wpeden_post_sharing($params){
    extract($params);
    $sns = array('icon-facebook'=>'Facebook',
                 'icon-twitter'=>'Twitter',
                 'icon-fontello-delicious'=>'Delicious',
                 'icon-fontello-yahoo'=>'Yahoo',
                 'icon-fontello-quora'=>'Quora',
                 'icon-fontello-digg'=>'Digg',
                 'icon-fontello-reddit'=>'Reddit',
                 'icon-fontello-xing'=>'Xing',
                 'icon-fontello-flickr'=>'Flickr',
                 'icon-fontello-evernote'=>'Evernote',
                 'icon-fontello-stumbleupon'=>'Stumble Upon',
                 'icon-fontello-mixi'=>'Mixi',
                 'icon-pinterest'=>'Pinterest',
                 'icon-googleplus'=>'Google+',
                 'icon-linkedin'=>'LinkedIn',
                 'icon-fontello-instagram'=>'Instagram',
                 'icon-fontello-yelp'=>'Yelp',
                 'icon-fontello-myspace'=>'My Space',
                 'icon-fontello-skype'=>'Skype',
                 'icon-envelope'=>'Email'
    );
    $html = "<ul class='post-sharing'>";
    foreach($sns as $icon => $label){
        $checked = in_array($icon, $selected)?'checked=checked':''; // checked() is not usable here as 1 parameter is array
        $html .= "<li><label><input type='checkbox' name='{$name}[]' value='{$icon}' ".$checked." /> {$label}</label></li>";
    }
    $html .= "</ul>";
    return $html;
}

/**
* Generate list of pre-defined category/archive page layout
*
* @param mixed $params
*/

function wpeden_category_layout($params){
    extract($params);
    $html = "<select name='{$name}' id='{$id}'>";
    $html .= "<option value='archive-layout-1' ".selected($selected, 'archive-layout-1', false).">Archive Layout 1</option>";
    $html .= "<option value='archive-layout-2' ".selected($selected, 'archive-layout-2', false).">Archive Layout 2</option>";
    $html .= "<option value='archive-layout-3' ".selected($selected, 'archive-layout-3', false).">Archive Layout 3</option>";
    $html .= "<option value='archive-layout-4' ".selected($selected, 'archive-layout-4', false).">Archive Layout 4</option>";
    $html .= "</select>";
    return $html;
}

/**
* Generate list of pre-defined nav menu styles
*
* @param mixed $params
*/

function wpeden_header_styles($params){
    extract($params);
    $default = !isset($params['default'])?1:$params['default'];
    $html = "<select name='{$name}' id='{$id}'><option value='{$default}'>Default</option>";
    for($i=1;$i<10; $i++){
        if(file_exists(get_template_directory().'/header-'.$i.'.php'))
        $html .= "<option value='{$i}' ".selected($selected,$i,false).">Header Style {$i}</option>";
    }
    $html .= "</select>";
    return $html;
}

function wpeden_header_style(){
    global $wp_query;
    wp_reset_query();
    if(is_page()||is_single()) {
        $heavenly_post_meta = get_post_meta(get_the_ID(),'heavenly_post_meta', true);
        //echo "<!--  header|styles ".print_r($heavenly_post_meta,1)." -->";
        $style = $heavenly_post_meta['page_header'];
    }
    if(!$style)
    $style = wpeden_get_theme_opts('header_style',1);
    get_template_part("header",$style);
}

function wpeden_custom_css(){
    //return;
    $data = wpeden_get_theme_opts('page_title_bg');
    $ptcolor = wpeden_get_theme_opts('page_title_clr');
    $btnbgcolor = wpeden_get_theme_opts('button_bg');
    $btntxtcolor = wpeden_get_theme_opts('button_txt');
    $nvbg = wpeden_get_theme_opts('menu_bg');
    $ftbg = wpeden_get_theme_opts('footer_bg');
    $nvclr = wpeden_get_theme_opts('menu_txt');
    $ftclr = wpeden_get_theme_opts('footer_txt');
    $headertxtcolor = wpeden_get_theme_opts('header_txt_color');
    $panelbc = wpeden_get_theme_opts('panel_border_color');
    $panelfbg = wpeden_get_theme_opts('panel_footer_bg');
    $panelfc = wpeden_get_theme_opts('panel_footer_color');

    $fonts = get_option('wpeden_typo_settings',array());
    foreach($fonts as $font){
        $family[] = $font['family'];
    }

    //$family[] = "Bitter:400,700";
    //$family[] = "Roboto+Slab:400,300,700";
    $family[] = "Open+Sans:300,400,700,800";

    $family = array_filter(array_unique($family));
    
    $cssimport = "@import url(https://fonts.googleapis.com/css?family=".implode("|",$family).");";
    
    $nvbg = $nvbg!=''?"background: $nvbg !important;":''; /* rgba(".wpeden_hex2rgb($nvbg).",0.8) */
    $ftbg = $nvbg!=''?"background-color: $ftbg !important;":'';
    $nvcolor = $nvclr!=''?"color: $nvclr !important;":'';
    $ftcolor = $nvclr!=''?"color: $ftclr !important;":'';
    $bgcolor = $data['color']!=''?"background-color: $data[color];":'';
    $ptcolor = $ptcolor!=''?"color: $ptcolor !important;":'';
    $wpeden_custom_css_txt = wpeden_get_theme_opts('wpeden_custom_css_txt');
    $bgimage = $data['image']!=''?"background-image: url($data[image]); background-position: $data[position_h] $data[position_v]; background-repeat:  $data[repeat]; background-attachment: $data[attachment];":'';
    echo "<style>
    $cssimport
    $wpeden_custom_css_txt
    body,p{".wpeden_font_css('typo_regular')."}
    .brand, h1, h1 a, .entry-content h1{".wpeden_font_css('typo_h1')."}
    h2, h2 a, .entry-content h2{".wpeden_font_css('typo_h2')."}
    h3, h3 a, .entry-content h3{".wpeden_font_css('typo_h3')."}
    h4, h4 a, .entry-content h4{".wpeden_font_css('typo_h4')."}

    .archive-top {  $bgcolor $bgimage }
    .archive-top h1.entry-title,.archive-top, .archive-top *{  $ptcolor }
     #topnav-area .dropdown-menu { text-shadow:none !important; $nvbg $nvcolor }
     #topnav-area .dropdown-menu *, #topnav-area .dropdown-menu a { $nvcolor }
    .dropdown-menu .active > a,.current-menu-item>a,.dropdown-menu a:hover,.dropdown-menu li > a:hover { background: rgba(0,0,0,0.2) !important; $nvcolor  }
    .footer {  $ftbg $ftcolor } 
    .widget-footer h3,.footer *,.footer a{  $ftcolor } 
    #menu-top-menu>li>a {  $nvcolor } 


    *.entry-title,*.entry-title a {".wpeden_font_css('wpeden_post_title')."}
    .meta,.meta a {".wpeden_font_css('typo_post_meta')."}
    .entry-content,.entry-content p {".wpeden_font_css('wpeden_post_content')."}
    .box.widget, .box.widget li, .box.widget p, .box.widget a {".wpeden_font_css('wpeden_widget_content')."; line-height:2; }
    .box.widget h3 {".wpeden_font_css('wpeden_widget_title')."}
    ul#menu-top-menu a {".wpeden_font_css('typo_top_menu')."}
    ul#menu-top-menu .dropdown-menu > li > a {".wpeden_font_css('typo_dropdown_menu')."}
    ul#menu-top-menu .dropdown-menu > li.current-menu-item > a,
    ul#menu-top-menu .dropdown-menu > li:hover > a {color:#ffffff !important;}
    .home .panel,
    .home .thumbnail,
    .home .panel-footer .btn.btn-xs,
    .home .panel-footer{
        border-color: $panelbc !important;
    }
    .home .panel-footer .btn.btn-xs,
    .home .panel-footer{
        background-color: $panelfbg !important;
    }
    .home .panel-footer *,
    .home .panel-footer .btn.btn-xs,
    .home .panel-footer{         
        color: $panelfc !important;
    }
    </style>";
}


function wpeden_hex2rgb($hex) {
    $hex = preg_replace("/\#/", "", $hex);
    $color = array();
     
    if(strlen($hex) == 3) {
    $color['r'] = hexdec(substr($hex, 0, 1) . $r);
    $color['g'] = hexdec(substr($hex, 1, 1) . $g);
    $color['b'] = hexdec(substr($hex, 2, 1) . $b);
    }
    else if(strlen($hex) == 6) {
    $color['r'] = hexdec(substr($hex, 0, 2));
    $color['g'] = hexdec(substr($hex, 2, 2));
    $color['b'] = hexdec(substr($hex, 4, 2));
    }
     
    return $color['r'].",".$color['g'].",".$color['b'];
}

/**
* Dropdown list of post including any custom post type
* 
* @param mixed $params
*/
function wpeden_dropdown_posts($params) {
        extract($params);
        $post_type_object = get_post_type_object($post_type);   
        $label = $post_type_object->label;
        $posts = get_posts(array('post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));
        $html = '<select name="'. $name .'" id="'.$id.'">';        
        foreach ($posts as $post) {
            $html .= '<option value="'. $post->ID. '"'. selected($selected , $post->ID, false) . '>'. $post->post_title. '</option>';
        }
        $html .= '</select>';
        return $html;
}


function wpeden_dropdown_taxonomies($params){
    extract($params);
    $html .= "<select id='{$id}' name='{$name}'>";
    
    global $wpdb; 
    $txns = get_terms($params['taxonomy'], 'hide_empty=0');
    if(count($txns)>0){
      foreach($txns as $txn){

          $html .= '<option value="'.$txn->term_id.'" '.selected($selected, $txn->term_id, false ).'>'.$txn->name.'</option>';
          }
      } 
    $html .='</select>';
    return $html;
}

/**
*  Get font list  from fonts.ini 
*/            

function wpeden_get_fonts(){
    $ini_directory= get_template_directory().'/theme-data/';
    $font_array = parse_ini_file("$ini_directory/fonts.ini", true, INI_SCANNER_RAW);
    return $font_array;    
}


  /**
  * Generate drop download list of webfonts 
  * 
  * @param mixed $params
  * @return mixed
  */


function wpeden_font_dropdown($params = array()){
        extract($params);
        $fonts = wpeden_get_fonts();
        //$id = uniqid();
        $html = '<div class="row-fluid"><div class="pull-left" style="min-width:180px"><select style="max-width:180px" id="ff_'.$id.'" class="typography_font_family" name="'.$name.'[family]"><option value="">Default</option>';
        foreach($fonts as $key => $font){
            if($sel['family']==$key) { $cff = $font['family']; }
            $html .= '<option value="'.$key.'" '. selected($sel['family'], $key, false).'>'.$font['name'].'</option>';
        }
       $html .= '</select>&nbsp;</div>';
       
       $html .= '<div class="pull-left" style="min-width:120px"><select style="width:70px" id="fs_'.$id.'" class="typography_font_size" name="'.$name.'[size]"><option value="">Size</option>';
       for($i=9;$i<=200;$i++){

             $html .= '<option value="'.$i.'" '.selected($sel['size'], $i, false).'>'.$i.'</option>';
         }
       $html .= '</select>&nbsp;';
       
       $html .= '<select  id="fss_'.$id.'" style="width:70px" class="typography_font_size" name="'.$name.'[pxpt]">';
       $html .= '<option value="pt">pt</option><option value="px"'.($sel['pxpt']=='px'?"selected='selected'":'').'>px</option>';
       $html .= '</select></div>';
       
       $html .= '<div class="pull-left" style="max-width:110px;margin-right:10px"><select id="fw_'.$id.'"  style="width:80px" class="typography_font_weight" name="'.$name.'[weight]"><option value="">Weight</option>';
       for($i=300;$i<=700;$i+=100){
             $html .= '<option value="'.$i.'" '.selected($sel['weight'], $i, false).'>'.$i.'</option>';
         }
       $html .= '</select></div>'; 
      
       $selu = $sel['u']==1?'active':'';
       $seli = $sel['i']==1?'active':'';
       $fnts = $sel['i']==1?'font-style:italic':'';
       $gflink = $sel['family']?"https://fonts.googleapis.com/css?family={$sel['family']}":"";
       if(isset($cff))
       $ccss = "font-family:$cff;font-weight:{$sel['weight']};font-size:{$sel['size']}{$sel['pxpt']};$fnts";
       $html .= '<div class="pull-left" style="max-width:110px"><input size="12" style="width:90px !important"  type="text" class="colorpicker" id="'.$id.'_color" name="'.$name.'[color]" placeholder="Color" value="'.$sel['color'].'" /></div>';
       $html .= '<div class="span1" style="min-width:80px"><div class="btn-group" data-toggle="buttons-checkbox">
                 <button name="" type="button" class="btn '.$seli.' typocb" id="ib-'.$id.'">I</button>
                 <button type="button" class="btn '.$selu.' typocb" id="ub-'.$id.'">U</button>
                 </div><input id="ib-'.$id.'-x" type="hidden" value="'.$sel['i'].'" name="'.$name.'[i]" /><input id="ub-'.$id.'-x" type="hidden" value="'.$sel['u'].'" name="'.$name.'[u]" /></div></div>';
       $html .= '<div class="row-fluid"><div class="span12">
                 <link id="lnk_'.$id.'" href="'.$gflink.'" rel="stylesheet" type="text/css">
                 <div id="ptxt_'.$id.'" contenteditable="true" type="text" style="'.$ccss.'">Font Preview Text</div></div></div>
                 <script>
                        jQuery("#ff_'.$id.'").change(function(){ jQuery("#lnk_'.$id.'").attr("href","https://fonts.googleapis.com/css?family="+this.value); jQuery("#ptxt_'.$id.'").css("font-family",jQuery("#ff_'.$id.' option:selected").text()); });
                        jQuery("#fw_'.$id.'").change(function(){ jQuery("#ptxt_'.$id.'").css("font-weight",jQuery(this).val()); });
                        jQuery("#fs_'.$id.'").change(function(){ jQuery("#ptxt_'.$id.'").css("font-size",jQuery(this).val()+jQuery("#fss_'.$id.'").val()); });
                        jQuery("#fss_'.$id.'").change(function(){ jQuery("#ptxt_'.$id.'").css("font-size",jQuery("#fs_'.$id.'").val()+jQuery(this).val()); });
                        jQuery("#'.$id.'_color").change(function(){ jQuery("#ptxt_'.$id.'").css("color",jQuery(this).val()); });
                 </script>';
       return $html;
}

function wpeden_font_css($oname){
    $font = wpeden_get_theme_opts($oname);
    if(!$font) return;
    $fonts = wpeden_get_fonts();
    extract($font);
    $italic = $i==1?'font-style:italic;':'';
    $underline = $u==1?'text-decoration:underline;':'';
    $font_family = isset($fonts[$family]['family']) && $fonts[$family]['family']!=''?"font-family:{$fonts[$family]['family']} !important;":"";
    $font_size = $size!=''?"font-size:{$size}{$pxpt} !important;":"";
    $text_color = $color?"color:{$color} !important;":"";
    $font_weight = $weight?"font-weight:{$weight};":"";
    $css = "{$font_family}{$font_size}{$text_color}{$font_weight}{$italic}{$underline}" ;
    return $css;
}

function wpeden_select_slider($params = array()){
    extract($params);
    $slider = get_option('aois_sliders', array());
    $html = "<select name='{$name}[native]' id='{$id}' onchange=\"if(this.value=='external') jQuery('#sscode').slideDown(); else jQuery('#sscode').slideUp();\"><option value='none'>Hide Slider</option>";
    foreach ($slider as $sid=>$s) {
        $html .= "<option value='{$sid}' ".($selected['native']==$sid?'selected=selected':'').">{$s[name]}</option>";
    }
    $html = apply_filters("wpeden_slider", $html, $selected);
    $html .= "<option value='external' ".($selected['native']=='external'?'selected=selected':'').">External</option>";
    $html .= "</select><div id='sscode' style='".($selected['native']=='external'?'display:block':'display:none')."' >Enter Slider Short-code</br><input type='text'  name='{$name}[shortcode]' value='{$selected['shortcode']}' /></div>";
    return $html;
}

function wpeden_render_slider($page){
    $slider = wpeden_get_theme_opts($page);
    if($slider['native']=='external')
         echo do_shortcode($slider['shortcode']);
    else echo do_shortcode("[aios_render slider_id='{$slider['native']}']");
}

function wpeden_dropdown($params){
    $html = "<select name='{$params['name']}'  id='{$params['id']}' >";
    foreach($params['options'] as $value => $label){

        $html .= "<option value='{$value}' ".selected($params['selected'],$value,false).">$label</option>";
    }
    $html .= "</select>";
    return $html;
}

function wpeden_favicon(){
    ?>
    <link rel="icon" type="image/png" href="<?php echo wpeden_get_theme_opts('favicon'); ?>" />
    <?php
}



function wpeden_reset_theme_opts(){
    if ( isset($_REQUEST['_wtodr_nonce'])&&wp_verify_nonce( $_REQUEST['_wtodr_nonce'], 'wpeden-reset-data' )) {
        global  $settings_sections_ex;

        $settings_sections_ex = apply_filters("themeopt_section",$settings_sections_ex);

            foreach($settings_sections_ex as $s => $settings_sections) {
                foreach($settings_sections as $section_id => $section_name) {
                    delete_option($section_id);
                }
            }

        header('location: '.$_SERVER['HTTP_REFERER']);
        die();
    }
}

function wpeden_custom_css_option($params){
    ?>
<textarea style="max-width: 100%;min-width: 100%;height: 600px;font-family: 'courier new'" name="<?php echo $params['name']; ?>" id="<?php echo $params['id']; ?>"><?php echo $params['value']; ?></textarea>
    <script>jQuery('#wpeden_custom_css th').hide();</script>
<?php
}

/**
* List all available modules
* 
* @param mixed $params
*/

function wpeden_list_modules($params){

            extract($params);
            $module_dir = MX_THEME_DIR.'/modules/';
            $modules = scandir($module_dir);
            $active_modules = $wpeden_modules;   
            $html = '<style>#wpeden_module_settings .control-label{ display:none; } #wpeden_module_settings .controls{ margin-left:50px; }</style><div class=""><table class="table table-striped">';
            foreach($modules as $module){
                $moduledata=array();
                if($module!="." && $module!=".."){
                    if(is_dir($module_dir.$module)){
                        $moduledata = get_plugin_data($module_dir.$module."/".$module.".php");
                        $mod_status = $wpeden_modules[$module];
                        
                        $html .= ' 
     
                                <tr><td><b style="font-size:10pt;">'.$moduledata['Name'].'</b><br/><em  style="color:#888;">'.substr($moduledata['Description'],0,strpos($moduledata['Description'],"By")).'</em></td><td width=90>
                                <div class="btn-group pull-right wpeden-radio-btns" data-toggle="buttons-radio" data-toggle-id="#'.$module.'">
                                    <button type="button" rel="'.$module.'" class="btn btn-on btn-small btnx-'.$module.'">On</button>
                                    <button type="button" rel="'.$module.'" class="btn btn-off btn-small btnx-'.$module.'">Off</button>
                                </div>
                                <input type="hidden" name="'.$name.'['.$module.']" id="'.$module.'" value="'.$mod_status.'" />
                                </td></tr>
                        
                        ';
                    }
                       
                }
            }
    
    
    $html .= "</table></div>";
    $js = " <script>
            jQuery(function($) {
                  $('#wpeden_module_settings th').hide();
                  $('.wpeden-radio-btns').each(function(){
                    var group   = $(this);

                    var form    = group.parents('form').eq(0);
                    var id    = group.attr('data-toggle-id');
                    var hidden  = $(id);
                    $('button', group).each(function(){
                      var button = $(this);
                      button.live('click', function(){
                          if($(this).html() == 'On')  {
                            $(this).addClass('btn-success');
                            $('.btnx-'+$(this).attr('rel')+'.btn-off').removeClass('btn-danger');
                            }
                          else {
                            $('.btnx-'+$(this).attr('rel')).removeClass('btn-success');
                            $('.btnx-'+$(this).attr('rel')+'.btn-off').addClass('btn-danger');
                            }
                          hidden.val($(this).html());
                      });
                      if(button.html() == hidden.val()) {
                        button.addClass('active');
                      }
                      if(button.html() == 'On'&&hidden.val() == 'On') {
                        button.addClass('btn-success');
                        $('.btnx-'+$(this).attr('rel')).removeClass('btn-danger');
                      } else{
                        button.removeClass('btn-success');
                        $('.btn-off.active').addClass('btn-danger');
                      }
                    });
                  });
                  
                });  
                </script>
                ";
    
    return $html.$js;
}

/**
* Include options fields file
*/
require_once(dirname(__FILE__).'/option-fields.php');

/**
* Setup theme
* 
*/

function wpeden_setup_theme_options(){
    global $settings_fields, $settings_sections_ex;

    $settings_sections_ex = apply_filters("themeopt_section",$settings_sections_ex);
    $settings_fields = apply_filters("themeopt_fields",$settings_fields);

    foreach($settings_sections_ex as $settings_sections){
    foreach($settings_sections as $section_id=>$section_name){
        register_setting($section_id,$section_id,'wpeden_validate_optdata');
    }}

        foreach($settings_fields as $id=>$field){
        if($field['type']=='heading')
        add_settings_field($id, '', 'wpeden_setting_field', 'wpeden-themeopts', $field['section'], $field);
        else
        add_settings_field($id, $field['label'], 'wpeden_setting_field', 'wpeden-themeopts', $field['section'], $field);
    }

}

add_action('admin_init','wpeden_setup_theme_options');

/**
* Validate theme option data
* 
* @param mixed $data
*/
function wpeden_validate_optdata($data){    
    global $settings_fields;

    $settings_fields = apply_filters("themeopt_fields",$settings_fields);
     
    $error = array();     
    foreach($settings_fields as $id=>$field){                   
         if(!isset($data[$id])) continue;
         if(!isset($field['validate'])) $field['validate'] = 'str';
         switch($field['validate']){
             case 'int':
                $data[$id] = intval($data[$id]);
             break;
             case 'double':
                $data[$id] = doubleval($data[$id]);
             break;
             case 'str':
                $data[$id] = esc_attr(strval($data[$id]));
             break;
             case 'email':
                $data[$id] = is_email($data[$id])?$data[$id]:'';
                $error[$id] = __('Invalid Email Address','wpeden');
             break;
             case 'array':
                $data[$id] = $data[$id]; 
             break;
             case "callback":
                 $data[$id] = call_user_func($field['validate_callback'],$data[$id]);
             break;
         }
    }
    if($error) return $data['__error__'] = $error;
    //if($_POST) {print_r($data); echo $section_id; die();  }
    return $data;
}
    
/**
* Generate theme admin options
*     
*/
function wpeden_theme_options(){
global $settings_sections_ex, $section, $settings_icons;
$settings_sections_ex = apply_filters("themeopt_section",$settings_sections_ex);
    /*Theme Option Function*/
?>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css" />
    <div class="wrap wpeden-theme-options w3eden">
            <div class="container-fluid">
                <div class="row-fluid theader">
                    <div class="span12">

                        <h2 class="thm_heading"><img src="<?php echo get_template_directory_uri(); ?>/admin/images/logo-min.png" /></h2>
                    </div>

                </div>
                <div class="row-fluid">
                    <div class="span12">
<!--                        <div style="position: absolute">-->
<!--                            <ul class="nav nav-pills">-->
<!--                                <li class="active"><a href="#" class="ttip" rel=".g" title="Generel"><i class="icon icon-cogs icon-white"></i></a></li>-->
<!--                                <li class="active"><a href="#" class="ttip" rel=".h" title="Homepage"><i class="icon icon-home icon-white"></i></a></li>-->
<!--                                <li class="active"><a href="#" class="ttip" rel=".t" title="Typography"><i class="icon icon-font icon-white"></i></a></li>-->
<!--                                <li class="active"><a href="#" class="ttip" rel=".p" title="Page Templates"><i class="icon icon-file icon-white"></i></a></li>-->
<!--                                <li class="active"><a href="#" class="ttip" rel=".m" title="Modules"><i class="icon icon-magic icon-white"></i></a></li>-->
<!---->
<!--                            </ul>-->
<!--                        </div>-->

                        <div class=" tabbable tabs-left">
                        <!-- Theme Option Sections -->
                        <ul class="nav nav-tabs smn">
                            <?php foreach($settings_sections_ex as $id => $settings_sections){ ?>
                            <?php foreach($settings_sections as $section_id=>$section_name){ $settings_sections_tmp[$section_id] = $section_name; ?>
                            <li class="<?php echo $id; ?> <?php echo $section==$section_id?'active':''; ?>"><a href="#<?php echo $section_id; ?>" id="tab_<?php echo ++$tbc;?>" data-toggle='tab'><i class="icon-section icon <?php echo $settings_icons[$section_id]?$settings_icons[$section_id]:'icon-cog' ?>"></i> <?php echo $section_name; ?></a></li>
                            <?php } ?>
                            <?php } ?>
                        </ul>
                        <!-- Theme Option Sections Ends -->


                        <!-- Theme Option Fields for section # -->
                        <div class="tab-content">

                          <?php foreach($settings_sections_tmp as $section_id=>$section_name){ ?>
                            <div class="tab-pane <?php echo $section_id==$section?'active':''; ?>" id="<?php echo $section_id; ?>">

                            <form id="theme-admin-form-<?php echo $section_id; ?>"  class="form-horizontal wpeden-theme-opt-form" action="options.php" method="post" enctype="multipart/form-data">
                                <div class="xbtn pull-right">
                                <button type="submit" class="btn btn-submit btn-large" id="<?php echo $section_id; ?>-submit" name="form_submit"><i class="icon icon-save"></i> Save Changes</button>
                                </div>
                                <div class="xbtn pull-right" style="margin-right: 195px">
                                <button type="button" onclick="if(confirm('Are you sure?')) location.href='themes.php?_wtodr_nonce=<?php echo wp_create_nonce('wpeden-reset-data'); ?>';" class="btn btn-submit btn-large" id="<?php echo $section_id; ?>-submit" name="form_submit"><i class="icon icon-refresh"></i> Reset to Default</button>
                                </div>
                                <table class="table">
                                <?php 
                                  settings_fields( $section_id ); 
                                  do_settings_fields( 'wpeden-themeopts',$section_id );
                                ?></table>

                                         

                            <div class="clear"></div>

                            </form>
                            </div>
                           <?php } ?>

              
                        </div> 
                        <!-- Theme Option Fields for section # Ends -->
                        </div>
                    </div>
                <script>jQuery('.ttip').tooltip({placement:'right',animation:false, container:'ul.nav-pills'}); jQuery('.nav-pills a').click(function(e){e.preventDefault(); jQuery('.nav-tabs li').slideUp();jQuery(jQuery(this).attr('rel')).slideDown(); });</script>
</div>
</div>
 
</div>
<?php
        
}

function wpeden_admin_head(){
    ?>
<script>var wpeden_theme_url='<?php echo WPEDEN_THEME_URL; ?>';</script>
    <?php
}

function wpeden_setup_mmode($mode){
    if($mode==1) update_option("__mmode_time",time());
    return $mode;
}

function wpeden_check_maintenence_mode(){
    if(wpeden_get_theme_opts('site_offline')==1&&!current_user_can('edit_post')){
        //if(get_option('_mmode_time')=='') update_option('_mmode_time',time());
        $protocol = "HTTP/1.0";
        if ("HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] )
        $protocol = "HTTP/1.1";
        header( "$protocol 503 Service Unavailable", true, 503 );
        header( "Retry-After: 3600" );
        include(WPEDEN_THEME_DIR."/page-maintenance-mode.php");
        exit();
    }
}

add_action('admin_head', 'wpeden_admin_head');
add_action('admin_menu', 'wpeden_theme_opt_menu'); 
add_action('wp_head', 'wpeden_custom_css');
add_action('wp_head', 'wpeden_favicon');
add_action('init', 'wpeden_reset_theme_opts');
add_action('template_redirect', 'wpeden_check_maintenence_mode');
