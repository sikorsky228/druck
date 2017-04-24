<?php

class DS_LOGS {
    
    private $log;
    
    function __construct() {
        $this->log = array(
            'default'=>array()
        );
    }
    
    function loadRequestLogs() {
        if(is_array($_SESSION['ds_logs'])) {
            $this->log = array_merge($this->log,$_SESSION['ds_logs']);
            $this->log = array_filter($this->log);
        }
        return $this->getLog();
    }
    
    function addEntry($str,$group=null) {
        $group = (!$group) ? 'default' : $group;
        if(!is_array($_SESSION['ds_logs'])) {
            $_SESSION['ds_logs'] = array(
                'default' => array()
            );
        }
        if(!is_array($_SESSION['ds_logs'][$group])) {
            $_SESSION['ds_logs'][$group] = array();
        }
        $_SESSION['ds_logs'][$group][] = $str;
    }
    
    function getEntry($entry,$group=null) {
        $group = (!$group) ? 'default' : $group;
        if(!$this->log[$group][$entry]) return false;
        return $this->log[$group][$entry];
    }
    
    function addLog($logEntry) {
        if(is_array($logEntry)) {
            $this->log = array_merge($this->log,$logEntry);
            $this->log = array_filter($this->log);
        }
        elseif(!is_empty($logEntry)) {
            $this->log[] = $logEntry;
        }
    }
    
    function getLog($group=null) {
        if($group && array_key_exists($group, $this->log) && is_array($this->log[$group])) {
            return $this->log[$group];
        }
        elseif($group) {
            return array();
        }
        else {
            return $this->log;
        }
    }
    
    function clearLog($group=null) {
        if($group) {
            unset($_SESSION['ds_logs'][$group]);
        }
        else {
            $_SESSION['ds_logs'] = array();
        }
    }
    
    function saveLog() {
        $_SESSION['ds_logs'] = $this->log;
    }
    
}

?>
