<?php
/*
 * DRUCKSTUDIO - AJAX - FILE UPLOADER - UPLOADING
 * 
 * Saves the images of the uploader-script to specific folder
 *
 */

if(DS_INDEX_CHECK != "DS_LOADED") exit();

// Base Data
$product = $druckstudio->getProduct();
$fileManager = $druckstudio->getManager()->getUploader();

// Upload directory
$origDir = $product->getDir('original');
$tmpDir = $product->getDir('uploader');
$tmp_width = $fileManager->getThumbSize(0);
$tmp_height = $fileManager->getThumbSize(1);

// Data for outpur
$data = array();

// Get name for new image
$imgName = $fileManager->newFileNr();

if($imgName && $product->isLegalFileType($_FILES['ds_uploads']['name'][0])) {
    // Uploading
    $img = $_FILES['ds_uploads']['tmp_name'][0];
    $ext = pathinfo($_FILES['ds_uploads']['name'][0],PATHINFO_EXTENSION);
    $imgURL = $tmpDir.$imgName.".jpg"; // .$ext
    $imgWide = getimagesize($img);
    $imgWide = ($imgWide[0] > $imgWide[1]) ? true : false;
    $imgPath = $origDir.$imgName.".jpg";//.$ext;
    $tmpPath = $tmpDir.$imgName.".jpg";//$tmpDir.$imgName.".".$ext;
    // Upload successful
    if(move_uploaded_file($img,$imgPath)) {
        // Create thumbnail
        $tmpImage = imageResize($imgPath,$tmpPath,$tmp_width,$tmp_height);
        // Save data
        $fileManager->addFile(basename($imgPath),true);
        $druckstudio->saveData();
        // Setup data
        $data = array(
            'src' => $imgURL,
            'name' => utf8_encode($imgName),
            'dir' => $tmpDir,
            'wide' => $imgWide
        );
        // Save Status
        $status = array(
            'type' => "success",
            'value' => ""
        );
    }
    // Upload error
    else {
        // Save Status
        $status = array(
            'type' => "error",
            'value' => "Der Uploadvorgang f�r das Bild (".$_FILES['ds_uploads']['name'][0].") ist fehlgeschlagen!",
            'ds_istagram' => json_encode($_SESSION['DS_INSTAGRAM'])
        );
    }
}
else {
    // Save Status
    $status = array(
        'type' => "error",
        'value' => "Es konnte kein neues Bild angelegt werden!"
    );
}
        
// Save Output for Ajax-Response
$return = array(
    'status' => $status,
    'data' => $data,
    'ds_instagram' => !empty($_SESSION['DS_INSTAGRAM']) ? json_encode($_SESSION['DS_INSTAGRAM']) : json_encode(array())
);

?>
