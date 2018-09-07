<?php

if(!class_exists('EdenFileManagerUtlity')) {
    class EdenFileManagerUtlity{
        public function __construct() {
            ;
        }
        
        function deleteDir($dir) {
            if (!file_exists($dir)) return true;
            if (!is_dir($dir)) return unlink($dir);
            foreach (scandir($dir) as $item) {
                if ($item == '.' || $item == '..') continue;
                if (!$this->deleteDir($dir.DIRECTORY_SEPARATOR.$item)) return false;
            }
            return rmdir($dir);
        }

        function duplicate_file($old_path,$name){
            if(file_exists($old_path)){
                $info=pathinfo($old_path);
                $new_path=$info['dirname']."/".$name.".".$info['extension'];
                if(file_exists($new_path)) return false;
                return copy($old_path,$new_path);
            }
        }

        function rename_file($old_path,$name,$transliteration){
            $name=  $this->fix_filename($name,$transliteration);
            if(file_exists($old_path)){
                $info=pathinfo($old_path);
                $new_path=$info['dirname']."/".$name.".".$info['extension'];
                if(file_exists($new_path)) return false;
                return rename($old_path,$new_path);
            }
        }

        function rename_folder($old_path,$name,$transliteration){
            $name=  $this->fix_filename($name,$transliteration);
            if(file_exists($old_path)){
                $new_path=  $this->fix_dirname($old_path)."/".$name;
                if(file_exists($new_path)) return false;
                return rename($old_path,$new_path);
            }
        }
        
        function makeSize($size) {
           $units = array('B','KB','MB','GB','TB');
           $u = 0;
           while ( (round($size / 1024) > 0) && ($u < 4) ) {
             $size = $size / 1024;
             $u++;
           }
           return (number_format($size, 0) . " " . $units[$u]);
        }

        function foldersize($path) {
            $total_size = 0;
            $files = scandir($path);
            $cleanPath = rtrim($path, '/'). '/';

            foreach($files as $t) {
                if ($t != "." && $t != "..") {
                    $currentFile = $cleanPath . $t;
                    if (is_dir($currentFile)) {
                        $size = $this->foldersize($currentFile);
                        $total_size += $size;
                    }
                    else {
                        $size = filesize($currentFile);
                        $total_size += $size;
                    }
                }   
            }

            return $total_size;
        }

        function filescount($path) {
            $total_count = 0;
            $files = scandir($path);
            $cleanPath = rtrim($path, '/'). '/';

            foreach($files as $t) {
                if ($t != "." && $t != "..") {
                    $currentFile = $cleanPath . $t;
                    if (is_dir($currentFile)) {
                        $size = $this->filescount($currentFile);
                        $total_count += $size;
                    }
                    else {
                        $total_count += 1;
                    }
                }   
            }

            return $total_count;
        }

        function create_folder($path=false){
            $oldumask = umask(0);
            if ($path && !file_exists($path))
                mkdir($path, 0777, true); // or even 01777 so you get the sticky bit set 
            umask($oldumask);
        }

        function check_files_extensions_on_path($path,$ext){
            if(!is_dir($path)){
                $fileinfo = pathinfo($path);
                if(!in_array(mb_strtolower($fileinfo['extension']),$ext))
                    unlink($path);
            }else{
                $files = scandir($path);
                foreach($files as $file){
                    $this->check_files_extensions_on_path(trim($path,'/')."/".$file,$ext);
                }
            }
        }

        function check_files_extensions_on_phar( $phar, &$files, $basepath, $ext ) {
            foreach( $phar as $file )
            {
                if( $file->isFile() )
                {
                    if(in_array(mb_strtolower($file->getExtension()),$ext))
                    {
                        $files[] = $basepath.$file->getFileName( );
                    }
                }
                else if( $file->isDir() )
                {
                    $iterator = new DirectoryIterator( $file );
                    $this->check_files_extensions_on_phar($iterator, $files, $basepath.$file->getFileName().'/', $ext);
                }
            }
        }

        function fix_get_params($str){
            return strip_tags(preg_replace( "/[^a-zA-Z0-9\.\[\]_| -]/", '', $str));
        }

        function fix_filename($str,$transliteration){
            if($transliteration){
                if( function_exists( 'transliterator_transliterate' ) )
                {
                   $str = transliterator_transliterate( 'Accents-Any', $str );
                }
                else
                {
                   $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
                }

                $str = preg_replace( "/[^a-zA-Z0-9\.\[\]_| -]/", '', $str );
            }

            $str=str_replace(array('"',"'","/","\\"),"",$str);
            $str=strip_tags($str);

            // Empty or incorrectly transliterated filename.
            // Here is a point: a good file UNKNOWN_LANGUAGE.jpg could become .jpg in previous code.
            // So we add that default 'file' name to fix that issue.
            if( strpos( $str, '.' ) === 0 )
            {
               $str = 'file'.$str;
            }

            return trim( $str );
        }

        function fix_dirname($str){
            return str_replace('~',' ',dirname(str_replace(' ','~',$str)));
        }

        function fix_strtoupper($str){
            if( function_exists( 'mb_strtoupper' ) )
                return mb_strtoupper($str);
            else
                return strtoupper($str);
        }


        function fix_strtolower($str){
            if( function_exists( 'mb_strtoupper' ) )
                return mb_strtolower($str);
            else
                return strtolower($str);
        }

        function fix_path($path,$transliteration){
            $info=pathinfo($path);
            $tmp_path = $info['dirname'];
                $str =  $this->fix_filename($info['filename'],$transliteration);
            if($tmp_path!="")
                        return $tmp_path.DIRECTORY_SEPARATOR.$str;
            else
                        return $str;
        }
        
        // test for dir/file writability properly
        function is_really_writable($dir){
            $dir = rtrim($dir, '/');
            // linux, safe off
            if (DIRECTORY_SEPARATOR == '/' && @ini_get("safe_mode") == FALSE){
                return is_writable($dir);
            }

            // Windows, safe ON. (have to write a file :S)
            if (is_dir($dir)){
                $dir = $dir.'/'.md5(mt_rand(1,1000).mt_rand(1,1000));

                if (($fp = @fopen($dir, 'ab')) === FALSE){
                    return FALSE;
                }

                fclose($fp);
                @chmod($dir, 0777);
                @unlink($dir);
                return TRUE;
            }
            elseif ( ! is_file($dir) || ($fp = @fopen($dir, 'ab')) === FALSE){
                return FALSE;
            }

            fclose($fp);
            return TRUE;
        }
        
        function endsWith($haystack, $needle)
        {
            return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
        }
        
        // recursivly copies everything
        function rcopy($source, $destination, $is_rec = FALSE) {
            if (is_dir($source)) {
                if ($is_rec === FALSE){
                    $pinfo = pathinfo($source);
                    $destination = rtrim($destination, '/').DIRECTORY_SEPARATOR.$pinfo['basename'];
                }
                if (is_dir($destination) === FALSE){
                    mkdir($destination, 0777, true);
                }

                $files = scandir($source);
                foreach ($files as $file){
                    if ($file != "." && $file != "..") {
                        $this->rcopy($source.DIRECTORY_SEPARATOR.$file, rtrim($destination, '/').DIRECTORY_SEPARATOR.$file, TRUE);
                    }
                }
            }
            else {
                if (file_exists($source)){
                    if (is_dir($destination) === TRUE){
                        $pinfo = pathinfo($source);
                        $dest2 = rtrim($destination, '/').DIRECTORY_SEPARATOR.$pinfo['basename'];
                    }
                    else {
                        $dest2 = $destination;
                    }

                    copy($source, $dest2);
                }
            }
        }
        
        function recurse_copy($src,$dst) { 
            $dir = opendir($src); 
            @mkdir($dst); 
            while(false !== ( $file = readdir($dir)) ) { 
                if (( $file != '.' ) && ( $file != '..' )) { 
                    if ( is_dir($src . '/' . $file) ) { 
                        recurse_copy($src . '/' . $file,$dst . '/' . $file); 
                    } 
                    else { 
                        copy($src . '/' . $file,$dst . '/' . $file); 
                    } 
                } 
            } 
            closedir($dir); 
        }
        
        function filenameSort_z($x, $y) {
            return $this->fix_strtolower($x['file'][0]) < $this->fix_strtolower($y['file'][0]);
        }
        function filenameSort_a($x, $y) {
            return $this->fix_strtolower($x['file'][0]) > $this->fix_strtolower($y['file'][0]);
        }
        function dateSort_z($x, $y) {
            return $x['date'] <  $y['date'];
        }
        function dateSort_a($x, $y) {
            return $x['date'] >  $y['date'];
        }
        function sizeSort($x, $y) {
            return $x['size'] -  $y['size'];
        }
        function extensionSort_z($x, $y) {
            //if($x['extension'] == 'dir' || $y['extension']) return false;
            if($x['extension'] == $y['extension']) {
                return $this->fix_strtolower($x['file'][0]) < $this->fix_strtolower($y['file'][0]);
            }
            return $x['extension'] <  $y['extension'];
        }
        function extensionSort_a($x, $y) {
            //if($x['extension'] == 'dir' || $y['extension']) return false;
            if($x['extension'] == $y['extension']) {
                return $this->fix_strtolower($x['file'][0]) > $this->fix_strtolower($y['file'][0]);
            }
            return $x['extension'] >  $y['extension'];
        }
    }
    
    
}

