<?php
/*
Plugin Name: WPDM - File Manager
Plugin URI: http://www.wpdownloadmanager.com/
Description: File Manager Plugin for WordPress Download Manager
Author: Shaon
Version: 1.2.1
Author URI: http://www.wpdownloadmanager.com/
*/

if(!defined('TD_FILE')){
    define('TD_FILE','');
}


if(!class_exists('WpdmFileManager')):
    class WpdmFileManager{
        private static $instance;
        private $dir,$url;
        private $mime_type;
        
        public static function getInstance(){
            if(self::$instance === null){
                self::$instance = new self;
                self::$instance->dir = dirname(__FILE__);
                self::$instance->url = WP_PLUGIN_URL . '/' . basename(self::$instance->dir);
                self::$instance->actions();
                //print_r($_SESSION);
            }
            return self::$instance;
        }
        
        private function actions(){
            register_activation_hook(__FILE__, array($this,'activate'));
            register_deactivation_hook(__FILE__, array($this,'deactivate'));
            
            add_action('init',array($this,'register_session'),1);
            add_action('wp_logout', array($this,'myEndSession'));
            add_action('wp_login', array($this,'myEndSession'));
            add_action('wp_ajax_wpdm_fm_file_upload', array($this,'uploadFile'));
            add_action('wp_ajax_wpdm_mkdir', array($this,'mkDir'));
            add_action('wp_ajax_wpdm_scandir', array($this,'scanDir'));
            add_action('wp_ajax_wpdm_unlink', array($this,'deleteItem'));
            add_action('wp_ajax_wpdm_rename', array($this,'renameItem'));
            add_action('wp_ajax_wpdm_copypaste', array($this,'copyItem'));
            add_action('wp_ajax_wpdm_cutpaste', array($this,'moveItem'));

            add_shortcode('wpdm_file_manager', array($this,'frontendFileManager'));

            add_filter('wpdm_frontend', array($this,'frontendFileManagerTab'));

            if(!class_exists('EdenFileManagerMain')) {
                require_once 'classes/fileManager.php';
            }
            if(!class_exists('EdenFileManagerUtlity')){
                require_once 'classes/utility.php';
            }
            
            
            if ( defined('DOING_AJAX') ) {
                add_action("wp_ajax_filemanager_call", array($this,"fileManagerCall"));
            }
            
            if(is_admin()){
                add_action('admin_menu',array($this,'adminMenu'),10);
                //add_action('admin_head', array($this,'customCss'));
                add_action('init',array($this,'downloadFile'));
               
            }
            
        }
        
        public function activate(){
            
        }
        
        public function deactivate(){
            
        }
        
        public function register_session(){
            if( !session_id() ){
                session_start();
            }
        }
        
        function myEndSession() {
            if( session_id() )
            session_destroy ();
        }

        public function fileManagerCall(){
            EdenFileManagerMain::getInstance();
        }

        public function uploadFile(){

            if(!current_user_can('manage_options') || !isset($_POST['tdir'])) die('Error!');
            if (is_uploaded_file($_FILES['async-upload']['tmp_name'])) {
                $dir = get_option('_wpdm_file_browser_root',$_SERVER['DOCUMENT_ROOT']) . '/' . $_POST['tdir'];
                $tempFile = $_FILES['async-upload']['tmp_name'];
                $targetFile = $dir . '/' . $_FILES['async-upload']['name'];
                move_uploaded_file($tempFile, $targetFile);
                echo $_POST['tdir'];
            }
            die();
        }
        
        
        public function downloadFile(){
            if(isset($_GET['act']) && $_GET['act'] == 'download')
            if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'filemanager_nonce')) {
                $this->util = new EdenFileManagerUtlity();
                $this->base_path = get_option('_wpdm_file_browser_root',$_SERVER['DOCUMENT_ROOT']) . '/';
                $this->path = isset($_GET['path']) ? urldecode(trim(strip_tags($_GET['path']),"/") ."/") : '';
                $this->file = isset($_GET['file']) ? $this->util->fix_get_params($_GET['file']) : '';
                
                $old_file = $this->base_path . $this->path . $this->file;
                $info = pathinfo($old_file);
                
                if(!file_exists($old_file)){
                    die('Wrong File');
                }
                $fix = $this->util->fix_strtolower($info['extension']);
                $mime="";
                $MIMEtypes = WpdmFileManager::mimeTypes();
                if (!empty($MIMEtypes))
                {
                    if ($fix == "")
                            $mime="Unknown/Unknown";
                    $f = false;
                    while (list($mimetype, $file_extensions) = each($MIMEtypes)){
                        foreach (explode(" ", $file_extensions) as $file_extension)
                                if ($fix == $file_extension){
                                    $mime=$mimetype;
                                    $f = true;
                                    break;
                                }
                                if($f == true) break;
                    }
                                        
                }
                
                header('Content-Description: File Transfer');
                header('Content-Type: ' . $mime);
                header('Content-Disposition: attachment; filename='.basename($old_file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($old_file));
                ob_clean();
                flush();
                readfile($old_file);
                exit;
                
            }
        }
        
        public static function mimeTypes(){
            self::$instance->mime_type = array(
                "application/andrew-inset"       => "ez",
                "application/mac-binhex40"       => "hqx",
                "application/mac-compactpro"     => "cpt",
                "application/msword"             => "doc",
                "application/octet-stream"       => "bin dms lha lzh exe class so dll",
                "application/oda"                => "oda",
                "application/pdf"                => "pdf",
                "application/postscript"         => "ai eps ps",
                "application/smil"               => "smi smil",
                "application/vnd.ms-excel"       => "xls",
                "application/vnd.ms-powerpoint"  => "ppt",
                "application/vnd.wap.wbxml"      => "wbxml",
                "application/vnd.wap.wmlc"       => "wmlc",
                "application/vnd.wap.wmlscriptc" => "wmlsc",
                "application/x-bcpio"            => "bcpio",
                "application/x-cdlink"           => "vcd",
                "application/x-chess-pgn"        => "pgn",
                "application/x-cpio"             => "cpio",
                "application/x-csh"              => "csh",
                "application/x-director"         => "dcr dir dxr",
                "application/x-dvi"              => "dvi",
                "application/x-futuresplash"     => "spl",
                "application/x-gtar"             => "gtar",
                "application/x-hdf"              => "hdf",
                "application/x-javascript"       => "js",
                "application/x-koan"             => "skp skd skt skm",
                "application/x-latex"            => "latex",
                "application/x-netcdf"           => "nc cdf",
                "application/x-sh"               => "sh",
                "application/x-shar"             => "shar",
                "application/x-shockwave-flash"  => "swf",
                "application/x-stuffit"          => "sit",
                "application/x-sv4cpio"          => "sv4cpio",
                "application/x-sv4crc"           => "sv4crc",
                "application/x-tar"              => "tar",
                "application/x-tcl"              => "tcl",
                "application/x-tex"              => "tex",
                "application/x-texinfo"          => "texinfo texi",
                "application/x-troff"            => "t tr roff",
                "application/x-troff-man"        => "man",
                "application/x-troff-me"         => "me",
                "application/x-troff-ms"         => "ms",
                "application/x-ustar"            => "ustar",
                "application/x-wais-source"      => "src",
                "application/zip"                => "zip",
                "audio/basic"                    => "au snd",
                "audio/midi"                     => "mid midi kar",
                "audio/mpeg"                     => "mpga mp2 mp3",
                "audio/x-aiff"                   => "aif aiff aifc",
                "audio/x-mpegurl"                => "m3u",
                "audio/x-pn-realaudio"           => "ram rm",
                "audio/x-pn-realaudio-plugin"    => "rpm",
                "audio/x-realaudio"              => "ra",
                "audio/x-wav"                    => "wav",
                "chemical/x-pdb"                 => "pdb",
                "chemical/x-xyz"                 => "xyz",
                "image/bmp"                      => "bmp",
                "image/gif"                      => "gif",
                "image/ief"                      => "ief",
                "image/jpeg"                     => "jpeg jpg jpe",
                "image/png"                      => "png",
                "image/tiff"                     => "tiff tif",
                "image/vnd.wap.wbmp"             => "wbmp",
                "image/x-cmu-raster"             => "ras",
                "image/x-portable-anymap"        => "pnm",
                "image/x-portable-bitmap"        => "pbm",
                "image/x-portable-graymap"       => "pgm",
                "image/x-portable-pixmap"        => "ppm",
                "image/x-rgb"                    => "rgb",
                "image/x-xbitmap"                => "xbm",
                "image/x-xpixmap"                => "xpm",
                "image/x-xwindowdump"            => "xwd",
                "model/iges"                     => "igs iges",
                "model/mesh"                     => "msh mesh silo",
                "model/vrml"                     => "wrl vrml",
                "text/css"                       => "css",
                "text/html"                      => "html htm",
                "text/plain"                     => "asc txt",
                "text/richtext"                  => "rtx",
                "text/rtf"                       => "rtf",
                "text/sgml"                      => "sgml sgm",
                "text/tab-separated-values"      => "tsv",
                "text/vnd.wap.wml"               => "wml",
                "text/vnd.wap.wmlscript"         => "wmls",
                "text/x-setext"                  => "etx",
                "text/xml"                       => "xml xsl",
                "video/mpeg"                     => "mpeg mpg mpe",
                "video/quicktime"                => "qt mov",
                "video/vnd.mpegurl"              => "mxu",
                "video/x-msvideo"                => "avi",
                "video/x-sgi-movie"              => "movie",
                "x-conference/x-cooltalk"        => "ice",
           );
           return self::$instance->mime_type;
        }
        
        public static function getDir(){
            return self::$instance->dir;
        }

        public static function getUrl(){
            return self::$instance->url;
        }
        
        public function adminMenu(){
            $slug1 = add_submenu_page( 'edit.php?post_type=wpdmpro',__("Eden File Manager",TD_FILE), __('File Manager',TD_FILE), 'manage_options', 'eden-file-manager', array($this,'fileManager'));
            //$slug2 = add_submenu_page( 'eden-file-manager', __('File Manager Settings',TD_FILE), __('Settings',TD_FILE), 'manage_options', 'file-manager-settings', array($this,'fileSettings'));

            add_action('admin_print_styles-' . $slug1, array($this,'adminStyles'));
            add_action('admin_print_scripts-' . $slug1, array($this,'adminScripts'));
        }

        function mkDir(){
            global $current_user;
            if(isset($_REQUEST['__wpdm_mkdir']) && !wp_verify_nonce($_REQUEST['__wpdm_mkdir'], NONCE_KEY)) die('Error! Session Expired. Try refreshing page.');
            if(!is_user_logged_in()) die('Unauthorized Access!');
            $root = UPLOAD_DIR.$current_user->user_login.'/';
            $path = realpath($root.$_REQUEST['path']).'/';
            if(!strstr($path, $root)) die('Error!');
            $name = str_replace(array(".", "/"), "_", $_REQUEST['name']);
            mkdir($path.$name);
            die($path.$name);
        }

        function scanDir(){
            if(isset($_REQUEST['__wpdm_scandir']) && !wp_verify_nonce($_REQUEST['__wpdm_scandir'], NONCE_KEY)) die('Error! Session Expired. Try refreshing page.');
            if(!is_user_logged_in()) die('Unauthorized Access!');
            global $current_user;
            $root = current_user_can('manage_options')?UPLOAD_DIR:UPLOAD_DIR.$current_user->user_login.'/'; //UPLOAD_DIR.$current_user->user_login.'/';
            if(!file_exists($root)){
                mkdir($root);
                \WPDM\FileSystem::blockHTTPAccess($root);
            }

            $path = realpath($root.$_REQUEST['path']).'/';
            if(!strstr($path, $root)) die('Error! '. $path." || ".$root);
            $items = scandir($path);
            $_items = array();
            header('Content-type: application/json');
            foreach ($items as $item){
                if(!in_array($item, array('.', '..', '.htaccess'))) {
                    $item_label = $item;
                    $item_label = strlen($item_label) > 20 ? substr($item_label, 0, 10) . "..." . substr($item_label, strlen($item_label) - 10) : $item_label;
                    $ext = explode('.', $item); $ext = end($ext);
                    $icon = file_exists(WPDM_BASE_DIR.'/assets/file-type-icons/'.$ext.'.png')?plugins_url('download-manager/assets/file-type-icons/'.$ext.'.png'):plugins_url('download-manager/assets/file-type-icons/_blank.png');
                    $type = is_dir($path.$item)?'dir':'file';
                    $note = is_dir($path.$item)?(count(scandir($path.$item))-2).' items':number_format((filesize($path.$item)/1024),2).' KB';
                    $rpath = str_replace($root, "", $path.$item);
                    $_items[] = array('item_label' => $item_label, 'item' => $item, 'icon' => $icon, 'type' => $type, 'note' => $note, 'path' => $rpath);
                }
            }
            echo json_encode($_items);
            die();
        }

        function deleteItem(){
            if(isset($_REQUEST['__wpdm_unlink']) && !wp_verify_nonce($_REQUEST['__wpdm_unlink'], NONCE_KEY)) die('Error! Session Expired. Try refreshing page.');
            if(!is_user_logged_in()) die('Unauthorized Access!');
            global $current_user;
            $root = UPLOAD_DIR.$current_user->user_login.'/';
            $path = realpath($root.$_REQUEST['path']).'/'.$_REQUEST['delete'];
            if(!strstr($path, $root)) die('Error!');
            if(is_dir($path))
                $this->rmDir($path);
            else
                unlink($path);
        }

        function renameItem(){
            if(isset($_REQUEST['__wpdm_rename']) && !wp_verify_nonce($_REQUEST['__wpdm_rename'], NONCE_KEY)) die('Error! Session Expired. Try refreshing page.');
            if(!is_user_logged_in()) die('Unauthorized Access!');
            global $current_user;
            $root = UPLOAD_DIR.$current_user->user_login.'/';
            $oldpath = realpath($root.$_REQUEST['path']).'/'.$_REQUEST['item'];
            $newpath = realpath($root.$_REQUEST['path']).'/'. str_replace(array("/","\\"), "_", $_REQUEST['newname']);
            if(!strstr($oldpath, $root)) die('Error!');
            rename($oldpath, $newpath);
            die('ok');
        }

        function moveItem(){
            if(isset($_REQUEST['__wpdm_cutpaste']) && !wp_verify_nonce($_REQUEST['__wpdm_cutpaste'], NONCE_KEY)) die('Error! Session Expired. Try refreshing page.');
            if(!is_user_logged_in()) die('Unauthorized Access!');
            global $current_user;
            $root = UPLOAD_DIR.$current_user->user_login.'/';
            $oldpath = realpath($root.$_REQUEST['source']);
            $newpath = realpath($root.$_REQUEST['dest']).'/'.basename($oldpath);
            if(!strstr($oldpath, $root)) die('Error! '.$oldpath);
            if(!strstr($newpath, $root)) die('Error! '.$newpath);
            if(!strstr($oldpath, $root)) die('Error!');
            rename($oldpath, $newpath);
            die('ok');
        }

        function copyItem(){
            if(isset($_REQUEST['__wpdm_copypaste']) && !wp_verify_nonce($_REQUEST['__wpdm_copypaste'], NONCE_KEY)) die('Error! Session Expired. Try refreshing page.');
            if(!is_user_logged_in()) die('Unauthorized Access!');
            global $current_user;
            $root = UPLOAD_DIR.$current_user->user_login.'/';
            $oldpath = realpath($root.$_REQUEST['source']);
            $newpath = realpath($root.$_REQUEST['dest']).'/'.basename($oldpath);
            if(!strstr($oldpath, $root)) die('Error! '.$oldpath);
            if(!strstr($newpath, $root)) die('Error! '.$newpath);
            echo $oldpath;
            copy($oldpath, $newpath);
            die('ok');
        }

        function rmDir($dir){
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? $this->rmDir("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }

        
        
        public function fileSettings(){
            echo "hello";
        }
        
        public function fileManager(){
            if(!class_exists('EdenFileManagerMain')) {
                require_once 'classes/fileManager.php';
            }
            EdenFileManagerMain::getInstance();
        }


        public function adminStyles() {
            wp_enqueue_style('file_bootstrap', $this->url . '/assets/bootstrap3.1.1/css/bootstrap.css');
            wp_enqueue_style('file_main', $this->url . '/assets/css/style.css');
            wp_enqueue_style('jquery_jGrowl' , $this->url . '/assets/jGrowl/jquery.jgrowl.min.css');
            
            if(isset($_REQUEST['act']) && $_REQUEST['act'] === 'edit'){
                // Load SyntaxHighlight CSS file
                wp_enqueue_style( 'sh-css', 				$this->url . '/assets/syntax-highlight/syntax-highlight.css' );
            }
            
            
        }
        
        public function adminScripts(){
            
            wp_enqueue_script('jquery');
            wp_enqueue_script('file_bootstrap', $this->url . '/assets/bootstrap3.1.1/js/bootstrap.min.js', array('jquery'));
            
            wp_enqueue_script('jquery_jGrowl' , $this->url . '/assets/jGrowl/jquery.jgrowl.min.js', array('jquery'));
            
            
            if(isset($_REQUEST['act']) && $_REQUEST['act'] === 'edit'){
                // Load ACE
                wp_enqueue_script( 'sh-ace',  $this->url . '/assets/syntax-highlight/lib/src-min-noconflict/ace.js');	
                wp_enqueue_script( 'sh-ace-ext-modelist', 	$this->url . '/assets/syntax-highlight/lib/src-min-noconflict/ext-modelist.js');	

                // Load SyntaxHighlight CSS file
                wp_enqueue_script( 'sh-js', 		$this->url . '/assets/syntax-highlight/syntax-highlight.js');
            }
           
        }

        function frontendFileManagerTab($tabs){
            $tabs['file-manager'] = array('label' => 'File Manager', 'callback' => array($this, 'frontendFileManager'));
            return $tabs;
        }

        function frontendFileManager(){

            include \WPDM\Template::locate("filemanager-frontend.php", __DIR__);

        }
    }
    
    WpdmFileManager::getInstance();
endif;