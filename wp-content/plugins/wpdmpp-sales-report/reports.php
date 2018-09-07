<?php

/*
Plugin Name: WPDMPP - Sales Reports
Plugin URI: http://www.wpdownloadmanager.com/
Description: Check, track & compare your digital store sales
Author: Shaon
Version: 1.0.0
Author URI: http://www.wpdownloadmanager.com/
 */

if (!defined('WPINC')) {
    exit;
}

if (!class_exists('WpmpProductReports')):

    class WpmpProductReports {

        private static $instance;
        private $dir, $url;

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self;
                self::$instance->dir = dirname(__FILE__);
                self::$instance->url = WP_PLUGIN_URL . '/' . basename(self::$instance->dir);
                self::$instance->actions();
            }
            return self::$instance;
        }

        private function actions() {
            register_activation_hook(__FILE__, array($this, 'activate'));
            register_deactivation_hook(__FILE__, array($this, 'deactivate'));

            if (is_admin()) {
                add_action('admin_menu', array($this, 'adminMenu'));
                
            }

            if (!is_admin()) {
                
            }

            if ( defined('DOING_AJAX') ) {
                if(!class_exists('WpmpR_Ajax')){
                    require_once 'classes/ajax.php';
                }
                WpmpR_Ajax::getInstance();
            }
        }

        public static function getDir() {
            return self::$instance->dir;
        }

        public static function getUrl() {
            return self::$instance->url;
        }

        public function activate() {
            
        }

        public function deactivate() {
            
        }

        public function adminMenu() {
            $slug = add_submenu_page('edit.php?post_type=wpdmpro', __('Sales Report', 'download-manager'), __('Sales Report'), 'manage_options', 'product-reports', array($this, 'productReports'));
            add_action("admin_print_styles-$slug",array($this,"adminStyles"));
            add_action('admin_print_scripts-' . $slug,array($this,'adminScripts'));
        }

        public function productReports() {
            if(!class_exists('WpmpR_AdminPanel')) {
                require_once $this->dir . '/classes/adminmenu.php';
            }
            WpmpR_AdminPanel::getInstance();
        }
        
        public function adminScripts(){
            wp_enqueue_script('bootstrap', $this->url . '/assets/bootstrap-3.1.1/js/bootstrap.min.js',array('jquery'));
            wp_enqueue_script('jqPlot',  $this->url . '/assets/jqplot/jquery.jqplot.min.js',array('jquery'));
            wp_enqueue_script('jqPlot_barRenderer',  $this->url . '/assets/jqplot/plugins/jqplot.barRenderer.min.js',array('jquery','jqPlot'));
            wp_enqueue_script('jqPlot_highlighter',  $this->url . '/assets/jqplot/plugins/jqplot.highlighter.min.js',array('jquery','jqPlot'));
            wp_enqueue_script('jqPlot_cursor',  $this->url . '/assets/jqplot/plugins/jqplot.cursor.min.js',array('jquery','jqPlot'));
            wp_enqueue_script('jqPlot_pointLabels',  $this->url . '/assets/jqplot/plugins/jqplot.pointLabels.min.js',array('jquery','jqPlot'));
            wp_enqueue_script('jqPlot_categoryAxisRenderer',  $this->url . '/assets/jqplot/plugins/jqplot.categoryAxisRenderer.min.js',array('jquery','jqPlot'));
            wp_enqueue_script('jqPlot_pieRenderer',  $this->url . '/assets/jqplot/plugins/jqplot.pieRenderer.min.js',array('jquery','jqPlot'));
            wp_enqueue_script('auto-suggest',  $this->url . '/assets/jquery.fcbkcomplete.js',array('jquery','jqPlot'));
            //wp_enqueue_script('jqPlot_barRenderer',  $this->url . '/assets/jqplot/plugins/jqplot.barRenderer.min.js',array('jquery','jqPlot'));
            //wp_enqueue_script('jqPlot_barRenderer',  $this->url . '/assets/jqplot/plugins/jqplot.barRenderer.min.js',array('jquery','jqPlot'));
            //wp_enqueue_script('jqPlot_barRenderer',  $this->url . '/assets/jqplot/plugins/jqplot.barRenderer.min.js',array('jquery','jqPlot'));
            //wp_enqueue_script('jqPlot_barRenderer',  $this->url . '/assets/jqplot/plugins/jqplot.barRenderer.min.js',array('jquery','jqPlot'));
            
        }
        
        public function adminStyles(){
            wp_enqueue_style('bootstrap', $this->url . '/assets/bootstrap-3.1.1/css/bootstrap.css');
            wp_enqueue_style('wpmp_report_admin_css', $this->url . '/assets/adminStyle.css');
            wp_enqueue_style('jqPlot',  $this->url . '/assets/jqplot/jquery.jqplot.min.css');
            wp_enqueue_style('auto-suggest',  $this->url . '/assets/auto_suggest.css');
            wp_enqueue_style('jquery-ui',  '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
            
        }

    }

    WpmpProductReports::getInstance();
    

    
    
    
endif;

