<?php

class DS_PRODUCT_TYPE {
    
    // SETTINGS
    // --------------------------------------------------------------------
    private $id;
    private $name;
    private $fullname;
    private $baseprice;
    private $class;
    private $settings;
    
    // KONSTRUKTOR
    // --------------------------------------------------------------------
    function __construct($id=null,$name=null,$wcID=null) {
        $this->id = 0;
        $this->name = "";
        $this->fullname = "";
        $this->baseprice = 0.0;
        $this->class = "";
        $this->wcID = $wcID;
        $this->settings = array();
        // Load given type data
        if($id !== null && $wcID !== null) {
            $this->setTypeByID($id, $wcID);
        } elseif($name !== null) {
            $this->setTypeByName($name);
        }
    }

    function getWcID() {
        return $this->wcID;
    }

    // SET DATA
    // --------------------------------------------------------------------
    function setTypeByID($id, $wcID) {
        global $DSDB;
        $id = (int)$id;
        $qry = $DSDB->query("SELECT * FROM ".DS_TABLE_PRODUCT_TYPE." WHERE id='".$id."' LIMIT 0,1");
        if($qry) {
            $row = $qry->fetch_assoc();
            $this->setID($row['id']);
            $this->setClass($row['class']);
            $this->loadSettings();
        } else {
            DS_LOGS::addEntry("PRODUCT TYPE: Eintrag mit ID ".$id." nicht gefunden!");
            return false;
        }

        # todo query wp
        $wcQry = $DSDB->query("SELECT * FROM b1mNf_postmeta WHERE meta_key='web2print_version_id' AND post_id=" . $wcID . " AND meta_value='" . $id ."' LIMIT 0,1");
        $postQry = $DSDB->query("SELECT * FROM b1mNf_posts WHERE ID=" . $wcID . " LIMIT 0,1");
        $priceQry = $DSDB->query("SELECT * FROM b1mNf_postmeta WHERE meta_key='_price' AND post_id=" . $wcID . " LIMIT 0,1");
        if($wcQry && $postQry && $priceQry) {
            $post = $postQry->fetch_assoc();
            $price = $priceQry->fetch_assoc();

            $this->wcID = $wcID;
            $this->setName($post['post_title']);
            $this->setFullname($post['post_title']);
            $this->setBaseprice($price['meta_value']);
            return true;
        } else {
            DS_LOGS::addEntry("PRODUCT TYPE: Woocommerce-Eintrag mit ID ".$wcID." nicht gefunden!");
            return false;
        }
    }
    
    /*function setTypeByName($name) {
        global $DSDB;
        $qry = $DSDB->query("SELECT * FROM ".DS_TABLE_PRODUCT_TYPE." WHERE name='".mysql_real_escape_string($name)."' LIMIT 0,1");
        if($qry) {
            $row = $qry->fetch_assoc();
            $this->setID($row['id']);
            $this->setName($row['name']);
            $this->setFullname($row['fullname']);
            $this->setBaseprice($row['baseprice']);
            $this->setClass($row['class']);
            $this->loadSettings();
            return true;
        } else {
            DS_LOGS::addEntry("PRODUCT TYPE: Eintrag mit Name ".$name." nicht gefunden!");
            return false;
        }
    }*/
    
    private function loadSettings() {
        global $DSDB;
        $typeID = (int)$this->id;
        $qry = $DSDB->query("SELECT setting_name, setting_value FROM ".DS_TABLE_PRODUCT_SETTINGS." WHERE product_type='".$typeID."'");
        if($qry) {
            $settings = array();
            while($row = $qry->fetch_assoc()) {
                if($row['setting_name'] && $row['setting_value']) {
                    $settings[ $row['setting_name'] ] = $row['setting_value'];
                } else {
                    DS_LOGS::addEntry("PRODUCT TYPE: Fehlerhafte Einstellung (".$row['setting_name']." : ".$row['setting_value'].")!");
                }
            }
            $this->settings = $settings;
            return true;
        } else {
            DS_LOGS::addEntry("PRODUCT TYPE: Einstellungen konnten nicht geladen werden!");
            return false;
        }
    }
    function setID($id) {
        $this->id = (int)$id;
        return true;
    }
    function setName($name) {
        $this->name = $name;
        return true;
    }
    function setFullname($fullname) {
        $this->fullname = $fullname;
        return true;
    }
    function setBaseprice($baseprice) {
        if(is_numeric($baseprice)) {
            $this->baseprice = $baseprice;
            return true;
        } 
        else {
            return false;
        }
    }
    function setClass($class) {
        $this->class = $class;
        return true;
    }
    
    // GET DATA
    // --------------------------------------------------------------------
    function getID() {
        return $this->id;
    }
    function getName() {
        return $this->name;
    }
    function getFullname() {
        return $this->fullname;
    }
    function getBaseprice() {
        return $this->baseprice;
    }
    function getClass() {
        return $this->class;
    }
    function getSettings() {
        return $this->settings;
    }
    function getSetting($settingName) {
        return $this->settings[$settingName];
    }
    
}

?>
