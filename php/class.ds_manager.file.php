<?php

class DS_MANAGER_UPLOADER {
    
    // SETTINGS
    // --------------------------------------------------------------------
    private $path;
    private $files;
    private $settings;
    
    // THUMBNAIL SETTINGS
    // --------------------------------------------------------------------
    private $thumb_size = array(200,200);
    
    // INIT
    // --------------------------------------------------------------------
    function __construct($path=null) {
        $this->path = "";
        $this->files = array();
        $this->settings = array();
        // Set given path directly
        if($path !== null) {
            $this->setPath($path);
        }
    }
    
    // GET DATA
    // --------------------------------------------------------------------
    function getPath() {
        return $this->path;
    }
    
    function getFiles() {
        return $this->files;
    }
    
    function getFileURL($fileName) {
        return $this->path.$fileName;
    }
    
    function getSettings() {
        return $this->settings;
    }
    
    function getThumbSize($sizeType) {
        if($sizeType==0 || $sizeType==1) {
            return $this->thumb_size[$sizeType];
        }
        else {
            return $this->thumb_size;
        }
    }
    
    function hasFile($file) {
        if($file) {
            $fileName = basename($file);
            if(file_exists($this->path.$fileName)) {
                // Check in materials
                foreach($this->files as $iFile) {
                    $iFileName = basename($iFile['name']);
                    if($fileName == $iFileName) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    function serialize() {
        $data = array(
            'FILES' => $this->files
        );
        return $data;
    }
    
    // SET DATA
    // --------------------------------------------------------------------
    function setPath($path) {
        if(file_exists($path)) {
            $this->path = $path;
            return true;
        }
        else {
            return false;
        }
    }

    function addFile($fileName,$manualUpload = true) {
        $file = $this->getFileURL($fileName);
        if(file_exists($file)) {
            $this->files[] = array('name'=>$fileName,'checked'=>false, 'manualUpload' => $manualUpload);
            return true;
        }
        else {
            return false;
        }
    }
    
    function removeFile($fileNr) {
        if($this->files[$fileNr]) {
            $file = $this->getFileURL( $this->files[$fileNr]['name'] );
            if(unlink($file)) {
                unset($this->files[$fileNr]);
                return true;
            } else {
                return false;
            }
        }
        else {
            return false;
        }
    }
    
    function removeFileByName($file) {
        $checksum = !empty($file);
        if($checksum) {
            $fileName = basename($file);
            foreach($this->files as $index=>$iFile) {
                $iFileName = basename($iFile['name']);
                if($fileName == $iFileName) {
                    $checksum *= $this->removeFile($index);
                }
            }
        }
        return $checksum;
    }
    
    // SHOWS NEXT NUMBER FROM FILESYSTEM
    // --------------------------------------------------------------------
    function newFileNr() {
        $path = $_SERVER["DOCUMENT_ROOT"].DS_PATH_BASE.$this->path;
        if(file_exists($path)) {
            $files = scandir($path);
            $files = preg_grep("/^[0-9]+\.(.){2,5}$/",$files);
            $max = (@count($files)>0) ? max($files) + 1 : 1;
            if($max<=1) $max = 1;
            $fileName = str_pad($max,5,'0',STR_PAD_LEFT);
            return $fileName;
        }
        return false;
    }
    
}

?>