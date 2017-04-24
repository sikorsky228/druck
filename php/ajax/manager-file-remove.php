<?php
/*
 * DRUCKSTUDIO - AJAX - FILE UPLOADER - REMOVE FILE
 *
 */

if(DS_INDEX_CHECK != "DS_LOADED") exit();

// Get Ajax-Data
$DS_DATA = $_REQUEST['DS_AJAX_DATA'];

// Product
$product = $druckstudio->getProduct();

// File Manager
$fileManager = $druckstudio->getManager()->getUploader();
$tmpDir = $fileManager->getPath();

// Status setup
$status = "success";
$statusMsg = "";

// Filelist checkup
$images = $DS_DATA['DS_IMAGES'];
$filesReport = array();
if(is_array($images) && @count($images)>0) {
    // Files handling
    foreach($images as $fileURL) {
        $fileparts = realPathinfo($fileURL);
        $fileName = $fileparts['filename'];
        $baseName = $fileparts['basename'];
        $fileStatus = "success";
        // File removing
        if($product->countFiles($fileURL)<=0) {
            $filePath = $product->getDir('original').$baseName;
            unlink($filePath);
        }
        if(!$fileManager->removeFileByName($baseName)) {
            // Could not remove file
            $fileStatus = "error";
            $status = "error";
            $statusMsg .= "Die Datei (".$fileName.") konnte nicht entfernt werden!\n";
        }
        $filesReport[] = array(
            'status' => $fileStatus,
            'file' => $fileName
        );
    }
} else {
    // No files selected
    $status = "error";
    $statusMsg = "Es sind keine Dateien mitgesendet worden!";
}

// Save new data structure
$druckstudio->saveData();

// Setup data
$data = array(
    'report' => $filesReport,
    'dir' => $tmpDir
);
$status = array(
    'type' => $status,
    'value' => $statusMsg
);

// Save Output for Ajax-Response
$return = array(
    'status' => $status,
    'data' => $data,
    'ds_instagram' => !empty($_SESSION['DS_INSTAGRAM']) ? json_encode($_SESSION['DS_INSTAGRAM']) : json_encode(array())
);

?>
