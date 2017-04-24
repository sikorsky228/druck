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

$dpi = 100;

$thumb_width = 600;
$thumb_height = 400;


$temp 		= explode(';base64,', $_REQUEST['DS_AJAX_DATA']['imgurl']);
$buffer		= base64_decode($temp[1]);

$product = $druckstudio->getProduct();
$materials = $product->getMaterials();
$material = $materials[$MID];
$oldZoom = $material->getZoom();
$oldOffset = $material->getOffset();
$oldAngle = $material->getAngle();

$type = $product->getType();
$settings = $type->getSettings();
$print_dpi = $settings['dpi'];
$print_width = $settings['size-x'];
$print_height = $settings['size-y'];

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
        
        
        //$f = fopen( 'test.png', 'w+' );
        //fwrite($f, $imgdata);
        //fclose($f);
        $nr = $material->getNr();
		$dest = $product->getDir('thumb') . str_pad($nr,5,'0',STR_PAD_LEFT) . '.jpg';
		$destPrint = $product->getDir('print') . str_pad($nr,5,'0',STR_PAD_LEFT) . '.jpg';
        //file_put_contents($filename, $buffer);
        
        // Set quality by DPI
		$quality = ($dpi<=72) ? 50 : 100;
        
        
        /* Thumb */
        $destFile = fopen($dest,"w");
		$im = new Imagick();
		$im->readImageBlob($buffer);
		$im->setImageFormat('jpeg');
		$im->setResolution($dpi,$dpi);
		$im->setImageResolution($dpi,$dpi);
		$im->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
		$im->setImageCompressionQuality($quality);
		$im->resampleImage($dpi,$dpi,imagick::FILTER_UNDEFINED,0);
		$im->resizeImage($thumb_width, $thumb_height, Imagick::IMGTYPE_TRUECOLOR);
		$im->writeImageFile($destFile);
		$im->clear();
		$im->destroy();
		
		/* Print */
        $destFile = fopen($destPrint,"w");
		$im = new Imagick();
		$im->readImageBlob($buffer);
		$im->setImageFormat('jpeg');
		$im->setResolution($print_dpi,$print_dpi);
		$im->setImageResolution($print_dpi,$print_dpi);
		$im->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
		$im->setImageCompressionQuality(100);
		$im->resampleImage($print_dpi,$print_dpi,imagick::FILTER_UNDEFINED,0);
		$im->resizeImage($print_width, $print_height, Imagick::IMGTYPE_TRUECOLOR);
		$im->writeImageFile($destFile);
		$im->clear();
		$im->destroy();
        
        
        //$newImage = $product->createThumbs($MID);
        // Activate refreshing for gallery image
        $data['refresh'] = true;
        $data['file'] = $product->getThumb($MID, true);
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
