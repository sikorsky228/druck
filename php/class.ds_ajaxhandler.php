<?php

class DS_AjaxHandler {
    
    // SETTINGS
    private $data;
    
    // KONSTRUKTOR
    function __construct() {
        $this->load();
    }
    
    // LOAD JSON DATA
    function load() {
        if(array_key_exists('DS_AJAX_DATA', $_REQUEST)) {
            $this->data = $_REQUEST['DS_AJAX_DATA'];
            return true;
        }
        return false;
    }
    
    // HANDLE DATA
    function setData($data) {
        if($data && is_array($data)) {
            $this->data = $data;
            return true;
        }
        return false;
    }
    function getData() {
        return $this->data;
    }
    
    // SHOW JSON DATA
    function getJSON() {
        header('Content-Type: application/json; charset=utf-8');
        exit( $this->encode($this->data) );
    } 
    
    // ENCODE
    function encode($data) {
        return json_encode($data);
    }
    
    // DECODE
    function decode($data) {
        return json_decode($data,true);
    }
    
}

?>
