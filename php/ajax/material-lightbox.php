<?php
/*
 * DRUCKSTUDIO - AJAX - MATERIAL EDITING
 * 
 * Shows the material in full-screen mode where it's editable.
 * Actions for material: 
 *  - Crop the material image
 *  - Set image filters
 *  - Remove or duplicate the material
 *
 */

if(DS_INDEX_CHECK != "DS_LOADED") exit();

// Directory of Files
$tempDir = $druckstudio->getProduct()->getTemplate();

$fileDir = $druckstudio->getProduct()->getDir('original');

// MATERIAL INFO
$MID = (int)$_REQUEST['DS_AJAX_DATA']['MID'];
$material = $druckstudio->getProduct()->getMaterials();
$material = $material[$MID];

// Image Data
$image_file = $material->getFile();
$image_original = ($image_file) ? $fileDir.basename($image_file) : "";
$image_wide = getimagesize( $image_original );
$image_wide = ($image_wide[0] > $image_wide[1]) ? true : false;

// Load Template Data
$tempData = http_build_query(
    array(
        'image_original' => $image_original,
        'image_wide' => $image_wide,
        'image_zoom' => $material->getZoom(),
        'image_angle' => $material->getAngle(),
        'image_offset' => $material->getOffset(),
        'MID' => $MID
    )
);
$opts = array(
    'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $tempData
    ),
    'socket' => array(
            'bindto' => '0:0',
    ),
);
$context  = stream_context_create($opts);
//$output_url = '/var/www/vhosts/webiprog.com/druck.webiprog.com/stripes/views/products/squares/material-lightbox.php';
$output_url = DS_PATH_ABS_BASE.$tempDir.'material-lightbox.php';
error_log($output_url);

$html = file_get_contents($output_url, true, $context);

#$ch = curl_init();
#curl_setopt($ch, CURLOPT_URL, $output_url);
#curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
#curl_setopt($ch, CURLOPT_POST, 1);
#curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
#curl_setopt($ch, CURLOPT_POSTFIELDS,$tempData);

#$data = curl_exec($ch);

#$error = curl_error($ch); 
#if ($error) {
     // Get additional informations about the failed CURL transfert
//     $info = curl_getinfo($ch);
//     echo '<pre>';
//     print_r($info);
//     echo '</pre>'; 
//     die();
#}

#curl_close($ch);

// Save Status
$status = array(
    'type' => "success",
    'value' => ""
);

// Save Output for Ajax-Response
$return = array(
    'status' => $status,
    'html' => $html,
);

?>