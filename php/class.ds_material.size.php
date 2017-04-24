<?php

class DS_MATERIAL_SIZE {
    
    private $data = array();
    
    // Instanzierung zur Auslesung einer Grφίe
    function __construct1($id=null, $name=null) {
        $id = (int)$id;
        $name = mysqli_escape_string(strip_tags($name));
        if($id > 0) {
            $this->getSizeByID($id);
        }
        else if(!is_empty($name)) {
            $this->getSizeByName($name);
        }
    }
    
    // Instanzierung zum schnellen Anlegen neuer Grφίen in der Datenbank
    function __construct2($id=null, $name, $width, $height) {
        $this->setSize($id,$name,$width,$height);
        $this->save();
    }
    
    // Grφίe aus der Tabelle anhand der ID ermitteln und ausgeben
    function getSizeByID($id) {
        global $DSDB;
        $id = (int)$id;
        $qry = $DSDB->query("SELECT * FROM ".DS_TABLE_MATERIAL_SIZES." WHERE id=".$id." LIMIT 1");
        if($qry) {
            $row = $qry->fetch_assoc();
            $this->setSize($row['id'],$row['name'],$row['width'],$row['height']);
            return $this->getSize();
        } else {
            DS_LOGS::addEntry('MATERIAL SIZE: Eintrag mit ID '.$id.' nicht gefunden!');
            return false;
        }
    }
    
    // Grφίe aus der Tabelle anhand des Namen ermitteln und ausgeben
    function getSizeByName($name) {
        global $DSDB;
        $name = mysqli_escape_string(strip_tags($name));
        $qry = $DSDB->query("SELECT * FROM ".DS_TABLE_MATERIAL_SIZES." WHERE name=".$name." LIMIT 1");
        if($qry) {
            $row = $qry->fetch_assoc();
            $this->setSize($row['id'],$row['name'],$row['width'],$row['height']);
            return $this->getSize();
        } else {
            DS_LOGS::addEntry('MATERIAL SIZE: Eintrag mit Name '.$name.' nicht gefunden!');
            return false;
        }
    }
    
    // Grφίe ausgeben
    function getSize() {
        return $this->data;
    }
    
    // Daten der Grφίe umschreiben
    function setSize($id=null, $name=null, $width=null, $height=null) {
        $id = (int)$id;
        $name = mysqli_escape_string(strip_tags($name));
        $width = (int)$width;
        $height = (int)$height;
        if($id>0) {
            $this->data['id'] = $id;
        }
        if(!is_empty($name)) {
            $this->data['name'] = $name;
        }
        if($width>0) {
            $this->data['width'] = $width;
        }
        if($height>0) {
            $this->data['height'] = $height;
        }
    }
    
    // Grφίe lφschen
    function remove($id) {
        global $DSDB;
        $id = (int)$id;
        $qry = $DSDB->query("DELETE FROM ".DS_TABLE_MATERIAL_SIZES." WHERE id=".$id);
        if($qry) {
            $qry->close();
            return true;
        } else {
            $qry->close();
            DS_LOGS::addEntry('MATERIAL SIZE: ('.$DSDB->errno.') '.$DSDB->error);
            return false;
        }
    }
    
    // Grφίe in Datenbank speichern
    function save() {
        global $DSDB;
        // Daten prόfen
        $id = (int)$this->id;
        $name = mysqli_escape_string(strip_tags($this->name));
        $width = (int)$this->width;
        $height = (int)$this->height;
        // Vorhandenen Eintrag updaten
        $qry = $DSDB->query("SELECT id FROM ".DS_TABLE_MATERIAL_SIZES." WHERE id=".$this->id);
        $count = $qry->numRows();
        if($count == 1) {
            $qry = $DSDB->query("UPDATE ".DS_TABLE_MATERIAL_SIZES." SET name='".$name."', width=".$width.", height=".$height." WHERE id=".$this->id);
        }
        // Neuen Eintrag einstellen
        else {
            $qry = $DSDB->query("INSERT INTO ".DS_TABLE_MATERIAL_SIZES." (name,width,height) VALUES ('".$name."',".$width.",".$height.")");
        }
        // Auswertung der Query
        if($qry) {
            $qry->close();
            return true;
        } else {
            $qry->close();
            DS_LOGS::addEntry('MATERIAL SIZE: ('.$DSDB->errno.') '.$DSDB->error);
            return false;
        }
    }
}

?>