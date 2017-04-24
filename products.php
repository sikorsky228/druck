<?php

// INDEX MARK
define('DS_INDEX_CHECK','DS_LOADED');

// Session starten
session_start();

// Daten laden
require_once("settings.php");
require_once("php/class.ds_logs.php");
require_once("php/class.ds_ajaxhandler.php");

// Datenbank
$DSDB = new mysqli(DS_DB_HOST,DS_DB_USER,DS_DB_PASS,DS_DB_NAME);

// AJAX REQUEST
if(array_key_exists('DS_AJAX_REQUEST', $_REQUEST) && $_REQUEST['DS_AJAX_REQUEST'] && $_REQUEST['DS_AJAX_URL']) {
    $DS_ajax = new DS_AjaxHandler();
    if(@is_file($_REQUEST['DS_AJAX_URL'])) {
        include $_REQUEST['DS_AJAX_URL'];
        $DS_ajax->setData($return);
        $DS_ajax->getJSON();
    } else {
        $DS_ajax->setData(array(
            'status' => array(
                'type' => 'error',
                'value' => 'AJAX: DATEI ('.$_REQUEST['DS_AJAX_URL'].') NICHT VORHANDEN!'
            )
        ));
        $DS_ajax->getJSON();
    }
}
    
// NORMAL REQUEST
else {
    // Produktübersicht
    $tempFile = "views/products-manager.php";
    if(is_file($tempFile)) {
        include $tempFile;
    }
    else {
        include "404.php";
    }
    
    // Logs anzeigen
    require_once("views/general/logs.php");
}

// Close connection
$DSDB->close();

?>
