<?php

$section = isset($_GET['section']) ? $_GET['section'] : 'wpeden_general_settings';

$settings_sections_ex = array(
    'g' => array('wpeden_general_settings' => 'General'),
    'h' => array('wpeden_homepage_settings' => 'Homepage Default'),
    't' => array(/*'wpeden_font_settings'         =>  'Web Fonts',*/
        'wpeden_typo_settings' => 'Typography'),  //will available from next update
    'p' => array('wpeden_contact_settings' => 'Contact Page',
        //'wpeden_portfolio_settings'    =>  'Portfolio Page'
    ),
    'm' => array(//'wpeden_module_settings'       =>  'Modules',
        'wpeden_custom_css' => 'Custom CSS'),
    's' => array(//'wpeden_social_settings'       =>  'Social Networks',
        //             'wpeden_mmode_settings'       =>  'Maintenence Mode'
    )
);

$settings_icons = array(
    'wpeden_general_settings' => 'icon-cogs',
    'wpeden_homepage_settings' => 'icon-home',
    'wpeden_homepage_ecommerce_settings' => 'icon-shopping-cart',
    'wpeden_homepage_personal_settings' => 'icon-user',
    'wpeden_homepage_magazine_settings' => 'icon-th',
    'wpeden_font_settings' => 'icon-font',  //will available from next update
    'wpeden_typo_settings' => 'icon-font',  //will available from next update
    'wpeden_contact_settings' => 'icon-phone',
    'wpeden_portfolio_settings' => 'icon-picture',
    'wpeden_module_settings' => 'icon-cog',
    'wpeden_custom_css' => 'icon-cog',
    'wpeden_social_settings' => 'icon-share'


);

