<?php

class DS_MATERIAL {
    
    // SETTINGS
    // --------------------------------------------------------------------
    
    private $MID;
    private $nr;
    private $file;
    private $position;
    private $zoom;
    private $angle;
    private $offset;
    private $options;
    
    private $type;
    
    // CONSTRUCTOR
    // --------------------------------------------------------------------
    
    function __construct($id=null) {
        $this->MID = 0;
        $this->nr = 0;
        $this->file = "";
        $this->position = 0;
        $this->zoom = 100;
        $this->angle = 0;
        $this->offset = array('0'=>0.0,'1'=>0.0);
        $this->type = "";
        $this->options = array();
        // Load Material
        if($id !== null) {
            $this->getMaterialByID($id);
        }
    }
    
    
    // GET DATA FROM DATABASE
    // --------------------------------------------------------------------
    
    function getMaterialByID($id) {
        return false;
    }
    
    
    // GET DATA
    // --------------------------------------------------------------------
    
    function getMID() {
        return $this->MID;
    }
    
    function getNr() {
        return $this->nr;
    }
    
    function getFile() {
        return $this->file;
    }
    
    function getPosition() {
        return $this->position;
    }
    
    function getZoom() {
        return $this->zoom;
    }

    function getAngle() {
        return $this->angle;
    }

    function setAngle($angle) {
        $this->angle = (int)$angle;
    }
    
    function getOffset() {
        return $this->offset;
    }
    
    function getOptions() {
        return $this->options;
    }

    function getType() {
        return $this->type;
    }

    function serialize() {
        $output = array(
            'MID' => $this->MID,
            'nr' => $this->nr,
            'file' => $this->file,
            'position' => $this->position,
            'angle' =>$this->angle,
            'zoom' => $this->zoom,
            'offset' => $this->offset,
            'options' => $this->options,
            'type' => $this->type
        );
        return $output;
    }
    
    
    // SET DATA
    // --------------------------------------------------------------------
    
    function setMaterial($file=null,$position=null,$zoom=null,$offset_x=null,$offset_y=null,$angle=null,$options=null,$type=null) {
        if($file!==null) $this->setFile($file);
        if($position!==null) $this->setPosition($position);
        if($zoom!==null) $this->setZoom($zoom);
        if($angle!==null) $this->setAngle($angle);
        if($offset_x!==null && $offset_y!==null) $this->setOffset($offset_x,$offset_y);
        if($options!==null) $this->setOptions($options);
        if($type!==null) $this->setOptions($type);
    }
    
    function setMID($MID) {
        $this->MID = (int)$MID;
    }
    
    function setNr($nr) {
        $this->nr = (int)$nr;
    }
    
    function setFile($file) {
        $this->file = $file;
        return true;
    }
    
    function setPosition($position) {
        $this->position = (int)$position;
    }
    
    function setZoom($zoom) {
        if(is_numeric($zoom)) {
            $this->zoom = $zoom;
            return true;
        }
        return false;
    }
    
    function setOffset($x,$y) {
        if(is_numeric($x) && is_numeric($y)) {
            $this->offset = array(
                    '0' => $x,
                    '1' => $y
            );
            return true;
        }
        return false;
    }
    
    function setOptions($options) {
        $this->options = $options;
        return true;
    }

    function setType($options) {
        $this->type = $type;
        return true;
    }
    
}

?>
