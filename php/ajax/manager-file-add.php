<?php
/*
 * DRUCKSTUDIO - AJAX - FILE UPLOADER - ADD IMAGE TO GALLERY
 *
 */

if(DS_INDEX_CHECK != "DS_LOADED") exit();

// Get Ajax-Data
$DS_DATA = $_REQUEST['DS_AJAX_DATA'];

// Product data
$product = $druckstudio->getProduct();
$thumbDir = $product->getDir('thumb');

// Status
$status = "success";
$statusMsg = "";

// Filelist checkup
$images = $DS_DATA['DS_IMAGES'];
$imageList = array();

// Base Data
$instagram = $DS_DATA['DS_INSTAGRAM'];
if($instagram == 1){

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
    $imgURL= $images[0];
    $ext = 'jpg';
    $imgPath = $origDir.$imgName.".".$ext;
    $tmpPath = $tmpDir.$imgName.".".$ext;
    file_put_contents($tmpPath, file_get_contents($imgURL));
    file_put_contents($imgPath, file_get_contents($imgURL));
    // Upload successful
    $tmpImage = imageResize($imgPath,$tmpPath,$tmp_width,$tmp_height);
    // Save data
    $fileManager->addFile(basename($imgPath),false);
    $druckstudio->saveData();

    $_SESSION['DS_INSTAGRAM'][$imgURL] = $imgPath;
    unset($images);
    $images[] = $tmpPath;

}

if(is_array($images) && @count($images)>0) {
    // Files handling
    foreach($images as $imageSrc) {
        // Add material
        $fileparts = realPathinfo($imageSrc);
        $imageName = $fileparts['basename'];
        $material = new DS_MATERIAL();
        $material->setFile($imageName);
        $MID = $product->addMaterial($material);
        if($MID !== false) {
            if($product->createThumbs($MID)) {
                $thumbName = $product->getThumb($MID);

                $settings = $product->getType()->getSettings();
                $materialSizeX = $settings['size-x'];
                $materialSizeY = $settings['size-y'];
                $materialRes = $settings['dpi'];
                $src = explode('?', $imageSrc);

                $msg = "";
                if(!checkImage($product->getOriginal($MID),$materialSizeX,$materialSizeY)) {
                    $msg = "dpi-error";
                }

                $imageList[] = array(
                    'status' => "success",
                    'msg' => $msg,
                    'id' => $MID,
                    'imageName' => explode('.', $imageName)[0],
                    'thumb' => $thumbName,
                    'upload' => $imageSrc,
                    'instagram' => $instagram
                );
            }
            else {
                $product->removeMaterial($MID);
                $imageList[] = array(
                    'status' => "error",
                    'msg' => "Es konnten keine Vorschaubilder vom Bild ($MID) erstellt werden!",
                    'id' => $MID,
                    'thumb' => ""
                );
            }
        }
        else {
            $imageList[] = array(
                'status' => "error",
                'msg' => "Das Bild ($MID) konnte nicht zur Gallerie hinzugef�gt werden!",
                'id' => $MID,
                'thumb' => ""
            );
        }
    }
    // Save added materials in cache
    $druckstudio->saveData();
} else {
    // No files selected
    $status = "error";
    $statusMsg = "Es sind keine Dateien mitgesendet worden!";
}

// Setup data
$data = array(
    'images' => $imageList,
    'dir' => $thumbDir
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
