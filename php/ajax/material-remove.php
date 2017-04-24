<?php
/*
 * DRUCKSTUDIO - AJAX - MATERIAL REMOVE
 * 
 * Remove Material from Gallery
 *
 */

if(DS_INDEX_CHECK != "DS_LOADED") exit();

// Material info
$MID = (int)$_REQUEST['DS_AJAX_DATA']['MID'];
$product = $druckstudio->getProduct();
$material = $product->getMaterials($MID);
$materialFile = $material->getFile();

// Removing file
$filePath = $product->getDir('original').$materialFile;
if(@file_exists($filePath)) {
    $fileManager = $druckstudio->getManager()->getUploader();
    if(!$fileManager->hasFile($materialFile) && $product->countFiles($materialFile)<=1) {
//        unlink($filePath);
    }
}

// Removing material
if($druckstudio->getProduct()->removeMaterial($MID)) {
    $druckstudio->saveData();
    // Status
    $status = array(
        'type' => "success",
        'value' => ""
    );
} else {
    // Status
    $status = array(
        'type' => "error",
        'value' => "Could not remove image nr ".$MID."!"
    );
}


// Save Output for Ajax-Response
$return = array(
    'status' => $status
);

?>