$settings_sections = array(
    'wpeden_general_settings' => 'General',
    'wpeden_homepage_settings' => 'Homepage',
    'wpeden_homepage_ecommerce_settings' => 'Homepage Ecommerce',
    'wpeden_homepage_personal_settings' => 'Homepage Persoanl',
    'wpeden_typo_settings' => 'Typography',  //will available from next update
    'wpeden_contact_settings' => 'Contact Page',
    'wpeden_portfolio_settings' => 'Contact Page',
    'wpeden_module_settings' => 'Modules'
);
$settings_fields = array(

    /***  General Settings *******************/

    'site_logo' => array('id' => 'site_logo',
        'section' => 'wpeden_general_settings',
        'label' => 'Logo',
        'name' => 'wpeden_general_settings[site_logo]',
        'type' => 'callback',
        'value' => wpeden_get_theme_opts('site_logo'),
        'validate' => 'str',
        'dom_callback' => 'wpeden_site_logo',
        'dom_callback_params' => array('name' => 'wpeden_general_settings[site_logo]', 'id' => 'site_logo', 'selected' => wpeden_get_theme_opts('site_logo'))
    ),

    'favicon' => array('id' => 'favicon',
        'section' => 'wpeden_general_settings',
        'label' => 'Fav Icon',
        'name' => 'wpeden_general_settings[favicon]',
        'type' => 'callback',
        'value' => wpeden_get_theme_opts('favicon'),
        'validate' => 'str',
        'dom_callback' => 'wpeden_site_logo',
        'dom_callback_params' => array('name' => 'wpeden_general_settings[favicon]', 'id' => 'favicon', 'selected' => wpeden_get_theme_opts('favicon'))
    ),
    'color_scheme' => array('id' => 'color_scheme',
        'section' => 'wpeden_general_settings',
        'label' => 'Color Scheme',
        'name' => 'wpeden_general_settings[color_scheme]',
        'type' => 'text',
        'cssclass' => 'colorpicker span2',
        'value' => wpeden_get_theme_opts('color_scheme'),
        'validate' => 'str'
    ),
    'panel_border_color' => array('id' => 'panel_border_color',
        'section' => 'wpeden_general_settings',
        'label' => 'Homepage Panel<br><small>Border Color</small>',
        'name' => 'wpeden_general_settings[panel_border_color]',
        'type' => 'text',
        'cssclass' => 'colorpicker span2',
        'value' => wpeden_get_theme_opts('panel_border_color'),
        'validate' => 'str'
    ),
    'panel_footer_bg' => array('id' => 'panel_footer_bg',
        'section' => 'wpeden_general_settings',
        'label' => 'Homepage Panel<br><small>Footer Background</small>',
        'name' => 'wpeden_general_settings[panel_footer_bg]',
        'type' => 'text',
        'cssclass' => 'colorpicker span2',
        'value' => wpeden_get_theme_opts('panel_footer_bg'),
        'validate' => 'str'
    ),

    'panel_footer_color' => array('id' => 'panel_footer_color',
        'section' => 'wpeden_general_settings',
        'label' => 'Homepage Panel<br><small>Footer Color</small>',
        'name' => 'wpeden_general_settings[panel_footer_color]',
        'type' => 'text',
        'cssclass' => 'colorpicker span2',
        'value' => wpeden_get_theme_opts('panel_footer_color'),
        'validate' => 'str'
    ),


    'slider_category' => array('id' => 'slider_category',
        'section' => 'wpeden_homepage_settings',
        'label' => 'Slider Category',
        'name' => 'wpeden_homepage_settings[slider_category]',
        'type' => 'callback',
        'validate' => 'int',
        'dom_callback' => 'wpeden_dropdown_taxonomies',
        'dom_callback_params' => array('taxonomy' => 'wpdmcategory', 'name' => 'wpeden_homepage_settings[slider_category]', id => 'slider_category', 'selected' => wpeden_get_theme_opts('slider_category'))
    ),
    'slides' => array('id' => 'slides',
        'section' => 'wpeden_homepage_settings',
        'label' => 'Number of Slides',
        'name' => 'wpeden_homepage_settings[slides]',
        'type' => 'text',
        'value' => wpeden_get_theme_opts('slides', 5),
        'validate' => 'str'
    ),
    'blog_heading' => array('id' => 'blog_heading',
        'section' => 'wpeden_homepage_settings',
        'label' => 'HomePage - Opera Apps: Options ',
        'name' => 'blog_heading',
        'type' => 'heading'
    ),


//    'blog_section_desc' => array('id' => 'blog_section_desc',
//        'section'=>'wpeden_homepage_settings',
//        'label'=>'Tagline',
//        'name' => 'wpeden_homepage_settings[blog_section_desc]',
//        'type' => 'text',
//        'value' => wpeden_get_theme_opts('blog_section_desc','They are talking about...'),
//        'validate' => 'str'
//    ),


    'my_picture' => array('id' => 'my_picture',
        'section' => 'wpeden_homepage_personal_settings',
        'label' => 'Picture (<em>160x160 px</em>)',
        'name' => 'wpeden_homepage_personal_settings[my_picture]',
        'type' => 'callback',
        'value' => wpeden_get_theme_opts('my_picture'),
        'validate' => 'str',
        'dom_callback' => 'wpeden_site_logo',
        'dom_callback_params' => array('name' => 'wpeden_homepage_personal_settings[my_picture]', 'id' => 'my_picture', 'selected' => wpeden_get_theme_opts('my_picture'))
    ),
    'full_name' => array('id' => 'full_name',
        'section' => 'wpeden_homepage_personal_settings',
        'label' => 'Full Name',
        'name' => 'wpeden_homepage_personal_settings[full_name]',
        'type' => 'text',
        'value' => wpeden_get_theme_opts('full_name'),
        'validate' => 'str'
    ),

    'position_at_company' => array('id' => 'position_at_company',
        'section' => 'wpeden_homepage_personal_settings',
        'label' => 'Position & Company',
        'name' => 'wpeden_homepage_personal_settings[position_at_company]',
        'type' => 'text',
        'value' => wpeden_get_theme_opts('position_at_company'),
        'validate' => 'str'
    ),

    'about_me' => array('id' => 'about_me',
        'section' => 'wpeden_homepage_personal_settings',
        'label' => 'About Me',
        'name' => 'wpeden_homepage_personal_settings[about_me]',
        'type' => 'textarea',
        'value' => wpeden_get_theme_opts('about_me'),
        'validate' => 'str'
    ),
    'category_1' => array('id' => 'category_1',
        'section' => 'wpeden_homepage_settings',
        'label' => 'Category 1',
        'name' => 'wpeden_homepage_settings[category_1]',
        'type' => 'callback',
        'validate' => 'int',
        'dom_callback' => 'wpeden_dropdown_taxonomies',
        'dom_callback_params' => array('echo' => 0, 'taxonomy' => 'wpdmcategory', 'name' => 'wpeden_homepage_settings[category_1]', 'id' => 'category_1', 'selected' => wpeden_get_theme_opts('category_1'))
    ),

    'category_2' => array('id' => 'category_2',
        'section' => 'wpeden_homepage_settings',
        'label' => 'Category 2',
        'name' => 'wpeden_homepage_settings[category_2]',
        'type' => 'callback',
        'validate' => 'int',
        'dom_callback' => 'wpeden_dropdown_taxonomies',
        'dom_callback_params' => array('echo' => 0, 'taxonomy' => 'wpdmcategory', 'name' => 'wpeden_homepage_settings[category_2]', 'id' => 'category_2', 'selected' => wpeden_get_theme_opts('category_2'))
    ),
    'category_3' => array('id' => 'category_3',
        'section' => 'wpeden_homepage_settings',
        'label' => 'Category 3',
        'name' => 'wpeden_homepage_settings[category_3]',
        'type' => 'callback',
        'validate' => 'int',
        'dom_callback' => 'wpeden_dropdown_taxonomies',
        'dom_callback_params' => array('echo' => 0, 'taxonomy' => 'wpdmcategory', 'name' => 'wpeden_homepage_settings[category_3]', 'id' => 'category_3', 'selected' => wpeden_get_theme_opts('category_3'))
    ),

    'category_4' => array('id' => 'category_4',
        'section' => 'wpeden_homepage_magazine_settings',
        'label' => 'Category 4',
        'name' => 'wpeden_homepage_magazine_settings[category_4]',
        'type' => 'callback',
        'validate' => 'int',
        'dom_callback' => 'wp_dropdown_categories',
        'dom_callback_params' => array('echo' => 0, 'name' => 'wpeden_homepage_magazine_settings[category_4]', 'id' => 'category_4', 'selected' => wpeden_get_theme_opts('category_4'))
    ),


    'wpmp_category_1' => array('id' => 'wpmp_category_1',
        'section' => 'wpeden_homepage_ecommerce_settings',
        'label' => 'Product Category 1',
        'name' => 'wpeden_homepage_ecommerce_settings[wpmp_category_1]',
        'type' => 'callback',
        'validate' => 'int',
        'dom_callback' => 'wpeden_dropdown_taxonomies',
        'dom_callback_params' => array('echo' => 0, 'name' => 'wpeden_homepage_ecommerce_settings[wpmp_category_1]', 'id' => 'wpmp_category_1', 'selected' => wpeden_get_theme_opts('wpmp_category_1'))
    ),


    'wpmp_category_2' => array('id' => 'wpmp_category_2',
        'section' => 'wpeden_homepage_ecommerce_settings',
        'label' => 'Product Category 2',
        'name' => 'wpeden_homepage_ecommerce_settings[wpmp_category_2]',
        'type' => 'callback',
        'validate' => 'int',
        'dom_callback' => 'wpeden_dropdown_taxonomies',
        'dom_callback_params' => array('echo' => 0, 'name' => 'wpeden_homepage_ecommerce_settings[wpmp_category_2]', 'id' => 'wpmp_category_2', 'selected' => wpeden_get_theme_opts('wpmp_category_2'))
    ),


    'wpmp_category_3' => array('id' => 'wpmp_category_3',
        'section' => 'wpeden_homepage_ecommerce_settings',
        'label' => 'Product Category 3',
        'name' => 'wpeden_homepage_ecommerce_settings[wpmp_category_3]',
        'type' => 'callback',
        'validate' => 'int',
        'dom_callback' => 'wpeden_dropdown_taxonomies',
        'dom_callback_params' => array('echo' => 0, 'name' => 'wpeden_homepage_ecommerce_settings[wpmp_category_3]', 'id' => 'wpmp_category_3', 'selected' => wpeden_get_theme_opts('wpmp_category_3'))
    ),


    'wpmp_category_4' => array('id' => 'wpmp_category_4',
        'section' => 'wpeden_homepage_ecommerce_settings',
        'label' => 'Product Category 4',
        'name' => 'wpeden_homepage_ecommerce_settings[wpmp_category_4]',
        'type' => 'callback',
        'validate' => 'int',
        'dom_callback' => 'wpeden_dropdown_taxonomies',
        'dom_callback_params' => array('echo' => 0, 'name' => 'wpeden_homepage_ecommerce_settings[wpmp_category_4]', 'id' => 'wpmp_category_4', 'selected' => wpeden_get_theme_opts('wpmp_category_4'))
    ),


//            'featured_page_heading' => array('id' => 'featured_page_heading',
//                                'section'=>'wpeden_homepage_settings',
//                                'label'=>'Archive Pages',
//                                'name' => 'featured_page_heading',
//                                'type' => 'heading'
//                                ),
//            'blog_page' => array('id' => 'blog_page',
//                                'section'=>'wpeden_homepage_settings',
//                                'label'=>'Blog Page',
//                                'name' => 'wpeden_homepage_settings[blog_page]',
//                                'type' => 'callback',
//                                'validate' => 'int',
//                                'dom_callback'=> 'wp_dropdown_pages',
//                                'dom_callback_params' => 'echo=0&name=wpeden_homepage_settings[blog_page]&id=blog_page&selected='.wpeden_get_theme_opts('blog_page')
//                                ),
//            'portfolio_page' => array('id' => 'portfolio_page',
//                                'section'=>'wpeden_homepage_settings',
//                                'label'=>'Portfolio Page',
//                                'name' => 'wpeden_homepage_settings[portfolio_page]',
//                                'type' => 'callback',
//                                'validate' => 'int',
//                                'dom_callback'=> 'wp_dropdown_pages',
//                                'dom_callback_params' => 'echo=0&name=wpeden_homepage_settings[portfolio_page]&id=portfolio_page&selected='.wpeden_get_theme_opts('portfolio_page')
//                                ),
//            'client_page' => array('id' => 'client_page',
//                                'section'=>'wpeden_homepage_settings',
//                                'label'=>'Clients Page',
//                                'name' => 'wpeden_homepage_settings[client_page]',
//                                'type' => 'callback',
//                                'validate' => 'int',
//                                'dom_callback'=> 'wp_dropdown_pages',
//                                'dom_callback_params' => 'echo=0&name=wpeden_homepage_settings[client_page]&id=client_page&selected='.wpeden_get_theme_opts('client_page')
//                                ),
//            'service_page' => array('id' => 'service_page',
//                                'section'=>'wpeden_homepage_settings',
//                                'label'=>'Service Page',
//                                'name' => 'wpeden_homepage_settings[service_page]',
//                                'type' => 'callback',
//                                'validate' => 'int',
//                                'dom_callback'=> 'wp_dropdown_pages',
//                                'dom_callback_params' => 'echo=0&name=wpeden_homepage_settings[service_page]&id=service_page&selected='.wpeden_get_theme_opts('service_page')
//                                ),
//            'testimonial_page' => array('id' => 'testimonial_page',
//                                'section'=>'wpeden_homepage_settings',
//                                'label'=>'Client Review Page',
//                                'name' => 'wpeden_homepage_settings[testimonial_page]',
//                                'type' => 'callback',
//                                'validate' => 'int',
//                                'dom_callback'=> 'wp_dropdown_pages',
//                                'dom_callback_params' => 'echo=0&name=wpeden_homepage_settings[testimonial_page]&id=testimonial_page&selected='.wpeden_get_theme_opts('testimonial_page')
//                                ),

    /** portfolio page settings */
    'portfolio_style' => array('id' => 'portfolio_style',
        'section' => 'wpeden_portfolio_settings',
        'label' => 'Portfolio Page Style',
        'name' => 'wpeden_portfolio_settings[portfolio_style]',
        'type' => 'callback',
        'validate' => 'int',
        'dom_callback' => 'wpeden_portfolio_style',
        'dom_callback_params' => array('name' => 'wpeden_portfolio_settings[portfolio_style]', 'id' => 'portfolio_style', 'selected' => wpeden_get_theme_opts('portfolio_style', 3))
    ),
    'portfolio_cols' => array('id' => 'portfolio_cols',
        'section' => 'wpeden_portfolio_settings',
        'label' => 'Portfolio Page Cols',
        'name' => 'wpeden_portfolio_settings[portfolio_cols]',
        'type' => 'callback',
        'validate' => 'int',
        'dom_callback' => 'wpeden_portfolio_cols',
        'dom_callback_params' => array('name' => 'wpeden_portfolio_settings[portfolio_cols]', 'id' => 'portfolio_cols', 'selected' => wpeden_get_theme_opts('portfolio_cols', 4))
    ),

    /***  Module Settings *******************/
    'map_address' => array('id' => 'map_address',
        'section' => 'wpeden_contact_settings',
        'label' => 'Google Map Address',
        'name' => 'wpeden_contact_settings[map_address]',
        'type' => 'text',
        'validate' => 'str',
        'value' => wpeden_get_theme_opts('map_address')
    ),
    'contact_address' => array('id' => 'contact_address',
        'section' => 'wpeden_contact_settings',
        'label' => 'Contact Address',
        'name' => 'wpeden_contact_settings[contact_address]',
        'type' => 'textarea',
        'validate' => 'str',
        'value' => wpeden_get_theme_opts('contact_address')
    ),
    'contact_phone' => array('id' => 'contact_phone',
        'section' => 'wpeden_contact_settings',
        'label' => 'Phone',
        'name' => 'wpeden_contact_settings[contact_phone]',
        'type' => 'text',
        'validate' => 'str',
        'value' => wpeden_get_theme_opts('contact_phone')
    ),
    'contact_email' => array('id' => 'contact_email',
        'section' => 'wpeden_contact_settings',
        'label' => 'Email',
        'name' => 'wpeden_contact_settings[contact_email]',
        'type' => 'text',
        'validate' => 'str',
        'value' => wpeden_get_theme_opts('contact_email')
    ),
    'contact_thanks_msg' => array('id' => 'contact_thanks_msg',
        'section' => 'wpeden_contact_settings',
        'label' => 'Thank you message',
        'description' => 'Thank you message',
        'name' => 'wpeden_contact_settings[contact_thanks_msg]',
        'type' => 'textarea',
        'validate' => 'str',
        'value' => wpeden_get_theme_opts('contact_thanks_msg')
    ),


    /***  Custom CSS Settings *******************/

    'wpeden_custom_css_txt' => array('id' => 'wpeden_custom_css_opt',
        'section' => 'wpeden_custom_css',
        'label' => 'Custom CSS',
        'name' => 'wpeden_custom_css[wpeden_custom_css_txt]',
        'type' => 'callback',
        'validate' => 'array',
        'dom_callback' => 'wpeden_custom_css_option',
        'dom_callback_params' => array('name' => 'wpeden_custom_css[wpeden_custom_css_txt]', 'id' => 'wpeden_custom_css_opt', 'value' => wpeden_get_theme_opts('wpeden_custom_css_txt'))
    ),

    /***  Module Settings *******************/

    'wpeden_modules' => array('id' => 'wpeden_modules',
        'section' => 'wpeden_module_settings',
        'label' => 'Available Modules',
        'name' => 'wpeden_module_settings[wpeden_modules]',
        'type' => 'callback',
        'validate' => 'array',
        'dom_callback' => 'wpeden_list_modules',
        'dom_callback_params' => array('name' => 'wpeden_module_settings[wpeden_modules]', 'id' => 'wpeden_modules', 'wpeden_modules' => wpeden_get_theme_opts('wpeden_modules'))
    ),

    /***  Typography Settings *******************/

    'typo_generic' => array('id' => 'typo_generic',
        'section' => 'wpeden_typo_settings',
        'label' => 'Generic Fonts',
        'name' => 'typo_generic',
        'type' => 'heading'
    ),
    'typo_h1' => array('id' => 'typo_h1',
        'section' => 'wpeden_typo_settings',
        'label' => 'H1 Font',
        'name' => 'wpeden_typo_settings[typo_h1]',
        'type' => 'callback',
        'validate' => 'array',
        'dom_callback' => 'wpeden_font_dropdown',
        'dom_callback_params' => array('name' => 'wpeden_typo_settings[typo_h1]', 'id' => 'typo_h1', 'sel' => wpeden_get_theme_opts('typo_h1'))
    ),

    'typo_h2' => array('id' => 'typo_h2',
        'section' => 'wpeden_typo_settings',
        'label' => 'H2 Font',
        'name' => 'wpeden_typo_settings[typo_h2]',
        'type' => 'callback',
        'validate' => 'array',
        'dom_callback' => 'wpeden_font_dropdown',
        'dom_callback_params' => array('name' => 'wpeden_typo_settings[typo_h2]', 'id' => 'typo_h2', 'sel' => wpeden_get_theme_opts('typo_h2'))
    ),

    'typo_h3' => array('id' => 'typo_h3',
        'section' => 'wpeden_typo_settings',
        'label' => 'H3 Font',
        'name' => 'wpeden_typo_settings[typo_h3]',
        'type' => 'callback',
        'validate' => 'array',
        'dom_callback' => 'wpeden_font_dropdown',
        'dom_callback_params' => array('name' => 'wpeden_typo_settings[typo_h3]', 'id' => 'typo_h3', 'sel' => wpeden_get_theme_opts('typo_h3'))
    ),

    'typo_h4' => array('id' => 'typo_h4',
        'section' => 'wpeden_typo_settings',
        'label' => 'H4 Font',
        'name' => 'wpeden_typo_settings[typo_h4]',
        'type' => 'callback',
        'validate' => 'array',
        'dom_callback' => 'wpeden_font_dropdown',
        'dom_callback_params' => array('name' => 'wpeden_typo_settings[typo_h4]', 'id' => 'typo_h4', 'sel' => wpeden_get_theme_opts('typo_h4'))
    ),

    'typo_regular' => array('id' => 'typo_regular',
        'section' => 'wpeden_typo_settings',
        'label' => 'Regular Text Font',
        'name' => 'wpeden_typo_settings[typo_regular]',
        'type' => 'callback',
        'validate' => 'array',
        'dom_callback' => 'wpeden_font_dropdown',
        'dom_callback_params' => array('name' => 'wpeden_typo_settings[typo_regular]', 'id' => 'typo_regular', 'sel' => wpeden_get_theme_opts('typo_regular'))
    ),

    'type_post' => array('id' => 'type_post',
        'section' => 'wpeden_typo_settings',
        'label' => 'Post Fonts',
        'name' => 'wpeden_typo_settings',
        'type' => 'heading'
    ),

    'typo_post_title' => array('id' => 'typo_post_title',
        'section' => 'wpeden_typo_settings',
        'label' => 'Post Title',
        'name' => 'wpeden_typo_settings[wpeden_post_title]',
        'type' => 'callback',
        'validate' => 'array',
        'dom_callback' => 'wpeden_font_dropdown',
        'dom_callback_params' => array('name' => 'wpeden_typo_settings[wpeden_post_title]', 'id' => 'typo_post_title', 'sel' => wpeden_get_theme_opts('wpeden_post_title'))
    ),
    'typo_post_content' => array('id' => 'typo_post_content',
        'section' => 'wpeden_typo_settings',
        'label' => 'Post Content',
        'name' => 'wpeden_typo_settings[wpeden_post_content]',
        'type' => 'callback',
        'validate' => 'array',
        'dom_callback' => 'wpeden_font_dropdown',
        'dom_callback_params' => array('name' => 'wpeden_typo_settings[wpeden_post_content]', 'id' => 'wpeden_post_content', 'sel' => wpeden_get_theme_opts('wpeden_post_content'))
    ),

    'typo_post_meta' => array('id' => 'typo_post_meta',
        'section' => 'wpeden_typo_settings',
        'label' => 'Post Meta',
        'name' => 'wpeden_typo_settings[typo_post_meta]',
        'type' => 'callback',
        'validate' => 'array',
        'dom_callback' => 'wpeden_font_dropdown',
        'dom_callback_params' => array('name' => 'wpeden_typo_settings[typo_post_meta]', 'id' => 'typo_post_meta', 'sel' => wpeden_get_theme_opts('typo_post_meta'))
    ),
    'type_widget' => array('id' => 'type_widget',
        'section' => 'wpeden_typo_settings',
        'label' => 'Widget Fonts',
        'name' => 'wpeden_typo_settings',
        'type' => 'heading'
    ),


    'typo_widget_title' => array('id' => 'typo_widget_title',
        'section' => 'wpeden_typo_settings',
        'label' => 'Widget Title',
        'name' => 'wpeden_typo_settings[wpeden_widget_title]',
        'type' => 'callback',
        'validate' => 'array',
        'dom_callback' => 'wpeden_font_dropdown',
        'dom_callback_params' => array('name' => 'wpeden_typo_settings[wpeden_widget_title]', 'id' => 'wpeden_widget_title', 'sel' => wpeden_get_theme_opts('wpeden_widget_title'))
    ),
    'typo_widget_content' => array('id' => 'typo_widget_content',
        'section' => 'wpeden_typo_settings',
        'label' => 'Widget Content',
        'name' => 'wpeden_typo_settings[wpeden_widget_content]',
        'type' => 'callback',
        'validate' => 'array',
        'dom_callback' => 'wpeden_font_dropdown',
        'dom_callback_params' => array('name' => 'wpeden_typo_settings[wpeden_widget_content]', 'id' => 'wpeden_widget_content', 'sel' => wpeden_get_theme_opts('wpeden_widget_content'))
    ),
    'type_menu' => array('id' => 'type_menu',
        'section' => 'wpeden_typo_settings',
        'label' => 'Menu Fonts',
        'name' => 'wpeden_typo_settings',
        'type' => 'heading'
    ),

    'typo_top_menu' => array('id' => 'typo_top_menu',
        'section' => 'wpeden_typo_settings',
        'label' => 'Top Level Menu',
        'name' => 'wpeden_typo_settings[typo_top_menu]',
        'type' => 'callback',
        'validate' => 'array',
        'dom_callback' => 'wpeden_font_dropdown',
        'dom_callback_params' => array('name' => 'wpeden_typo_settings[typo_top_menu]', 'id' => 'typo_top_menu', 'sel' => wpeden_get_theme_opts('typo_top_menu'))
    ),

    'typo_dropdown_menu' => array('id' => 'typo_dropdown_menu',
        'section' => 'wpeden_typo_settings',
        'label' => 'DropDown Menu',
        'name' => 'wpeden_typo_settings[typo_dropdown_menu]',
        'type' => 'callback',
        'validate' => 'array',
        'dom_callback' => 'wpeden_font_dropdown',
        'dom_callback_params' => array('name' => 'wpeden_typo_settings[typo_dropdown_menu]', 'id' => 'typo_dropdown_menu', 'sel' => wpeden_get_theme_opts('typo_dropdown_menu'))
    ),

    /***  Social Settings *****************/

    'facebook_profile_url' => array('id' => 'facebook_profile_url',
        'section' => 'wpeden_social_settings',
        'label' => 'FaceBook Profile URL',
        'name' => 'wpeden_social_settings[facebook_profile_url]',
        'type' => 'text',
        'validate' => 'str',
        'value' => wpeden_get_theme_opts('facebook_profile_url')
    ),

    'twitter_profile_url' => array('id' => 'twitter_profile_url',
        'section' => 'wpeden_social_settings',
        'label' => 'Twitter Profile URL',
        'name' => 'wpeden_social_settings[twitter_profile_url]',
        'type' => 'text',
        'validate' => 'str',
        'value' => wpeden_get_theme_opts('twitter_profile_url')
    ),

    'googleplus_profile_url' => array('id' => 'googleplus_profile_url',
        'section' => 'wpeden_social_settings',
        'label' => 'Google+ Profile URL',
        'name' => 'wpeden_social_settings[googleplus_profile_url]',
        'type' => 'text',
        'validate' => 'str',
        'value' => wpeden_get_theme_opts('googleplus_profile_url')
    ),

    'pinterest_profile_url' => array('id' => 'pinterest_profile_url',
        'section' => 'wpeden_social_settings',
        'label' => 'Pinterest Profile URL',
        'name' => 'wpeden_social_settings[pinterest_profile_url]',
        'type' => 'text',
        'validate' => 'str',
        'value' => wpeden_get_theme_opts('pinterest_profile_url')
    ),

    'linkedin_profile_url' => array('id' => 'linkedin_profile_url',
        'section' => 'wpeden_social_settings',
        'label' => 'LinkedIn Profile URL',
        'name' => 'wpeden_social_settings[linkedin_profile_url]',
        'type' => 'text',
        'validate' => 'str',
        'value' => wpeden_get_theme_opts('linkedin_profile_url')
    ),

    'post_sharing' => array('id' => 'post_sharing',
        'section' => 'wpeden_social_settings',
        'label' => 'Post Sharing',
        'name' => 'wpeden_social_settings[post_sharing]',
        'type' => 'callback',
        'validate' => 'array',
        'value' => wpeden_get_theme_opts('post_sharing'),
        'dom_callback' => 'wpeden_post_sharing',
        'dom_callback_params' => array('name' => 'wpeden_social_settings[post_sharing]', 'id' => 'post_sharing', 'selected' => wpeden_get_theme_opts('post_sharing'))

    ),
    'site_offline' => array('id' => 'site_offline',
        'section' => 'wpeden_mmode_settings',
        'label' => 'Maintenance Mode',
        'name' => 'wpeden_mmode_settings[site_offline]',
        'type' => 'callback',
        'validate' => 'callback',
        'validate_callback' => 'wpeden_setup_mmode',
        'value' => wpeden_get_theme_opts('site_offline'),
        'dom_callback' => 'wpeden_dropdown',
        'dom_callback_params' => array('options' => array('0' => 'Inactive', '1' => 'Active'), 'name' => 'wpeden_mmode_settings[site_offline]', 'id' => 'site_offline', 'selected' => wpeden_get_theme_opts('site_offline'))

    ),
    'offline_page' => array('id' => 'offline_page',
        'section' => 'wpeden_mmode_settings',
        'label' => 'Offline Page',
        'name' => 'wpeden_mmode_settings[offline_page]',
        'type' => 'callback',
        'validate' => 'int',
        'dom_callback' => 'wp_dropdown_pages',
        'dom_callback_params' => 'echo=0&name=wpeden_mmode_settings[offline_page]&id=offline_page&selected=' . wpeden_get_theme_opts('offline_page')
    ),

    'maintenenance_time' => array('id' => 'maintenenance_time',
        'section' => 'wpeden_mmode_settings',
        'label' => 'Maintenance Time',
        'placeholder' => 'in minutes',
        'class' => 'input-small',
        'description' => 'minutes',
        'name' => 'wpeden_mmode_settings[maintenenance_time]',
        'type' => 'text',
        'validate' => 'str',
        'value' => wpeden_get_theme_opts('maintenenance_time')
    ),


);


if (file_exists(get_stylesheet_directory() . '/option-fields.php'))
    require_once(get_stylesheet_directory() . '/option-fields.php');