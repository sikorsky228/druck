<?php

// INDEX MARK
define('DS_INDEX_CHECK','DS_LOADED');

// SERVER SETTINGS
ini_set ('max_execution_time','600');
ini_set ('max_file_uploads','50');
ini_set ('post_max_size','15M');
ini_set ('upload_max_filesize','15M');

// Session starten
session_start();

// Daten laden
require_once("settings.php");
require_once("lib/fpdf/fpdf.php");
require_once("php/functions.php");
require_once("php/class.ds_logs.php");
require_once("php/class.ds_ajaxhandler.php");
require_once("php/class.ds_material.php");
require_once("php/class.ds_product_type.php");
require_once("php/class.ds_product.php");
require_once("php/class.ds_product.squares.php");
require_once("php/class.ds_product.ministripes.php");
require_once("php/class.ds_product.vintage_12.php");
require_once("php/class.ds_manager.file.php");
require_once("php/class.ds_manager.instagram.php");
require_once("php/class.ds_manager.php");
require_once("php/class.druckstudio.php");

// Datenbank
$DSDB = new mysqli(DS_DB_HOST,DS_DB_USER,DS_DB_PASS,DS_DB_NAME);

// Druckstudio starten
$druckstudio = new druckstudio(true);

// Check if Product exists
$PID = (int)$_GET['PID'];
if($druckstudio->productExists($PID)) {
    $product = $druckstudio->getProduct();
    // Product checkup
    if($product->checkProduct()) {
        // Show print version
        if(!$product->showPrint()) {
            $logs = new DS_LOGS();
            $logs->loadRequestLogs();
            echo "<pre>";
            print_r($logs);
            echo "</pre>";
            $logs->clearLog();
        }
    }
    // 404 Error
    else {
        $_GET['errorPage'] = "PRINT";
        include "404.php";
    }
}

// Close connection
$DSDB->close();

?>
