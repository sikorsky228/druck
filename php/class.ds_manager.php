<?php

class DS_MANAGER {
    
    // EIGENSCHAFTEN
    // --------------------------------------------------------------------
    private $uploader;
    private $instagram;
    private $settings;
    
    // KONSTRUKTOR
    // --------------------------------------------------------------------
    function __construct() {
        $this->uploader = new DS_MANAGER_UPLOADER();
        //$this->instagram = new DS_MANAGER_INSTAGRAM();
    }
    
    // GET MANAGER DATA
    // --------------------------------------------------------------------
    function getUploader() {
        return $this->uploader;
    }
    function getInstagram() {
        return $this->instagram;
    }
    function getSettings() {
        return $this->settings;
    }
    function serialize() {
        $data = array(
            'UPLOADER' => $this->uploader->serialize(),
        );
        if(isset($this->instagram))
        	$data['INSTAGRAM'] = $this->instagram->serialize();
        return $data;
    }
    function setInstagram($instagram_manager) {
    	$this->instagram = $instagram_manager;
    }
    
}

?>