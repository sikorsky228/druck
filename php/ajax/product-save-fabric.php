<?php
/*
 * DRUCKSTUDIO - PRODUKT SPEICHERN
 * 
 * Saves the new order of gallery images after sorting
 *
 */

if(DS_INDEX_CHECK != "DS_LOADED") exit();

// Base data
$tempDir = $druckstudio->getProduct()->getTemplate();
$status = array(
    'type' => "success",
    'value' => ""
);
$data = array();

// Saving succcessful
if($druckstudio->saveDataDB()) {
    $saveStatus = true;
}

// Saving failed
else {
    $saveStatus = false;
}

// Info from product check
$DS_LOGS = new DS_LOGS();
$DS_LOGS->loadRequestLogs();
$logs = $DS_LOGS->getLog('ProductCheck');
$DS_LOGS->clearLog('ProductCheck');

if (empty($logs)) {
    // put in cart
    require_once('../wp-load.php');
    $product = $druckstudio->getProduct();
    WC()->cart->add_to_cart($product->getWcID(), 1, '', '', array(
        'ministripes_order_id' => $product->getPID()
    ));
}

// Load Template Data
$tempData = http_build_query(
    array(
        'saveStatus' => $saveStatus,
        'logs' => $logs
    )
);

$opts = array(
    'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $tempData
    )
);
$context  = stream_context_create($opts);
$output_url = DS_PATH_ABS_BASE.$tempDir.'product-save.php';
$html = file_get_contents($output_url, true, $context);

// Save Output for Ajax-Response
$return = array(
    'status' => $status,
    'html' => utf8_encode($html)
);

?>
