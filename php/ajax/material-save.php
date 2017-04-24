<?php
/*
 * DRUCKSTUDIO - AJAX - MATERIAL SAVE
 * 
 * Saves the data setted in the lightbox
 *
 */

if(DS_INDEX_CHECK != "DS_LOADED") exit();

// MATERIAL INFO
$MID = (int)$_REQUEST['DS_AJAX_DATA']['MID'];
$zoom = $_REQUEST['DS_AJAX_DATA']['zoom'];
$angle = $_REQUEST['DS_AJAX_DATA']['angle'];
$offset = $_REQUEST['DS_AJAX_DATA']['offset'];
$product = $druckstudio->getProduct();
$materials = $product->getMaterials();
$material = $materials[$MID];
$oldZoom = $material->getZoom();
$oldOffset = $material->getOffset();
$oldAngle = $material->getAngle();

// Data container for ajax receive
$data = array(
    'MID' => $MID,
    'refresh' => false,
    'file' => ''
);

if($material) {
    // Daten speichern
    $materials[$MID]->setZoom($zoom);
    $materials[$MID]->setAngle($angle);
    $materials[$MID]->setOffset($offset[0],$offset[1]);
    $druckstudio->saveData();
    // Bilder anpassen
    if($zoom != $oldZoom || $offset != $oldOffset || $angle != $oldAngle) {
        $newImage = $product->createThumbs($MID);
        // Activate refreshing for gallery image
        $data['refresh'] = true;
        $data['file'] = $product->getThumb($MID);
    }
    
    // Save Status
    $status = array(
        'type' => "success",
        'value' => ""
    );
}

// Save Output for Ajax-Response
$return = array(
    'status' => $status,
    'data' => $data
);

?>