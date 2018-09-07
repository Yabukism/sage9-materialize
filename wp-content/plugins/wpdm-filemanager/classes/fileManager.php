<?php
if(!defined('WPINC')) {
    exit;
}

if(!class_exists('EdenFileManager')){
    class EdenFileManagerMain{
        private static $instance;
        private $base_path,  /** Base file path */
                $path,      /** file path */
                $dir,       /** Directory name to add or remove */
                $file,      /** File name to add or remove */
                $action,    /** File/Directory actions: add, edit, delete */
                $type,      /** File or Directory */
                $sort_by,   /** directory sorting */
                $util,      /** EdenFileManagerUtlity class object */
                $messages,  /** Will hold various notice */
                $extensions,    /** File edit extension */
                $view,          /** List / Grid View */
                $old;
                var $ext_img = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg'); //Images
                var $ext_file = array('doc', 'docx','rtf', 'pdf', 'xls', 'xlsx', 'txt', 'csv','html','xhtml','psd','sql','log','fla','xml','ade','adp','mdb','accdb','ppt','pptx','odt','ots','ott','odb','odg','otp','otg','odf','ods','odp','css','ai'); //Files
                var $ext_video = array('mov', 'mpeg', 'mp4', 'avi', 'mpg','wma',"flv","webm"); //Video 
                var $ext_music = array('mp3', 'm4a', 'ac3', 'aiff', 'mid','ogg','wav'); //Audio
                var $ext_misc = array('zip', 'rar','gz','tar','iso','dmg'); //Archives
                var $editable = array('php','php4','php3','phtml','phps','conf','sh','shar','csh',
                    'ksh','tcl','cgi','pl','js','txt','ini','html','htm','css','xml','xsl',
                    'ini','inf','cfg','log','nfo','bat','htaccess');
                

        
        public static function getInstance(){
            if(self::$instance === null){
                self::$instance = new self;
                if(is_user_logged_in()){
                    
                    self::$instance->util = new EdenFileManagerUtlity();
                    self::$instance->path = isset($_GET['path']) && trim($_GET['path']) != ''  ? urldecode(trim(strip_tags($_GET['path']),"/") ."/") : '';
                    self::$instance->dir = isset($_GET['dir']) ? self::$instance->util->fix_get_params($_GET['dir']) : '';
                    self::$instance->file = isset($_GET['file']) ? self::$instance->util->fix_get_params($_GET['file']) : '';
                    self::$instance->type = isset($_GET['type']) ? self::$instance->util->fix_get_params($_GET['type']) : '';
                    self::$instance->action = isset($_GET['act']) ? self::$instance->util->fix_get_params($_GET['act']) : '';
                    self::$instance->sort_by = isset($_GET['sort']) ? self::$instance->util->fix_get_params($_GET['sort']) : '';
                    self::$instance->base_path = get_option('_wpdm_file_browser_root',$_SERVER['DOCUMENT_ROOT']) . '/';
                    self::$instance->view = isset($_GET['view']) ? self::$instance->util->fix_get_params($_GET['view']) : 'grid';
                    self::$instance->messages = array();
                    self::$instance->extensions = array_merge(self::$instance->ext_img, self::$instance->ext_file, self::$instance->ext_misc, self::$instance->ext_video,self::$instance->ext_music);
                    //echo 'path = ' . self::$instance->path . ' ';
                    self::$instance->path = str_replace(array('../','./'), '', self::$instance->path);
                    self::$instance->file = str_replace(array('../','./'), '', self::$instance->file);
                    //echo 'path2 = ' .self::$instance->path . ' ';
                    self::$instance->old = admin_url('edit.php?post_type=wpdmpro&page=eden-file-manager&view='.  self::$instance->view);
                    self::$instance->actions();
                }
                    
            }
            return self::$instance;
        }

        private function actions(){
            
            if ( defined('DOING_AJAX') ) {
                $this->ajaxContent();
            }
            else {
                $content = "";
                ob_start();
                $this->getBreadcum();
                $this->getContent();
                $content = ob_get_clean();

                echo $this->getHeader();
                echo $this->printMessages();
                echo $content;
                echo $this->getFooter();
                echo $this->getJs();
            }
        }
        
        private function ajaxContent(){
            if($this->action === 'create' && $this->type === 'dir')
                $this->createDir();
            elseif($this->action === 'rename' && $this->type === 'dir')
                $this->renameDir();
            elseif($this->action === 'create' && $this->type === 'file')
                $this->createFile();
            elseif($this->action === 'rename' && $this->type === 'file')
                $this->renameFile();
            elseif($this->action === 'download' && $this->type === 'file') {
                $this->downloadFile();
            }
            elseif($this->action === 'copy' || $this->action === 'move') {
                $this->copyFile();
            }
            elseif($this->action === 'paste'){
                $this->pasteFile();
            }
            elseif ($this->action === 'clear') {
                $this->emptyClipBoard();
            }
            elseif($this->action === 'multiple') {
                $this->handleMultiple();
            }
            elseif($this->action === 'addToMtClip') {
                $this->addToMtClip();
            }
            elseif($this->action === 'copy_multiple'){
                $this->copyMultiple();
            }
            elseif($this->action === 'delete_multiple'){
                $this->deleteMultiple();
            }
            
            elseif($this->action === 'view_list') {
                if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'filemanager_view_list')) {
                    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        $this->getBreadcum();
                        if($this->view === 'grid'){
                            $this->browseDirGrid();
                        }
                        elseif($this->view === 'list'){
                            $this->browseDirList();
                        }
                    }
                    else {
                        header("Location: ".$_SERVER["HTTP_REFERER"]);
                    }
                }

                die();
            }
        }
        
        
        

        private function getContent(){
            if($this->action === 'edit' )
                $this->editFile();
            elseif($this->action === 'delete' && $this->type === 'file')
                $this->deleteFile();
            elseif($this->action === 'delete' && $this->type === 'dir')
                $this->deleteDir();
            elseif($this->view === 'grid')
                $this->browseDirGrid();  
            elseif($this->view === 'list')
                $this->browseDirList();
        }
        
        private function deleteMultiple(){
            if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'filemanager_nonce')) {
                $result['type']='success';
                $files = isset($_GET['files']) ? $_GET['files']: array();
                unset($_SESSION['fm_multiple']);
                foreach ($files as $key => $info){
                    $file_name = str_replace(array('../','./'), '', $info['file']);
                    $file_type = $info['type'];
                    $file = $this->path . $file_name;
                    
                    $file_path = $this->base_path . $file;
                    if($info['type']=='file') {
                        if(!file_exists($file_path)) {
                            $result['error'][] = "Invalid Link $file";
                        }
                        else {
                            if (@unlink($file_path)){
                                $result['message'][] = "$file deleted successfully";
                                unset($_SESSION['fm_multiple'][$file]);
                            }
                            else {
                                $result['error'][] = "$file delete failed";
                            }
                        }
                    }
                    elseif($info['type'] == 'dir'){
                        if(!file_exists($file_path)){
                            $this->messages[] = "Invalid Link $file";
                        }
                        else {
                            if ($this->util->deleteDir($file_path)){
                                $result['message'][] = "$file deleted successfully";
                                unset($_SESSION['fm_multiple'][$file]);
                            }
                            else {
                                $result['error'][] = "$file delete failed";
                            }
                        }
                    }
                }
                
                
                
            }
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $result = json_encode($result);
                echo $result;
            }
            else {
                header("Location: ".$_SERVER["HTTP_REFERER"]);
            }

            die();
        }
        
        private function copyMultiple(){
            if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'filemanager_nonce')) {
                $result['type']='success';
                $files = isset($_GET['files']) ? $_GET['files']: array();
                unset($_SESSION['fm_multiple']);
                foreach ($files as $key => $info){
                    $file_path = $this->path;
                    $file_name = str_replace(array('../','./'), '', $info['file']);
                    $file_type = $info['type'];
                    $file = $file_path . $file_name;
                    $temp = rtrim($file_path,'/');
                    if(!empty($_SESSION['fm_multiple']) && array_key_exists($temp, $_SESSION['fm_multiple'])) {
                        $result['error'][] = 'Parent folder already exist in clipboard.';
                    }
                    else {
                        $data = array(
                            'path' => $file_path,
                            'file' => $file_name,
                            'type' => $file_type
                        );
                        $_SESSION['fm_multiple'][$file] = $data;
                    }
                }
                
                $result['cnt'] = count($_SESSION['fm_multiple']);
                if($result['cnt']<=0) unset($_SESSION['fm_multiple']);
            }
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $result = json_encode($result);
                echo $result;
            }
            else {
                header("Location: ".$_SERVER["HTTP_REFERER"]);
            }

            die();
            
        }
        
        private function handleMultiple(){
            $task = isset($_GET['task']) ? self::$instance->util->fix_get_params($_GET['task']) : '';
            $result['type'] = 'error';
            $task_path = $this->base_path . $this->path;
            if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'filemanager_nonce')) {
                $result['type']='success';
                
                
                
                if($task === 'clear') {
                    if(isset($_SESSION['fm_multiple'])) {
                        unset($_SESSION['fm_multiple']);
                    }
                    $result['cnt'] = 0;
                    $result['message'][] = 'Clipboard Cleared.';
                }
                
                elseif($task === 'paste' || $task === 'move') {
                    if(!empty($_SESSION['fm_multiple'])) {
                        if(array_key_exists(rtrim($this->path,'/'), $_SESSION['fm_multiple'])) {
                            $result['error'][] = 'Current Path: ' . $this->path . ' already exist in clipboard. Please select another folder';
                        }
                        else {
                            foreach ($_SESSION['fm_multiple'] as $file => $info) {
                                $old_path = $this->base_path . $file;
                                $new_path = $this->base_path . $this->path . $info['file'];
                                if($info['type']=='file') {
                                    if(!file_exists($old_path)) {
                                        $result['error'][] = "Invalid Link $file";
                                    }
                                    elseif(file_exists($new_path)){
                                        $result['error'][] = "File already exist";
                                    }
                                    else {
                                        if(copy($old_path,$new_path)){
                                            $result['message'][] = "$file copied successfully";
                                            unset($_SESSION['fm_multiple'][$file]);
                                            if($task === 'move'){
                                                @unlink($old_path);
                                            }
                                        }
                                        else {
                                            $result['error'][] = $file . ' copy failed';
                                        }
                                    }
                                }
                                elseif($info['type'] == 'dir'){
                                    if(!is_dir($old_path)) {
                                        $result['error'][] = 'Invalid Directory: ' . $file;
                                    }
                                    elseif(file_exists($new_path)){
                                        $result['error'][] = 'Directory already exist';
                                    }
                                    else {
                                        $this->util->rcopy($old_path,$new_path,true);
                                        $result['message'][] = 'Folder Copied Successfully';
                                        unset($_SESSION['fm_multiple'][$file]);
                                        if($task === 'move'){
                                            $this->util->deleteDir($old_path);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                elseif($task === 'delete') {
                    if(!empty($_SESSION['fm_multiple'])) {
                        foreach ($_SESSION['fm_multiple'] as $file => $info) {
                            
                        }
                        
                    }
                }
                elseif($task === 'view') {
                    $file_list = '<h4>File List</h4>';
                    if(!empty($_SESSION['fm_multiple'])) {
                        foreach ($_SESSION['fm_multiple'] as $file => $info) {
                            $file_list .= '<p>path: ' . $file . ' type: ' . $info['type'] . '</p>';
                        }
                    }
                    $result['file_list'] = $file_list;
                }
                
                if(!empty($_SESSION['fm_multiple'])) {
                    $result['cnt'] = count($_SESSION['fm_multiple']);
                }
                else {
                    $result['cnt'] = 0;
                    unset($_SESSION['fm_multiple']);
                }
            }
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $result = json_encode($result);
                echo $result;
            }
            else {
                header("Location: ".$_SERVER["HTTP_REFERER"]);
            }

            die();
        }
        
        private function addToMtClip(){
            $task = isset($_GET['task']) ? self::$instance->util->fix_get_params($_GET['task']) : '';
            $result['type'] = 'error';
            if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'filemanager_nonce')) {
                $result['type']='success';
                $file = $this->path . $this->file;
                $data = array(
                    'path' => $file ,
                    'file' => $this->file,
                    'type' => $this->type
                );
                if($task === 'add') {
                    $temp = rtrim($this->path,'/');
                    if(!empty($_SESSION['fm_multiple']) && array_key_exists($temp, $_SESSION['fm_multiple'])) {
                        $result['error'][] = 'Parent folder already exist in clipboard.';
                    }
                    else {
                        $_SESSION['fm_multiple'][$file] = $data;
                    }
                }
                elseif($task === 'remove') {
                    if(!empty($_SESSION['fm_multiple'][$file])) {
                        unset($_SESSION['fm_multiple'][$file]);
                    }
                }
                $result['cnt'] = count($_SESSION['fm_multiple']);
                
                if($result['cnt']<=0) unset($_SESSION['fm_multiple']);
            }
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $result = json_encode($result);
                echo $result;
            }
            else {
                header("Location: ".$_SERVER["HTTP_REFERER"]);
            }

            die();
        }
        
        private function copyFile(){
            $result['type'] = 'error';
            if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'filemanager_nonce')) {
                $result['type']='success';
                $data = array('path' => $this->path, 'file' => $this->file, 'type' => $this->type , 'action' => $this->action);
                $_SESSION['filemanager_copy'] = $data;
                $result['message'][] = __('File successfully copied to clipboard');
            }
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $result = json_encode($result);
                echo $result;
            }
            else {
                header("Location: ".$_SERVER["HTTP_REFERER"]);
            }

            die();
        }
        
        private function pasteFile(){
            $result['type'] = 'error';
            if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'filemanager_nonce')) {
                $data = $_SESSION['filemanager_copy'];
                $result['type'] = 'success';
                if($data['type']=='file') {
                    $old_path = $this->base_path . $data['path'] . $data['file'];
                    $new_path = $this->base_path . $this->path . $data['file'];
                    if(!file_exists($old_path)) {
                        $result['error'][] = "Invalid Link";
                    }
                    elseif(file_exists($new_path)){
                        $result['error'][] = "File already exist";
                    }
                    else {
                        if(copy(realpath($old_path),realpath($new_path))){
                            $result['message'][] = "File copied successfully";
                            unset($_SESSION['filemanager_copy']);
                            if($data['action']==='move'){
                                @unlink($old_path);
                            }
                        }
                        else {
                            $result['error'][] = 'File copy failed';
                        }
                    }
                    
                }
                elseif($data['type'] == 'dir'){
                    $old_path = $this->base_path . $data['path'] . $data['file'];
                    $new_path = $this->base_path . $this->path . $data['file'];
                    
                    if(!is_dir($old_path)) {
                        $result['error'][] = 'Invalid Directory';
                    }
                    elseif(file_exists($new_path)){
                        $result['error'][] = 'Directory already exist';
                    }
                    else {
                        $this->util->rcopy($old_path,$new_path,true);
                        $result['message'][] = 'Folder Copied Successfully';
                        unset($_SESSION['filemanager_copy']);
                        if($data['action']==='move'){
                            $this->util->deleteDir($old_path);
                        }
                    }
                }
            }
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $result = json_encode($result);
                echo $result;
            }
            else {
                header("Location: ".$_SERVER["HTTP_REFERER"]);
            }

            die();
        }
        
        private function emptyClipBoard(){
            $result['type'] = 'error';
            if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'filemanager_nonce')) {
                $result['type']='success';
                unset($_SESSION['filemanager_copy']);
                $result['message'][] = 'Clipboard Cleared';
            }
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $result = json_encode($result);
                echo $result;
            }
            else {
                header("Location: ".$_SERVER["HTTP_REFERER"]);
            }

            die();
        }
        
        private function createDir(){
            $result['type'] = 'error';
            if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'filemanager_nonce')) {
                $result['type']='success';
                $path = $this->base_path . $this->path . $this->util->fix_get_params($_GET['name']);
                
                $oldumask = umask(0);
                if($this->base_path . $this->path == $path) {
                    $result['error'][] = "Please specify a folder name";
                }
                elseif(file_exists($path)) {
                    $result['error'][] = "Folder already exist.";
                }
                else {
                    if(mkdir($path, 0777, true)){
                        $result['message'][] = "Folder Created Successfully.";
                    }
                    else {
                        $result['error'][] = "Failed";
                    }
                }
                umask($oldumask);
            }
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $result = json_encode($result);
                echo $result;
            }
            else {
                header("Location: ".$_SERVER["HTTP_REFERER"]);
            }

            die();
        }

        private function createFile(){
            $result['type'] = 'error';
            if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'filemanager_nonce')) {
                $result['type']='success';
                $path = $this->base_path . $this->path . $this->util->fix_get_params($_GET['name']);
                
                if(trim($this->util->fix_get_params($_GET['name'])) == '') {
                    $result['error'][] = "Please specify a file name";
                }
                elseif(file_exists($path)) {
                    $result['error'] = "File already exist.";
                }
                else {
                    if(@fopen($path, "w+")){
                        $result['message'][] = "File Created Successfully.";
                    }
                    else {
                        $result['error'][] = "Failed";
                    }
                }
                
            }
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $result = json_encode($result);
                echo $result;
            }
            else {
                header("Location: ".$_SERVER["HTTP_REFERER"]);
            }

            die();
            
        }
        
        
        private function editFile(){
            if ( ! empty( $_POST ) && check_admin_referer( 'edit_file' ) ) {
                $path = $this->base_path . $this->path . $this->file;
                $data = stripslashes($_POST['data']);
                if ($fp = @fopen ($path, "wb")){
                    @fwrite($fp, $data);
                    @fclose($fp);
                    $tmp = explode('/', $this->path);
                    unset($tmp[count($tmp)-1]);
                    $old = $this->old;
                    $link = implode('/', $tmp);
                    $link = add_query_arg(array('path' => $link),$old);
                    $btn = <<<EOD
                            <input type="button" name="cancel" class="btn btn-default" value="Back" onclick="location.href = '$link';" />
EOD;
                            
                    $this->messages[] = "File saved successfully. $btn";
                }
                else {
                    $this->messages[] = 'File read Error';
                }
                
            }
            else {
                $temp = explode('/', $this->path);
                unset($temp[count($temp)-1]);
                $url = implode('/', $temp);
                $old = $this->old;
                $link = add_query_arg(array('path'=>$url),$old);
            ?>
<div class="panel panel-default">
    <div class="panel-heading">Edit File - <strong><?php echo $this->file;  ?></strong></div>
    <div class="">
        <?php  $path = $this->base_path . $this->path . $this->file;
        $file_info = pathinfo($path);
        $ext = $file_info['extension'];
        if ($fp = @fopen($path, "rb")): ?>
        <form method="post" class="form-horizontal" role="form">
            <?php wp_nonce_field( 'edit_file' );?>
            <div class="form-group" style="position:relative; margin-left: 0em;" >
                <input type="hidden" name="ext" value="<?php echo $path; ?>">
                <textarea name="data"  class="col-md-12 col-sm-12 col-xs-12" id="newcontent" rows="75" style=""><?php 
                        if (filesize($path) > 0 ){
                            print htmlentities(fread($fp, filesize($path)));
                            @fclose ($fp);
                        }
                    ?>
                </textarea>
            </div>

            <div class="form-group" style="position:relative; margin-left: 2em;" >
              <div class="">
                <button type="submit" class="btn btn-primary">Save</button>
                <input type="button" name="cancel" class="btn btn-default" value="Back" onclick="location.href = '<?php echo $link; ?>';" />
              </div>
            </div>
        </form>
        <?php else: ?>
            <?php echo __('Error Reading File',TD_FILE); ?>
        <?php endif; ?>
    </div>
</div>
<?php
            }
        }
        
        
        private function renameFile(){
            $result['type'] = 'error';
            if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'filemanager_nonce')) {
                
                $result['type']='success';
                $old_file = $this->base_path . $this->path . $this->file;
                $name = $this->util->fix_filename($_GET['name'],false);
                
                //$info=pathinfo($old_file);
                $new_file=  $this->base_path . $this->path .$name;
                
                if(!file_exists($old_file)){
                    $result['error'][] = "Invalid Link";
                }
                elseif($name == '') {
                    $result['error'][] = "Please specify a file name";
                }
                elseif(file_exists($new_file)) {
                    $result['error'][] = "Filename already exist.";
                }
                else {
                    if (@rename($old_file, $new_file)){
                        $result['message'][] = "Renamed Successfull.";
                        
                    }
                    else {
                        $result['error'][] = "File Rename Failed";
                    }
                }
                
            }
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $result = json_encode($result);
                echo $result;
            }
            else {
                header("Location: ".$_SERVER["HTTP_REFERER"]);
            }

            die();
        }
        
        private function renameDir(){
            $result['type'] = 'error';
            if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'filemanager_nonce')) {
                
                $result['type']='success';
                $old_file = $this->base_path . $this->path .$this->file;
                $name = $this->util->fix_filename($_GET['name'],false);
                $new_file = $this->base_path . $this->path . $name; 
                
                if(!file_exists($old_file)){
                    $result['error'][] = "Invalid Link";
                }
                elseif($name == '') {
                    $result['error'][] = "Please specify a folder name";
                }
                elseif(file_exists($new_file)) {
                    $result['error'][] = "Filename already exist.";
                }
                else {
                    
                    if(rename($old_file,$new_file)){
                        $result['message'][] = 'Folder Renamed Successfully';
                    }
                    else {
                        $result['error'][] = 'Folder rename failed';
                    }
                    
                }
                
            }
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $result = json_encode($result);
                echo $result;
            }
            else {
                header("Location: ".$_SERVER["HTTP_REFERER"]);
            }

            die();
        }
        
        private function deleteFile(){
            if ( ! empty( $_POST ) && check_admin_referer( 'delete_file' ) && current_user_can('manage_options')) {

                $file = $this->base_path . $this->path . $_GET['file'];
                if(!file_exists($file)){
                    $this->messages[] = "Invalid Link";
                }
                
                else {
                    if (@unlink($file)){
                        $tmp = explode('/', $this->path);
                        unset($tmp[count($tmp)-1]);
                        $old = $this->old;
                        $link = implode('/', $tmp);
                        $link = add_query_arg(array('path' => urlencode($link)),$old);
                        $btn = <<<EOD
                                <input type="button" name="cancel" class="btn btn-default" value="Back" onclick="location.href = '$link';" />
EOD;
                        $this->messages[] = "File Deleted Successfully. $btn";
                        
                    }
                    else {
                        $this->messages[] = "File Delete Failed";
                    }
                }
                
            }
            else {
                $url = remove_query_arg(array('action','type','file'));
?>
<div class="panel panel-default">
    <div class="panel-heading">Delete File <strong><?php echo $this->path . $this->file; ?></strong></div>
    <div class="panel-body">
        <form method="post" class="form-horizontal" role="form">
            <?php wp_nonce_field( 'delete_file' );?>
            <div class="form-group">
              <label for="name" class="col-sm-4 control-label">Are you sure, you want to delete this file?</label>
              
            </div>

            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Delete</button>
                <input type="button" name="cancel" class="btn btn-default" value="Back" onclick="location.href = '<?php echo $url; ?>';" />
              </div>
            </div>
        </form>
    </div>
</div>
<?php
            }
        }
        
        private function deleteDir(){
            if ( ! empty( $_POST ) && check_admin_referer( 'delete_folder' ) ) {
                $file = $this->base_path . $this->path . $this->file;
                if(!file_exists($file)){
                    $this->messages[] = "Invalid Link";
                }
                
                else {
                    if ($this->util->deleteDir($file)){
                        $tmp = explode('/', $this->path);
                        unset($tmp[count($tmp)-1]);
                        $old = $this->old;
                        $link = implode('/', $tmp);
                        $link = add_query_arg(array('path' => urlencode($link)),$old);
                        $btn = <<<EOD
                                <input type="button" name="cancel" class="btn btn-default" value="Back" onclick="location.href = '$link';" />
EOD;
                        $this->messages[] = "Folder Deleted Successfully. $btn";
                        
                    }
                    else {
                        $this->messages[] = "Folder Delete Failed";
                    }
                }
                
            }
            else {
                $url = remove_query_arg(array('action','type','file'));
?>
<div class="panel panel-default">
    <div class="panel-heading">Delete Folder <strong><?php echo $this->path . $this->file; ?></strong></div>
    <div class="panel-body">
        <form method="post" class="form-horizontal" role="form">
            <?php wp_nonce_field( 'delete_folder' );?>
            <div class="form-group">
              <label for="name" class="col-sm-4 control-label">Are you sure, you want to delete this folder?</label>
              
            </div>

            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Delete</button>
                <input type="button" name="cancel" class="btn btn-default" value="Back" onclick="location.href = '<?php echo $url; ?>';" />
              </div>
            </div>
        </form>
    </div>
</div>
<?php
            }
        }
        
        private function browseDirGrid(){
            
            if(is_dir($this->base_path . $this->path)):
            
            $files = scandir($this->base_path.$this->path);
            $n_files=count($files);

            //php sorting
            $sorted=array();
            $current_folder=array();
            $prev_folder=array();
            foreach($files as $k=>$file){
                $size = 0;
                if($file==".") $current_folder=array('file'=>$file);
                elseif($file=="..") $prev_folder=array('file'=>$file);
                elseif(is_dir($this->base_path.$this->path.$file)){
                    $date=filemtime($this->base_path.$this->path. $file);
                    $permissions = decoct(fileperms($this->base_path.$this->path.$file)%01000);
                    //$size =  $this->util->foldersize($this->base_path.$this->path. $file);
                    $file_ext='dir';
                    $sorted[$k]=array('file'=>$file,'date'=>$date,'size'=>$size,'extension'=>$file_ext,'permission'=>$permissions);
                }else{
                    $file_path=$this->base_path.$this->path.$file;
                    $date=filemtime($file_path);
                    $size=filesize($file_path);
                    $permissions = decoct(fileperms($this->base_path.$this->path.$file)%01000);
                    $file_ext = substr(strrchr($file,'.'),1);
                    $sorted[$k]=array('file'=>$file,'date'=>$date,'size'=>$size,'extension'=>$file_ext,'permission'=>$permissions);
                }
            }

            

            switch($this->sort_by){
                case 'name_a':
                    usort($sorted, array($this->util,'filenameSort_a'));
                    break;
                case 'name_z':
                    usort($sorted, array($this->util,'filenameSort_z'));
                    break;
                
                case 'date_a':
                    usort($sorted, array($this->util,'dateSort_a'));
                    break;
                case 'date_z':
                    usort($sorted, array($this->util,'dateSort_z'));
                    break;
                
                case 'ext_a':
                    usort($sorted, array($this->util,'extensionSort_a'));
                    break;
                case 'ext_z':
                    usort($sorted, array($this->util,'extensionSort_z'));
                    break;
                default:
                    break;

            }

            

            $files = array_merge(array($prev_folder),array($current_folder),$sorted);
            $start_list = '';
            $folder_list = '';
            $file_list = '';
            $end_list;
            
            //echo "<pre>" ; print_r($files); echo "</pre>";
            $start_list = "<ul class='grid cs-style-2 list-view0'>";
            if($this->path != '') {
                $icon = WpdmFileManager::getUrl() . '/assets/img/ico/folder_back.jpg';
                $src = explode("/",  $this->path);
                if($src[0] != '') $f = true;
                unset($src[count($src)-2]);
                $src = implode("/",$src);
                if($src == '') {
                    $link= $this->old;
                    
                }
                else {
                    $old_query = $this->old;
                    $link = add_query_arg( array('path' => $src), $old_query );
                }
    $folder_list .= <<<EOD
    <input type='hidden' name='_fm_path' id='_fm_path' value='{$this->path}'>
    <input type='hidden' name='_fm_view' id='_fm_view' value='{$this->view}'>
    <input type='hidden' name='_fm_sort' id='_fm_sort' value='{$this->sort_by}'>                
EOD;

                
if($f == true):

    $folder_list .= <<<EOD
<li class="back">
    <figure class="back-directory">
        <a class='folder-link' href="{$link}" path="{$src}">
            <div class="img-precontainer">
                <div class="img-container directory"><span></span>
                    <img src="{$icon}" />
                </div>
            </div>
            <div class="box no-effect">
                <h4>Back</h4>
            </div>
        </a>
    </figure>
</li>
EOD;
endif;
            }
            
            
            foreach($files as $file):
                if(empty($file) || $file['file'] === '.' || $file['file'] === '..')
                    continue;
                if($file['extension'] === 'dir'):
                    $icon = WpdmFileManager::getUrl() . '/assets/img/ico/folder.jpg';
                    $old_query = $this->old;
                    $link = add_query_arg( array('path' => $this->path . $file['file'] .'/'), $old_query );
                    $delete = add_query_arg(array('path' => $this->path, 'file' => $file['file'], 'act'=>'delete', 'type' => 'dir'),$old_query);
                    $date = date('Y-m-d',$file['date']);
$folder_list .= <<< EOD
<li>
    <figure class="directory">
        <a class='folder-link plink' href="{$link}" data-placement="left" path="{$this->path}{$file['file']}">
            <div class="img-precontainer">
                <div class="img-container directory"><span></span>
                    <img src="{$icon}">
                </div>
            </div>
        </a>
        <div class="box">
            <h4 class="ellipsis">
                <a class="folder-link"  href="{$link}">{$file['file']}</a>
            </h4>
        </div>
        <div class="pcontent">
            <div class="file-name"><span class='glyphicon glyphicon-file'></span> {$file['file']}</div>
            <div class="file-date"><span class='glyphicon glyphicon-calendar'></span> {$date}</div>
            <div class='file-extension'><span class="glyphicon glyphicon-asterisk"></span> {$file['extension']}</div>
            <div class='file-extension'><span class="glyphicon glyphicon-cog"></span> {$file['permission']}</div>
        </div>
        <figcaption>
                <a class="tt edit-button rename-file-paths rename-folder fileAction" data-type='dir' data-action='rename' data-title='Insert Folder Name:' data-btn='Rename' title="Rename" data-file='{$file['file']}'>
                    <span class="glyphicon glyphicon-pencil"></span>
                </a>
                <a class="tt fcopy"  title="Copy" data-path="{$this->path}" data-file="{$file['file']}" data-type="dir" data-act="copy">
                    <span class="glyphicon glyphicon-copyright-mark"></span>
                </a>
                <a class="tt fmove"  title="Move" data-path="{$this->path}" data-file="{$file['file']}" data-type="dir" data-act="move">
                    <span class="glyphicon glyphicon-move"></span>
                </a>
                <a href="{$delete}" class="tt erase-button delete-folder" title="Delete Folde">
                    <span class="glyphicon glyphicon-trash"></span>
                </a>
        </figcaption>
    </figure>
</li>
EOD;
                endif;
                
                if($file['extension'] !== 'dir'):
                    $extension_lower =  $this->util->fix_strtolower($file['extension']);
                    $ico = WpdmFileManager::getDir() . '/assets/img/ico/' . $extension_lower . '.jpg'; 
                    if(file_exists($ico)) {
                        $icon = WpdmFileManager::getUrl() . '/assets/img/ico/' . $extension_lower . '.jpg'; 
                    }
                    else {
                        $icon = WpdmFileManager::getUrl() . '/assets/img/ico/default.jpg'; 
                    }
                    
                    if(in_array($file['extension'], $this->editable)) {
                        $old = admin_url('edit.php?post_type=wpdmpro&page=eden-file-manager');
                        $edit_link = add_query_arg(array('path' => $this->path, 'file' => $file['file'],'act' => 'edit','type'=>'file'),$old);
                        //$edit_link = add_query_arg(array('path' => $this->path . $file['file'],'act' => 'edit','type'=>'file'),$old);
                    }
                    else {
                        $edit_link =  "javascript:void('')";
                    }
                    $class_ext=0;
                    $extension_lower =  $this->util->fix_strtolower($file['extension']);
                    if (in_array($extension_lower, $this->ext_video)) {
                            $class_ext = 4;
                            $is_video=true;
                    }elseif (in_array($extension_lower, $this->ext_img)) {
                            $class_ext = 2;
                    }elseif (in_array($extension_lower, $this->ext_music)) {
                            $class_ext = 5;
                            $is_audio=true;
                    }elseif (in_array($extension_lower, $this->ext_misc)) {
                            $class_ext = 3;
                    }else{
                            $class_ext = 1;
                    }
                    $old = $this->old;
                    $delete = add_query_arg(array('path' => $this->path, 'file' => $file['file'], 'act'=>'delete', 'type' => 'file'),$old);
                    $download = add_query_arg(array('path' => $this->path, 'file' => $file['file'], 'act'=>'download', 'type' => 'file'),$old);
                    $download = wp_nonce_url($download, 'filemanager_nonce', 'nonce');
                    $date = date('Y-m-d',$file['date']);
                    //$copy = add_query_arg(array('path' => $this->path, 'file' => $file['file'], 'act'=>'copy', 'type' => 'file'),$old);
                    //$copy = wp_nonce_url($copy,'filemanager_nonce','nonce');
$file_list .= <<<EOD
<li class="ff-item-type-{$class_ext} file">
    <figure>
        <a class="link plink" href="{$edit_link}">
            <div class="img-precontainer">
                <div class="filetype">{$file['extension']}</div>
                <div class="img-container">
                        <span></span>
                        <img class="" src="{$icon}">
                </div>
            </div>
        </a>
        <div class="box">
            <h4 class="ellipsis">
                <a class="link"  href="{$edit_link}">{$file['file']}</a>
            </h4>
        </div>
        <div class="pcontent">
            <div class="file-name"><span class='glyphicon glyphicon-file'></span> {$file['file']}</div>
            <div class="file-date"><span class='glyphicon glyphicon-calendar'></span> {$date}</div>
            <div class='file-extension'><span class="glyphicon glyphicon-asterisk"></span> {$file['extension']}</div>
            <div class='file-extension'><span class="glyphicon glyphicon-cog"></span> {$file['permission']}</div>
        </div>
        <figcaption>
<!--            <a href="<?php echo $download; ?>" class="tt" title="Download">
                <span class="glyphicon glyphicon-download"></span>
            </a>-->
            
            <a data-path="{$this->path}" data-file="{$file['file']}" data-type="file" class="tt fcopy" title="Copy" data-act="copy">
                <span class="glyphicon glyphicon-copyright-mark"></span>
            </a>
            
            <a data-path="{$this->path}" data-file="{$file['file']}" data-type="file" class="tt fmove" title="Move" data-act="move">
                <span class="glyphicon glyphicon-move"></span>
            </a>
            
            
            <a class='fileAction tt' data-type='file' data-action='rename' data-title='Insert file name: ({$this->path}{$file['file']})' data-btn='Rename' data-file='{$file['file']}' href="" class="tip-left edit-button rename-file-paths rename" title="Rename" >
                <span class="glyphicon glyphicon-pencil"></span>
            </a>
            <a href="{$delete}" class="tt erase-button delete" title="Delete File">
                <span class="glyphicon glyphicon-trash"></span>
            </a>
        </figcaption>
        
    </figure>
</li>    

EOD;
                endif;
                
            endforeach;  
            
            $end_list =  "</ul>";
            
            echo $start_list;
            echo $folder_list;
            echo $file_list;
            echo $end_list;
            
            else:
                echo '<div class="alert alert-danger">';
                _e('Invalid Directory',TD_FILE);
                echo '...</div>';
            endif;
            
        }
        
        
        private function browseDirList(){
            
            if(is_dir($this->base_path . $this->path)):
            
            $files = scandir($this->base_path.$this->path);
            $n_files=count($files);

            //php sorting
            $sorted=array();
            $current_folder=array();
            $prev_folder=array();
            foreach($files as $k=>$file){
                $size = 0;
                if($file==".") $current_folder=array('file'=>$file);
                elseif($file=="..") $prev_folder=array('file'=>$file);
                elseif(is_dir($this->base_path.$this->path.$file)){
                    $date=filemtime($this->base_path.$this->path. $file);
                    $permissions = decoct(fileperms($this->base_path.$this->path.$file)%01000);
                    //$size =  $this->util->foldersize($this->base_path.$this->path. $file);
                    $file_ext='dir';
                    $sorted[$k]=array('file'=>$file,'date'=>$date,'size'=>$size,'extension'=>$file_ext,'permission'=>$permissions);
                }else{
                    $file_path=$this->base_path.$this->path.$file;
                    $date=filemtime($file_path);
                    $size=filesize($file_path);
                    $permissions = decoct(fileperms($this->base_path.$this->path.$file)%01000);
                    $file_ext = substr(strrchr($file,'.'),1);
                    $sorted[$k]=array('file'=>$file,'date'=>$date,'size'=>$size,'extension'=>$file_ext,'permission'=>$permissions);
                }
            }

            

            switch($this->sort_by){
                case 'name_a':
                    usort($sorted, array($this->util,'filenameSort_a'));
                    break;
                case 'name_z':
                    usort($sorted, array($this->util,'filenameSort_z'));
                    break;
                
                case 'date_a':
                    usort($sorted, array($this->util,'dateSort_a'));
                    break;
                case 'date_z':
                    usort($sorted, array($this->util,'dateSort_z'));
                    break;
                
                case 'ext_a':
                    usort($sorted, array($this->util,'extensionSort_a'));
                    break;
                case 'ext_z':
                    usort($sorted, array($this->util,'extensionSort_z'));
                    break;
                default:
                    break;

            }

            

            $files = array_merge(array($prev_folder),array($current_folder),$sorted);
            //echo "<pre>" ; print_r($files); echo "</pre>";
            
            $main = <<<EOD
<input type='hidden' name='_fm_path' id='_fm_path' value='{$this->path}'>                    
<input type='hidden' name='_fm_view' id='_fm_view' value='{$this->view}'>
<input type='hidden' name='_fm_sort' id='_fm_sort' value='{$this->sort_by}'>

<div class="table-responsive">
    <table class='table table-bordered table-striped list-view table-condensed'>
        <thead>
            <tr>
                <th class='center'><input type='checkbox' id='fm_check_all'></th>    
                <th>Name</th>
                <th>Extension</th>
                <th>Date</th>
                <th>File Permission</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
EOD;
            
            if($this->path != '') {
                $icon = WpdmFileManager::getUrl() . '/assets/img/ico/folder_back.jpg';
                $src = explode("/",  $this->path);
                if($src[0] != '') $f = true;
                unset($src[count($src)-2]);
                $src = implode("/",$src);
                if($src == '') {
                    $link= $this->old;
                    
                }
                else {
                    $old_query = $this->old;
                    $link = add_query_arg( array('path' => $src), $old_query );
                }
                
                
if($f == true):
$main .= <<< EOD
<tr>
    <td colspan='6'>
        <a class='folder-link' href="{$link}" title='back' path="$src">
            <h5><span class='glyphicon glyphicon-arrow-left'></span></h5>
        </a>
    </td>
</tr>

EOD;
endif;
            }
            
            $folder_copy = '';
            $file_copy = '';
            //print_r($_SESSION['fm_multiple']);
            foreach($files as $file):
                if(empty($file) || $file['file'] === '.' || $file['file'] === '..')
                    continue;
                $fpath = ($this->path=='')? $file['file'] : $this->path . $file['file'];
                if(isset($_SESSION['fm_multiple']) && array_key_exists($fpath, $_SESSION['fm_multiple'])) {
                    $class = 'info';
                    $checked = 'checked="checked"';
                }
                else {
                    $class = '';
                    $checked = '';
                }
                
                if($file['extension'] === 'dir'):
                    $icon = WpdmFileManager::getUrl() . '/assets/img/ico/folder.jpg';
                    $old_query = $this->old;
                    $link = add_query_arg( array('path' => $this->path . $file['file'] .'/'), $old_query );
                    $delete = add_query_arg(array('path' => $this->path, 'file' => $file['file'], 'act'=>'delete', 'type' => 'dir'),$old_query);
                    $date =  date('Y-m-d',$file['date']);
                    
                    //echo $fpath . ' ';
                    
$folder_copy .= <<<EOD
<tr class='$class'>
    <td class='center'>
        <input type='checkbox' $checked class='form-control add_remove_file' data-file='{$file['file']}' data-type='dir'/>
    </td>
    <td style='width:50%'>
        <h5><a class='folder-link' href="{$link}" path="{$this->path}{$file['file']}" data-placement="left">
            {$file['file']}
        </a></h5>
    </td>
    <td><h6>dir</h6></td>
    <td><h6>{$date}</h6></td>
    <td><h6>{$file['permission']}</h6></td>
    <td class='center'>
        <a class="tt edit-button rename-file-paths rename-folder fileAction  btn btn-default btn-xs" data-type='dir' data-action='rename' data-title='Insert Folder Name:' data-btn='Rename' title="Rename" data-file='{$file['file']}'>
            <span class="glyphicon glyphicon-pencil"></span>
        </a>
        <a href="$delete" class="tt erase-button delete-folder  btn btn-default btn-xs" title="Delete Folder">
            <span class="glyphicon glyphicon-trash"></span>
        </a>
    </td>
</tr>
EOD;
                elseif($file['extension'] !== 'dir'):
                    $extension_lower =  $this->util->fix_strtolower($file['extension']);
                    $ico = WpdmFileManager::getDir() . '/assets/img/ico/' . $extension_lower . '.jpg'; 
                    if(file_exists($ico)) {
                        $icon = WpdmFileManager::getUrl() . '/assets/img/ico/' . $extension_lower . '.jpg'; 
                    }
                    else {
                        $icon = WpdmFileManager::getUrl() . '/assets/img/ico/default.jpg'; 
                    }
                    
                    if(in_array($file['extension'], $this->editable)) {
                        $old = $this->old;
                        $edit_link = add_query_arg(array('path' => $this->path, 'file' => $file['file'],'act' => 'edit','type'=>'file'),$old);
                    }
                    else {
                        $edit_link = "javascript:void('')";
                    }
                    
                    $old = $this->old;
                    $delete = add_query_arg(array('path' => $this->path, 'file' => $file['file'], 'act'=>'delete', 'type' => 'file'),$old);
                    $download = add_query_arg(array('path' => $this->path, 'file' => $file['file'], 'act'=>'download', 'type' => 'file'),$old);
                    $download = wp_nonce_url($download, 'filemanager_nonce', 'nonce');
                    //$copy = add_query_arg(array('path' => $this->path, 'file' => $file['file'], 'act'=>'copy', 'type' => 'file'),$old);
                    //$copy = wp_nonce_url($copy,'filemanager_nonce','nonce');
                    $date = date('Y-m-d',$file['date']);
$file_copy .= <<<EOD
<tr class='$class'>
    <td class='center'><input type='checkbox' $checked class='form-control add_remove_file' data-file='{$file['file']}' data-type='file'/></td>
    <td style='width:50%'>
        <h5><a href="$edit_link">{$file['file']}</a></h5>
    </td>
    <td><h6>{$file['extension']}</h6></td>
    <td><h6>{$date}</h6></td>
    <td><h6>{$file['permission']}</h6></td>
    <td class='center'>
        <a class='fileAction tt  btn btn-default btn-xs' data-type='file' data-action='rename' data-title='Insert file name: ({$this->path}{$file['file']})' data-btn='Rename' data-file='{$file['file']}' href="" class="tip-left edit-button rename-file-paths rename" title="Rename" >
            <span class="glyphicon glyphicon-pencil"></span>
        </a>
        <a href="$delete" class="tt erase-button delete  btn btn-default btn-xs" title="Delete File">
            <span class="glyphicon glyphicon-trash"></span>
        </a>
        <a href="$download" class="tt btn btn-default btn-xs" title="Download">
            <span class="glyphicon glyphicon-download"></span>
        </a>
    </td>
</tr>    
EOD;
                endif;
                
                
            endforeach;  
            
                
            $end = "</tbody></table></div>";
            echo $main . $folder_copy . $file_copy .$end;
            
            else:
                echo '<div class="alert alert-danger">';
                _e('Invalid Directory',TD_FILE);
                echo '...</div>';
            endif;
            
        }
        
        private function getHeader(){
            ?>
            <div class="wrap wpeden" style='margin-bottom: 8em;'>
                <nav class="navbar navbar-default" role="navigation">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                      </button>
                    </div>
                    
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li>
                            <?php
                                $old = $this->old;
                                $grid = add_query_arg(array('view'=>'grid' , 'path' => $this->path),$old);
                                $list = add_query_arg(array('view'=>'list' , 'path' => $this->path),$old);
                            ?>
                            <div class="btn-group">
                                <a href="" class="disabled btn btn-default">View</a>
                                <a href="<?php echo $grid; ?>" class="btn btn-default tt view_btn <?php if($this->view == 'grid') echo 'disabled'; ?>" data-view="grid" data-toggle="tooltip" data-placement="right" title='Grid View' ><span class="glyphicon glyphicon-th"></span></a>
                                <a href="<?php echo $list; ?>" class="btn btn-default tt view_btn <?php if($this->view == 'list') echo 'disabled'; ?>" data-view="list" data-toggle="tooltip" data-placement="right" title='List View' ><span class="glyphicon glyphicon-th-list"></span></a>
                            </div>
                        </li>
                        <li>    
                            <?php
                                $old = $this->old;
                                $name_a = add_query_arg(array('path'=>  $this->path, 'sort' => 'name_a'),$old);
                                $name_z = add_query_arg(array('path'=>  $this->path, 'sort' => 'name_z'),$old);
                                $ext_a = add_query_arg(array('path'=>  $this->path, 'sort' => 'ext_a'),$old);
                                $ext_z = add_query_arg(array('path'=>  $this->path, 'sort' => 'ext_z'),$old);
                                $date_a = add_query_arg(array('path'=>  $this->path, 'sort' => 'date_a'),$old);
                                $date_z = add_query_arg(array('path'=>  $this->path, 'sort' => 'date_z'),$old);
                            ?>
                            <div class="btn-group">
                                <a href="" class="disabled btn btn-default">Filter</a>
                                <a href="<?php echo $name_a; ?>" data-sort="name_a" class="btn btn-default tt filter_btn <?php if($this->sort_by == 'name_a') echo 'disabled'; ?>" data-toggle="tooltip" data-placement="right" title='Sort By Name' ><span class=" glyphicon glyphicon-sort-by-alphabet"></span></a>
                                <a type="submit" href="<?php echo $name_z; ?>" data-sort="name_z" class="btn btn-default tt filter_btn <?php if($this->sort_by == 'name_z') echo 'disabled'; ?>" data-toggle="tooltip" data-placement="right" title='Sort By Name Descending' ><span class="glyphicon glyphicon-sort-by-alphabet-alt"></span></a>
                                <a type="submit" href="<?php echo $date_a; ?>" data-sort="date_a" class="btn btn-default tt filter_btn <?php if($this->sort_by == 'date_a') echo 'disabled'; ?>" data-toggle="tooltip" data-placement="right" title='Sort By Date' ><span class="glyphicon glyphicon-sort-by-order"></span></a>
                                <a type="submit" href="<?php echo $date_z; ?>" data-sort="date_z" class="btn btn-default tt filter_btn <?php if($this->sort_by == 'date_z') echo 'disabled'; ?>" data-toggle="tooltip" data-placement="right" title='Sort By Date Descending' ><span class="glyphicon glyphicon glyphicon-sort-by-order-alt"></span></a>
                                <a type="submit" href="<?php echo $ext_a; ?>" data-sort="ext_a" class="btn btn-default tt filter_btn <?php if($this->sort_by == 'ext_a') echo 'disabled'; ?>" data-toggle="tooltip" data-placement="right" title='Sort By Extension' ><span class="glyphicon glyphicon-sort-by-attributes"></span></a>
                                <a type="submit" href="<?php echo $ext_z; ?>" data-sort="ext_z" class="btn btn-default tt filter_btn <?php if($this->sort_by == 'ext_z') echo 'disabled'; ?>" data-toggle="tooltip" data-placement="right" title='Sort By Extension Descending' ><span class="glyphicon glyphicon-sort-by-attributes-alt"></span></a>

                            </div>
                        </li>
                        
                        <?php if($this->view === 'list'): ?>
                        <li>
                            <div class="btn-group">
                                <a href="" class="disabled btn btn-default">Clipboard</a>
                                <button class='tt btn btn-default mt_copy <?php if(!isset($_SESSION['fm_multiple'])) { echo ''; } ?>' data-toggle="tooltip" data-placement="right" title='Copy'>
                                    <span class="glyphicon glyphicon-copyright-mark"></span>
                                </button>
                                <button class='tt btn btn-default multiple mt_paste <?php if(!isset($_SESSION['fm_multiple'])) { echo 'disabled'; } ?>' data-toggle="tooltip" data-placement="right" title='Paste Here' data-task="paste">
                                    <span class="rficon-clipboard-apply"></span>
                                </button>
                                <button class='tt btn btn-default multiple mt_move <?php if(!isset($_SESSION['fm_multiple'])) { echo 'disabled'; } ?>' data-toggle="tooltip" data-placement="right" title='Move Here' data-task="move">
                                    <span class="glyphicon glyphicon-move"></span>
                                </button>
                                <button class="tt btn btn-default multiple mt_clear <?php if(!isset($_SESSION['fm_multiple'])) { echo 'disabled'; } ?>" data-toggle="tooltip" data-placement="right" title="<?php echo 'Clear Clipboard'; ?>" data-task="clear">
                                    <span class="rficon-clipboard-clear"></span>
                                </button>
                                <button class='tt btn btn-default mt_delete' data-toggle="tooltip" data-placement="right" title='Delete'>
                                    <span class="glyphicon glyphicon-trash"></span>
                                </button>
                            </div>
                        </li>
                        <?php else: ?>
                        <li>
                            <div class="btn-group">
                                <?php 
                                    $old = $this->old;
                                    $paste = add_query_arg(array('path' => $this->path, 'act' => 'paste', 'file' => $this->file),$old);
                                    $paste = wp_nonce_url($paste,'filemanager_nonce','nonce');
                                    $clear = add_query_arg(array('act' => 'clear' ),$old);
                                    $clear = wp_nonce_url($clear,'filemanager_nonce','nonce');
                                ?>
                                <a href="" class="disabled btn btn-default">Clipboard</a>
                                <a href="<?php echo $paste; ?>" class="tt btn btn-default paste-here-btn <?php if(!isset($_SESSION['filemanager_copy'])) { echo 'disabled'; } ?>" data-toggle="tooltip" data-placement="right" title="<?php echo 'Paste Here'; ?>"><i class="rficon-clipboard-apply"></i></a> 
                                <a href="<?php echo $clear; ?>" class="tt btn btn-default clear-clipboard-btn <?php if(!isset($_SESSION['filemanager_copy'])) { echo 'disabled'; } ?>" data-toggle="tooltip" data-placement="right" title="<?php echo 'Clear Clipboard'; ?>"><i class="rficon-clipboard-clear"></i></a> 
                            </div>
                        </li>
                        <?php endif; ?>
                        <li>
                            <div class="btn-group">
                                <?php 
                                $old = $this->old;
                                $folder = add_query_arg(array('path'=>  $this->path, 'action' => 'create' , 'type' =>'dir'),$old);
                                $upload = add_query_arg(array('path'=>  $this->path, 'action' => 'upload' ),$old);

                                ?>
                                <a href="" class="disabled btn btn-default">New</a>
                                <button class='fileAction tt btn btn-default' href="#" data-toggle="tooltip" data-placement="bottom" title='Create New Folder' data-type='dir' data-action='create' data-title='Insert Folder Name:' data-btn='Create'><span class="glyphicon glyphicon-folder-open"></span></button>
                                <button class="fileAction tt btn btn-default" href="#" data-toggle="tooltip" data-placement="bottom" title='Create New File' data-type='file' data-action='create' data-title='Insert File Name:' data-btn='Create'><span class="glyphicon glyphicon-file"></span></button>
                                <button class="uploadFile tt btn btn-default" href="#" data-toggle="tooltip" data-placement="bottom" title='Upload File' data-type='upload' data-action='upload' data-title='Upload File' data-btn='Upload'><span class="glyphicon glyphicon-upload"></span></button>
                            </div>
                        </li>
                        
                    </ul>
                                
                            
                    </div>
                </nav>
                    
                    
                    
                    
                    
                    <div class="row ff-container">
                        <div class="col-md-12">
                            <div id="mainMessage"></div>
                            <div id="fileList">
                
            <?php
        }
        
        private function getBreadcum(){
            ?>
            <div class="row">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li><a href="<?php echo $this->old; ?>" class="breadcum_btn" data-path=""><span class="glyphicon glyphicon-home"></span></a></li>
                        <?php 
                            if($this->path != '' || $this->path != '/'):
                                $src = explode('/', $this->path);
                                //print_r($src);
                                unset( $src[count($src) - 1]);
                                //print_r($src);
                                //$tmp = '';
                                $old = $this->old;
                                $cnt = count($src) - 1;
                                //echo 'cnt = ' . $cnt;
                                $tmp = "";
                                foreach ($src as $key => $val):
                                    //echo $key . '   ' . $val . '<br>';
                                    $tmp .= $val . '/';
                                    $link = add_query_arg(array('path'=>$tmp),$old);
                                    if($cnt == $key) {
                                        echo "<li class='active'>$val</li>";
                                    }
                                    else {
                                        echo "<li><a href='$link' class='breadcum_btn' data-path='$tmp'>$val</a></li>";
                                    }


                                endforeach;
                            endif;
                        ?>
                    </ul>
                </div>
            </div>
            <?php
        }


        private function getFooter(){
            
            
            ?>
                            </div>
<div class="modal fade" id="createFolder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel" style="text-align:left;"></h4>
      </div>
        <div class="modal-body">
            <div id='folderMsg'></div>
            <div id='modalHidden'></div>
            <input type="text" id="modalInput" class="form-control" name="name"  autocomplete="off">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-inverse" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id='folderBtn'></button>
        </div>
    </div>
  </div>
</div>

                            <div class="modal fade" id="uploadFile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title" id="myModalLabel" style="text-align:left;">Upload File</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div id='folderMsg'></div>
                                            <div id='modalHidden'></div>



                                                    <div id="upload">
                                                        <div id="plupload-upload-ui" class="hide-if-no-js">
                                                            <div id="drag-drop-area">
                                                                <div class="drag-drop-inside">
                                                                    <p class="drag-drop-info"><?php _e('Drop files here'); ?></p>
                                                                    <p><?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?></p>
                                                                    <p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php esc_attr_e('Select Files'); ?>" class="btn btn-info" /><br/> <br clear="all"/></p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php
                                                        $slimit = get_option('__wpdm_max_upload_size',0);
                                                        if($slimit>0)
                                                            $slimit = wp_convert_hr_to_bytes($slimit.'M');
                                                        else
                                                            $slimit = wp_max_upload_size();
                                                        $plupload_init = array(
                                                            'runtimes'            => 'html5,silverlight,flash,html4',
                                                            'browse_button'       => 'plupload-browse-button',
                                                            'container'           => 'plupload-upload-ui',
                                                            'drop_element'        => 'drag-drop-area',
                                                            'file_data_name'      => 'async-upload',
                                                            'multiple_queues'     => true,
                                                            'max_file_size'       => $slimit.'b',
                                                            'url'                 => admin_url('admin-ajax.php'),
                                                            'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
                                                            'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
                                                            'filters'             => array(array('title' => __('Allowed Files'), 'extensions' =>  get_option('__wpdm_allowed_file_types','*'))),
                                                            'multipart'           => true,
                                                            'urlstream_upload'    => true,

                                                            // additional post data to send to our ajax hook
                                                            'multipart_params'    => array(
                                                                'action'      => 'wpdm_fm_file_upload'            // the ajax action name
                                                            )
                                                        );

                                                        // we should probably not apply this filter, plugins may expect wp's media uploader...
                                                        $plupload_init = apply_filters('plupload_init', $plupload_init); ?>

                                                        <script type="text/javascript">
                                                            var tdir = '<?php if(isset($_GET['path'])) echo $_GET['path'];?>/',uploader;

                                                            function FM_Browse(path, sort, view){

                                                                var url = '<?php print wp_nonce_url(admin_url( 'admin-ajax.php' ),'filemanager_view_list', 'nonce'); ?>';
                                                                jQuery('#fileList').html('<img src="<?php echo WpdmFileManager::getUrl() . '/assets/ajax-loader.gif';  ?>">');

                                                                if(path === null ) {
                                                                    path = jQuery('#_fm_path').val();
                                                                }


                                                                if(view === null) {
                                                                    view = jQuery('#_fm_view').val();
                                                                }


                                                                if(sort === null ) {
                                                                    sort = jQuery('#_fm_sort').val();
                                                                }


                                                                jQuery.ajax({
                                                                    type : "get",
                                                                    url : url,
                                                                    data : {action: "filemanager_call", act:'view_list',path:path, view:view,sort:sort},
                                                                    success: function(response) {
                                                                        jQuery('#fileList').html(response);
                                                                    }
                                                                });
                                                            }

                                                            jQuery(document).ready(function($){

                                                                // create the uploader and pass the config from above
                                                                uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);
                                                                uploader.bind('BeforeUpload', function (up, file) {
                                                                    up.settings.multipart_params = {tdir: tdir, action: 'wpdm_fm_file_upload'}
                                                                });

                                                                // checks if browser supports drag and drop upload, makes some css adjustments if necessary
                                                                uploader.bind('Init', function(up){
                                                                    var uploaddiv = jQuery('#plupload-upload-ui');

                                                                    if(up.features.dragdrop){
                                                                        uploaddiv.addClass('drag-drop');
                                                                        jQuery('#drag-drop-area')
                                                                            .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
                                                                            .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

                                                                    }else{
                                                                        uploaddiv.removeClass('drag-drop');
                                                                        jQuery('#drag-drop-area').unbind('.wp-uploader');
                                                                    }
                                                                });

                                                                uploader.init();

                                                                // a file was added in the queue
                                                                uploader.bind('FilesAdded', function(up, files){
                                                                    //var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);



                                                                    plupload.each(files, function(file){
                                                                        jQuery('#filelist').append(
                                                                            '<div class="file" id="' + file.id + '"><b>' +

                                                                            file.name + '</b> (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') ' +
                                                                            '<div class="progress progress-success progress-striped active"><div class="bar fileprogress"></div></div></div>');
                                                                    });

                                                                    up.refresh();
                                                                    up.start();
                                                                });

                                                                uploader.bind('UploadProgress', function(up, file) {

                                                                    jQuery('#' + file.id + " .fileprogress").width(file.percent + "%");
                                                                    jQuery('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));
                                                                });


                                                                // a file was uploaded
                                                                uploader.bind('FileUploaded', function(up, file, response) {

                                                                    FM_Browse(response.response,'','grid');
                                                                    jQuery('#'+file.id).remove();

                                                                });

                                                            });

                                                        </script>
                                                        <div id="filelist"></div>

                                                        <div class="clear"></div>
                                                    </div>



                                            <script>
                                                jQuery(function(){


                                                    jQuery('#rmta').click(function(){
                                                        var ID = 'file_' + parseInt(Math.random()*1000000);
                                                        var file = jQuery('#rurl').val();
                                                        var filename = file;
                                                        jQuery('#rurl').val('');
                                                        if(/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/|www\.)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test(file)==false){
                                                            alert("Invalid url");
                                                            return false;
                                                        }

                                                        jQuery('#wpdm-files').dataTable().fnAddData( [
                                                            "<input type='hidden' id='in_"+ID+"' name='file[files][]' value='"+file+"' /><img id='del_"+ID+"' src='<?php echo plugins_url(); ?>/download-manager/images/minus.png' rel='del' align=left />",
                                                            file,
                                                            "<input style='width:99%' type='text' name='file[fileinfo]["+file+"][title]' value='"+filename+"' onclick='this.select()'>",
                                                            "<input size='10' type='text' id='indpass_"+ID+"' name='file[fileinfo]["+file+"][password]' value=''> <img style='cursor: pointer;float: right;margin-top: -3px' class='genpass' onclick=\"return generatepass('indpass_"+ID+"')\" title='Generate Password' src=\"<?php echo plugins_url('download-manager/images/generate-pass.png'); ?>\" />"
                                                        ] );
                                                        jQuery('#wpdm-files tbody tr:last-child').attr('id',ID).addClass('cfile');

                                                        jQuery("#wpdm-files tbody").sortable();

                                                        jQuery('#'+ID).fadeIn();
                                                        jQuery('#del_'+ID).click(function(){
                                                            if(jQuery(this).attr('rel')=='del'){
                                                                jQuery('#'+ID).removeClass('cfile').addClass('dfile');
                                                                jQuery('#in_'+ID).attr('name','del[]');
                                                                jQuery(this).attr('rel','undo').attr('src','<?php echo plugins_url(); ?>/download-manager/images/add.png').attr('title','Undo Delete');
                                                            } else if(jQuery(this).attr('rel')=='undo'){
                                                                jQuery('#'+ID).removeClass('dfile').addClass('cfile');
                                                                jQuery('#in_'+ID).attr('name','file[files][]');
                                                                jQuery(this).attr('rel','del').attr('src','<?php echo plugins_url(); ?>/download-manager/images/minus.png').attr('title','Delete File');
                                                            }


                                                        });


                                                    });

                                                });

                                            </script>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default btn-inverse" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
            </div>
            <?php    
        }
        
        private function getJs(){
            ?>
<script type="text/javascript">
    jQuery(function($){
        
        $('body').on('click', '#fm_check_all' , function () {
            $('.add_remove_file').prop('checked', this.checked);
        });
        
        $('body').on('click' , '.uploadFile' , function(){
            $('#uploadFile').modal();
        });
        $('body').on('click' , '.fileAction' , function(){
            var title = $(this).attr('data-title');
            var action = $(this).attr('data-action');
            var type = $(this).attr('data-type');
            var btn = $(this).attr('data-btn');
            var file = $(this).attr('data-file');
            $('#modalHidden').html('');
            $('#modalInput').val('');
            $('#folderMsg').html('');
            if(action != ''){
                $('#modalHidden').append('<input type="hidden" name="act" value="'+action+'">');
                if(action === 'rename') {
                    $('#modalInput').val(file);
                }
            }
            if(type != ''){
                $('#modalHidden').append('<input type="hidden" name="type" value="'+type+'">');
            }
            if(file != ''){
                $('#modalHidden').append('<input type="hidden" name="file" value="'+file+'">');
            }
            if(file === 'undefined') {
                file = '';
            }
            if(btn != ''){
                $('#folderBtn').html(btn);
            }
            
            if(title != '') {
                $('#myModalLabel').html(title);
            }
            
            $('#createFolder').modal();
            
            return false;
        });

        $('body').on('click','.folder-link', function(){

            var path = $(this).attr('path');
            uploader.bind('BeforeUpload', function (up, file) {
                up.settings.multipart_params = {tdir: path, action: 'wpdm_fm_file_upload'}
            });


        });
        
        $('body').on('click' , '#folderBtn' , function(){
            
            var action = $('#modalHidden').find('input[name="act"]').val();
            var type = $('#modalHidden').find('input[name="type"]').val();
            var name = $('#modalInput').val();
            var file = $('#modalHidden').find('input[name="file"]').val();
            
            if(name==='') {
                $('#folderMsg').html('<p>Please specify a name.</p>');
                return;
            }
            
            var url = '<?php print wp_nonce_url(admin_url( 'admin-ajax.php' ),'filemanager_nonce', 'nonce'); ?>';
            //var path = '<?php print $this->path; ?>';
            var path = $('#_fm_path').val();

            $.ajax({
                type : "get",
                dataType : "json",
                url : url,
                data : {action: "filemanager_call", path : path, act:action, type:type,name:name,file:file},
                success: function(response) {
                    if(response.type == "success") {
                        var msg = '';
                        if(response.message){
                            $.each(response.message,function(key,val){
                                msg += '<p>'+ val + '</p>';
                            });
                        }

                        if(response.error){
                            $.each(response.error,function(key,val){
                                msg += '<p>'+ val + '</p>';
                            });
                        }
                        //$('#folderMsg').html(msg);
                        if(msg)
                            $.jGrowl(msg,{header:'Notice'}); 
                            
                        if(!response.error){
                            $('#createFolder').modal('hide')
                        }

                   }
                   else {
                        $.jGrowl('<p>Ajax Request Failed</p>',{header:'Notice'});
                   }
                },complete: function(){
                        //if(task === 'paste' || task ==='move'){
                        var sort = $('#_fm_sort').val();
                        var view = $('#_fm_view').val();
                        getFM_List(path,sort,view);
                        //}
                    }
            }); 
            
        });
        
        
        
        $('.tt').tooltip();
        $('body').on('mouseover', 'figure', function(){
            $(this).find('.box:not(.no-effect)').animate({top: "-26px"} ,{queue:false,duration:300});
        });

        $('body').on('mouseout', 'figure', function(){
            $(this).find('.box:not(.no-effect)').animate({top: "0px"} ,{queue:false,duration:300});
        });
        
        function popover_make(){
            var plink = $('.plink');
            $.each(plink,function(){
                var data = $(this).parent().find('.pcontent').html();
                var title = 'File Info';
                var option = {
                    trigger: 'hover',
                    placement: 'bottom',
                    title: title,
                    html: 'true',
                    content: data
                };

                $(this).popover(option);
            });
        }
        //popover_make();
        
        //copy file and folder
        $('body').on('click' , '.fcopy,.fmove' , function(){
            var path = $(this).attr('data-path');
            var file = $(this).attr('data-file');
            var act = $(this).attr('data-act');
            var type = $(this).attr('data-type');
            var url = '<?php print wp_nonce_url(admin_url( 'admin-ajax.php' ),'filemanager_nonce', 'nonce'); ?>';
            $('#mainMessage').html('');
            $.ajax({
                type : "get",
                dataType : "json",
                url : url,
                data : {action: "filemanager_call", path : path, act:act, type:type, file:file},
                success: function(response) {
                    if(response.type == "success") {
                        var msg = '';
                        if(response.message){
                            $.each(response.message,function(key,val){
                                msg += '<p>'+ val + '</p>';
                            });
                        }

                        if(response.error){
                            $.each(response.error,function(key,val){
                                msg += '<p>'+ val + '</p>';
                            });
                        }
                        //$('#mainMessage').html(msg);
                        if(msg)
                            $.jGrowl(msg,{header:'Notice'}); 
                        
                        if(!response.error){
                            $('.paste-here-btn').removeClass('disabled');
                            $('.clear-clipboard-btn').removeClass('disabled');
                        }
                   }
                   else {
                      $('#folderMsg').html('<p>Ajax Request Failed</p>');
                   }
                }
            }); 
            
            
            return false;
        });
        
        $('body').on('click' , '.paste-here-btn' ,function(){
            
            if(confirm("Are you sure, you want to paste here ?")){
                var url = '<?php print wp_nonce_url(admin_url( 'admin-ajax.php' ),'filemanager_nonce', 'nonce'); ?>';
                //var path = '<?php print $this->path; ?>';
                var path = $('#_fm_path').val();
                var file = '<?php print $this->file; ?>';
                var act = 'paste';
                if(file === '') {
                    $('#mainMessage').html('');
                    $.ajax({
                        type : "get",
                        dataType : "json",
                        url : url,
                        data : {action: "filemanager_call", path : path, act:act},
                        success: function(response) {
                            if(response.type == "success") {
                                var msg = '';
                                if(response.message){
                                    $.each(response.message,function(key,val){
                                        msg += '<p>'+ val + '</p>';
                                    });
                                }

                                if(response.error){
                                    $.each(response.error,function(key,val){
                                        msg += '<p>'+ val + '</p>';
                                    });
                                }
                                //$('#mainMessage').html(msg);
                                if(msg)
                                    $.jGrowl(msg,{header:'Notice'}); 
                                
                                if(!response.error){
                                    $('.paste-here-btn').addClass('disabled');
                                    $('.clear-clipboard-btn').addClass('disabled');
                                }
                           }
                           else {
                              $('#folderMsg').html('<p>Ajax Request Failed</p>');
                           }
                        },
                    complete: function(){
                        //if(task === 'paste' || task ==='move'){
                            var sort = $('#_fm_sort').val();
                            var view = $('#_fm_view').val();
                            getFM_List(path,sort,view);
                        //}
                    }
                    }); 
                }
            }
            return false;
        });
        
        $('body').on('click' , '.clear-clipboard-btn' ,function(){
            if(confirm("Are you sure, you want to clear the clipboard ?")){
                var act = 'clear';
                var url = '<?php print wp_nonce_url(admin_url( 'admin-ajax.php' ),'filemanager_nonce', 'nonce'); ?>';
                $('#mainMessage').html('');
                $.ajax({
                    type : "get",
                    dataType : "json",
                    url : url,
                    data : {action: "filemanager_call", act:act},
                    success: function(response) {
                        if(response.type == "success") {
                            var msg = '';
                            if(response.message){
                                $.each(response.message,function(key,val){
                                    msg += '<p>'+ val + '</p>';
                                });
                            }

                            if(response.error){
                                $.each(response.error,function(key,val){
                                    msg += '<p>'+ val + '</p>';
                                });
                            }
                            //$('#mainMessage').html(msg);
                            if(msg)
                                $.jGrowl(msg,{header:'Notice'}); 
                                
                            if(!response.error){
                                $('.paste-here-btn').addClass('disabled');
                                $('.clear-clipboard-btn').addClass('disabled');
                            }
                       }
                       else {
                          $('#folderMsg').html('<p>Ajax Request Failed</p>');
                       }
                    }
                }); 
            }
            
            return false;
        });
        
        
        $('body').on('click' , '.mt_delete' , function(){
            //var path = '<?php echo $this->path; ?>';
            var path = $('#_fm_path').val();
            var act = 'delete_multiple';
            var files = [];
            $(':checked.add_remove_file').each(function(){
                    var file = $(this).attr('data-file');
                    var type = $(this).attr('data-type');
                    files.push({file:file,type:type});
            });
            
            if(files.length > 0){   
                //this array is not empty
                var url = '<?php print wp_nonce_url(admin_url( 'admin-ajax.php' ),'filemanager_nonce', 'nonce'); ?>';
                var msg = confirm("Are you sure, you want to delete selected files ?");
                if(!msg) return;
                $.ajax({
                    type : "get",
                    dataType : "json",
                    url : url,
                    data : {action: "filemanager_call", act:act , files:files, path:path},
                    success: function(response) {
                        if(response.type === "success") {
                            var msg = '';
                            if(response.message){
                                $.each(response.message,function(key,val){
                                    msg += '<p>'+ val + '</p>';
                                });
                            }

                            if(response.error){
                                $.each(response.error,function(key,val){
                                    msg += '<p>'+ val + '</p>';
                                });
                            }
                            
                            //$('#mainMessage').html(msg);
                            if(msg)
                                $.jGrowl(msg,{header:'Notice'}); 
                            
                            
                       }
                       else {
                          $('#folderMsg').html('<p>Ajax Request Failed</p>');
                       }
                    },
                    complete: function(){
                        //if(task === 'paste' || task ==='move'){
                            var sort = $('#_fm_sort').val();
                            var view = $('#_fm_view').val();
                            getFM_List(path,sort,view);
                        //}
                    }
                });
                
            }else{
               //this array is empty
               alert("You didn't select anything to copy");
            }
            
        });
        
        $('body').on('click' , '.mt_copy' , function(){
            //var path = '<?php echo $this->path; ?>';
            var path = $('#_fm_path').val();
            var act = 'copy_multiple';
            var files = [];
            $(':checked.add_remove_file').each(function(){
                    var file = $(this).attr('data-file');
                    var type = $(this).attr('data-type');
                    files.push({file:file,type:type});
            });
            
            if(files.length > 0){   
                //this array is not empty
                $('#mainMessage').html('');
                var url = '<?php print wp_nonce_url(admin_url( 'admin-ajax.php' ),'filemanager_nonce', 'nonce'); ?>';
                $.ajax({
                    type : "get",
                    dataType : "json",
                    url : url,
                    data : {action: "filemanager_call", act:act , files:files, path:path},
                    success: function(response) {
                        if(response.type === "success") {
                            var msg = '<p>File Successfylly Copied to Clipboard</p>';
                            if(response.message){
                                $.each(response.message,function(key,val){
                                    msg += '<p>' + val + '</p>';
                                });
                            }

                            if(response.error){
                                $.each(response.error,function(key,val){
                                    msg += '<p>'+ val + '</p>';
                                });
                            }
                            
                            if(response.file_list) {
                                msg += '<p>'+ response.file_list + '</p>';
                            }
                            
                            //$('#mainMessage').html(msg);
                            if(msg)
                                $.jGrowl(msg,{header:'Notice'}); 
                            
                            if(response.cnt && response.cnt <= 0 ){
                                $('.mt_clip,.mt_copy,.mt_move,.mt_delete,.mt_clear').addClass('disabled');
                                $('.add_remove_file').each(function(){
                                    $(this).prop('checked', false); 
                                    $(this).parent().parent().removeClass('info');
                                });
                            }
                            if(!response.error && response.cnt && response.cnt>0) {
                                $('.mt_paste,.mt_move,.mt_clear').removeClass('disabled');
                            }
                            
                       }
                       else {
                          $('#folderMsg').html('<p>Ajax Request Failed</p>');
                       }
                    }
                });
                
            }else{
               //this array is empty
               alert("You didn't select anything to copy");
            }
            
        });
        
        
        
        $('body').on('click' , '.multiple' ,function(){
            var task = $(this).attr('data-task');
            var url = '<?php print wp_nonce_url(admin_url( 'admin-ajax.php' ),'filemanager_nonce', 'nonce'); ?>';
            //var path = '<?php print $this->path; ?>';
            var path = $('#_fm_path').val();
            var file = '<?php print $this->file; ?>';
            var act = 'multiple';
            if(task === '' ) return;
            if((task === 'paste' || task === 'move' || task === 'clear') && file === '') {
                $('#mainMessage').html('');
                if(task === 'paste' && !confirm("Are you sure, you want to paste clipboard content to current directory?"))
                    return;
                if(task === 'move' && !confirm("Are you sure, you want to move clipboard content to current directory?"))
                    return;
                if(task === 'clear' && !confirm("Are you sure, you want to clear clipboard content ?"))
                    return;
                
                $.ajax({
                    type : "get",
                    dataType : "json",
                    url : url,
                    data : {action: "filemanager_call", act:act,task:task,path:path},
                    success: function(response) {
                        if(response.type === "success") {
                            var msg = '';
                            if(response.message){
                                $.each(response.message,function(key,val){
                                    msg += '<p>' + val + '</p>';
                                });
                            }

                            if(response.error){
                                $.each(response.error,function(key,val){
                                    //msg += '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+ val + '</div>';
                                    msg += '<p>' + val + '</p>';
                                });
                            }
                            if(response.file_list) {
                                //msg += '<div class="alert alert-info alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+ response.file_list + '</div>';
                                msg += '<p>' + response.file_list + '</p>';
                            }
                            
                            //$('#mainMessage').html(msg);
                            if(msg)
                                $.jGrowl(msg,{header:'Notice'}); 
                             
                            if(response.cnt <= 0 ){
                                $('.mt_move,.mt_paste,.mt_clear').addClass('disabled');
                                $(':checked.add_remove_file').each(function(){
                                    $(this).prop('checked', false); 
                                    $(this).parent().parent().removeClass('info');
                                });
                            }
                            
                       }
                       else {
                          $('#folderMsg').html('<p>Ajax Request Failed</p>');
                       }
                    },
                    complete: function(){
                        if(task === 'paste' || task ==='move'){
                            var sort = $('#_fm_sort').val();
                            var view = $('#_fm_view').val();
                            getFM_List(path,sort,view);
                        }
                    }    
                });
            }
            
        });
        
        $('body').on('click' , '.folder-link' , function(){
            var path = $(this).attr('path');
            var view = $('#_fm_view').val();
            var sort = $('#_fm_sort').val();
            getFM_List(path,sort,view);
            return false;
        });
        
        $('body').on('click' , '.filter_btn' , function(){
            var sort = $(this).attr('data-sort');
            var path = $('#_fm_path').val();
            var view = $('#_fm_view').val();
            $('.filter_btn').removeClass('disabled');
            $(this).addClass('disabled');
            getFM_List(path,sort,view);
            return false;
        });
        
        $('body').on('click', '.view_btn' , function(){
            var view = $(this).attr('data-view');
            var path = $('#_fm_path').val();
            $('.view_btn').removeClass('disabled');
            $('.filter_btn').removeClass('disabled');
            $(this).addClass('disabled');
            getFM_List(path,null,view);
            return false;
        });
        
        $('body').on('click','.breadcum_btn', function(){
            var path = $(this).attr('data-path');
            var view = $('#_fm_view').val();
            var sort = $('#_fm_sort').val();
            getFM_List(path,sort,view);
            return false;
        });
        
        function getFM_List(path, sort, view){

            var url = '<?php print wp_nonce_url(admin_url( 'admin-ajax.php' ),'filemanager_view_list', 'nonce'); ?>';
            $('#fileList').html('<img src="<?php echo WpdmFileManager::getUrl() . '/assets/ajax-loader.gif';  ?>">');

            if(path === null ) {
                path = $('#_fm_path').val();
            }


            if(view === null) {
                view = $('#_fm_view').val();
            }


            if(sort === null ) {
                sort = $('#_fm_sort').val();
            }


            $.ajax({
                type : "get",
                url : url,
                data : {action: "filemanager_call", act:'view_list',path:path, view:view,sort:sort},
                success: function(response) {
                    $('#fileList').html(response);
                }
            });
        }
    });
</script>
            <?php
        }
        private function printMessages(){
            if(!empty($this->messages)) {
                foreach ($this->messages as $key => $msg):
                    echo '<div class="alert alert-success">'.$msg.'</div>';
                endforeach;
            }
        }
    }
}

//jQuery('body').on('event','id/cls',fu...)