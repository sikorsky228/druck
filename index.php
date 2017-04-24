<?php

#ini_set('display_errors', 'On');

//date_default_timezone_set('Europe/Berlin');

use MetzWeb\Instagram\Instagram;

// INDEX MARK
define('DS_INDEX_CHECK','DS_LOADED');

// Session starten
session_start();

// Daten laden
require_once("settings.php");
require_once("php/functions.php");
require_once("php/class.ds_logs.php");
require_once("php/class.ds_ajaxhandler.php");
require_once("php/class.ds_material.php");
require_once("php/class.ds_product_type.php");
require_once("php/class.ds_product.php");
require_once("php/class.ds_product.vintage_12.php");
require_once("php/class.ds_product.ministripes.php");
require_once("php/class.ds_manager.file.php");
require_once("php/class.ds_manager.instagram.php");
require_once("php/class.ds_manager.php");
require_once("php/class.druckstudio.php");
require_once("php/lib/MetzWeb/Instagram.php");

// Datenbank
$DSDB = new mysqli(DS_DB_HOST,DS_DB_USER,DS_DB_PASS,DS_DB_NAME);

// Instagram-Lib starten
$instagram = new Instagram(array(
		'apiKey'      => DS_INST_KEY,
		'apiSecret'   => DS_INST_SECRET,
		'apiCallback' => DS_INST_CALLBACK,
));
// Druckstudio starten
$druckstudio = new druckstudio();

// Catch Instagram Code

$code = $_GET["code"];
if(isset($code)) {
	try {
	$data = $instagram->getOAuthToken($code);
	$instagram_manager = new DS_MANAGER_INSTAGRAM($instagram, $data);
	$druckstudio->getManager()->setInstagram($instagram_manager);
	$druckstudio->saveData();
	} catch(Exception $e) {
		//Wrong code parameter
	}
}

// AJAX REQUEST
if(array_key_exists('DS_AJAX_REQUEST', $_REQUEST) && array_key_exists('DS_AJAX_URL', $_REQUEST)) {
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
    // Template Anzeige �ffnen
    if(!$druckstudio->getError()) {
        $tempFile = $druckstudio->getProduct()->getTemplate()."index.php";
        if(is_file($tempFile)) {
            include $tempFile;
        }
        else {
            include "404.php";
        }
    } else {
        $druckstudio->show();
    }
    
    // Logs anzeigen
    require_once("views/general/logs.php");
}

// Close connection
$DSDB->close();

?>
