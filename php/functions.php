<?php
/*
 * DRUCKSTUDIO - GENERAL FUNCTIONS
 * 
 * Collection of helper functions
 *
 */


/* REAL BASENAME
 * -------------------------------------------------------------------- 
 *
 */
function realPathinfo($filePath) {
    $name = pathinfo($filePath,PATHINFO_FILENAME);
    $name = explode("?",$name);
    $name = $name[0];
    $ext = pathinfo($filePath,PATHINFO_EXTENSION);
    $ext = explode("?",$ext);
    $ext = $ext[0];
    $data = array(
        'basename' => "$name.$ext",
        'filename' => $name,
        'extension' => $ext
    );
    return $data;
} 

/* RESIZE IMAGE
 * --------------------------------------------------------------------
 * 
 * $src = address of source image
 * $dest = address where resized image should be saved
 * $w = width of target image
 * $h = height of target image
 * $ratio = modus of aspect ratio (not used yet)
 *
 */
function imageResize($src,$dest,$dest_w,$dest_h,$src_x=0,$src_y=0,$src_zoom=100,$dpi=72,$ratio=0,$angle=0) {
    // Check source
    if(!@is_file($src)) {
        DS_LOGS::addEntry("BILDBEARBEITUNG: Keine Quelldatei vorhanden!");
        return false;
    }
    // Check if destiny directory is writable
    if(!@is_writable(dirname($dest))) {
        DS_LOGS::addEntry("BILDBEARBEITUNG: Auf Zielordner kann nicht geschrieben werden!");
        return false;
    }
    // Check parameter
    if(!is_numeric($dest_w) || !is_numeric($dest_h) || !is_numeric($src_x) || !is_numeric($src_y) || !is_numeric($src_zoom)) {
        DS_LOGS::addEntry("BILDBEARBEITUNG: Ung�ltige Angaben zum Bild �bergeben ($dest_w | $dest_h | $src_x | $src_y | $src_zoom)!");
        return false;
    }
    // Check ratio
    $legalRatio = array(0,1,2);
    if(!in_array($ratio,$legalRatio)) {
        DS_LOGS::addEntry("BILDBEARBEITUNG: Kein g�ltigen Modus f�r das Seitenverh�ltnis gew�hlt!");
        return false;
    }
    // Get image data
    $imageInfo = getimagesize($src);
    if(!$imageInfo) {
        DS_LOGS::addEntry("BILDBEARBEITUNG: Bildinformation konnte nicht ausgelesen werden!");
        return false;
    }
    // Check image type
    $imageType = $imageInfo[2];
    $imageLegalTypes = array(IMAGETYPE_JPEG,IMAGETYPE_PNG);
    if(!in_array($imageType,$imageLegalTypes)) {
        DS_LOGS::addEntry("BILDBEARBEITUNG: Dateityp (".$imageType.") ist nicht erlaubt!");
        return false;
    }
    
    // Image calculation
    $orig_w = $imageInfo[0];
    $orig_h = $imageInfo[1];
    $diff_w = ($orig_w - $dest_w);
    $diff_h = ($orig_h - $dest_h);
    
    $isVertical = $orig_h / $orig_w > 1 ? true : false;

    if($diff_w>=$diff_h) {
		//if(($angle)/90 === 1 || ($angle)/90 === -3 || ($angle)/90 === 3 || ($angle)/90 === -1){
		//	$new_h = $orig_w;
		//	$new_w = $orig_w * ($dest_h/$dest_w);
		//}else{
			$new_h = $orig_h;
			$new_w = $orig_h * ($dest_w/$dest_h);
		//}
    } 
    else {
		//if(($angle)/90 === 1 || ($angle)/90 === -3 || ($angle)/90 === 3 || ($angle)/90 === -1){
		//	$new_w = $orig_h;
		//	$new_h = $orig_h * ($dest_w/$dest_h);
		//}else{
			$new_w = $orig_w;
			$new_h = $orig_w * ($dest_h/$dest_w);
		//}
    }
    
    //echo 'new_w' . ' :: ' . $new_w; . '  ';
    //echo 'new_w' . ' :: ' . $new_w; . '  ';
    //echo 'new_h' . ' :: ' . $new_h; . '  ';

    // Zooming
    $zoomScale = ($src_zoom / 100);
    
    if(($angle)/90 === 1 || ($angle)/90 === -3 || ($angle)/90 === 3 || ($angle)/90 === -1){
		if ($isVertical){
			$final_w =  ($new_w / $zoomScale) / ($dest_h / $dest_w);
			$final_h =  ($new_h / $zoomScale) / ($dest_h / $dest_w);
		}else{
			$final_w =  ($new_w / $zoomScale) * ($dest_h / $dest_w);
			$final_h =  ($new_h / $zoomScale) * ($dest_h / $dest_w);
		}
	}else{
		$final_w =  ($new_w / $zoomScale);
		$final_h =  ($new_h / $zoomScale);
	}

// Offset

    if(($angle)/90 === 1 || ($angle)/90 === -3){
        $temp = $src_x;
        $src_x = $src_y;
        $src_y = 1 - $temp;
    }else if(($angle)/90 === 3 || ($angle)/90 === -1){
        $temp = $src_x;
        $src_x = 1 - $src_y;
        $src_y = $temp;
    }else if(($angle)/90 === 2 || ($angle)/90 === -2){
        $src_x = 1 - $src_x;
        $src_y = 1 - $src_y;
    }
    $final_x =  ($src_x * ($orig_w - $final_w));
    $final_y =  ($src_y * ($orig_h - $final_h));


    // Set quality by DPI
    $quality = ($dpi<=72) ? 50 : 100;
    
    // Image Resizing
    $destFile = fopen($dest,"w");
    $im = new Imagick();
    $im->setResolution($dpi,$dpi);
    $im->readImage(realpath($src));
    $im->setImageFormat('jpeg');
    $im->setImageResolution($dpi,$dpi);
    $im->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
    $im->setImageCompressionQuality($quality);
    $im->resampleImage($dpi,$dpi,imagick::FILTER_UNDEFINED,0);
    if(($angle)/90 === 1 || ($angle)/90 === -3 || ($angle)/90 === 3 || ($angle)/90 === -1){
		$im->cropImage($final_h,$final_w,$final_x,$final_y);
	}else{
		$im->cropImage($final_w,$final_h,$final_x,$final_y);
	}
    $im->rotateImage(new ImagickPixel('#00000000'), $angle);
    $im->thumbnailImage($dest_w, $dest_h, false, true);
    $im->writeImageFile($destFile);
    $im->clear();
    $im->destroy();

    // Return new image link
    return $dest;
}


/* CHECK IMAGE QUALITY
 * --------------------------------------------------------------------
 * 
 */  
function checkImage($src,$normSizeX,$normSizeY,$dpi=100,$sizeMargin=1.0) {
    if(@is_file($src)) {
        $image = new Imagick($src);
        $resolutions = $image->getImageGeometry();

        $minHeight = 161;
        $minWidth = 161;
        $cmHeight = 4.1;

        if(($resolutions['height']<$minHeight) || ($resolutions['width']<$minWidth)){
            return false;
        }

        $height = ($resolutions['height'] / ($cmHeight/2.54));
        if(round($height) >= $dpi) {
            return $height;
        }

    }
    return false;
}

?>
