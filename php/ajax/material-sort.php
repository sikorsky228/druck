<?php
/*
 * DRUCKSTUDIO - AJAX - MATERIAL SORTING
 * 
 * Saves the new order of gallery images after sorting
 *
 */

if(DS_INDEX_CHECK != "DS_LOADED") exit();

// Materials
$newMaterials = array();
$materials = $druckstudio->getProduct()->getMaterials();
$materialsCount = @count($materials);
$positions = $_REQUEST['DS_AJAX_DATA']['imgPos'];
$positionCount = @count($positions);

// Set new positions
if($positionCount == $materialsCount) {
    $error = false;
    foreach($materials as $index=>$material) {
        $pos = $positions[$index];
        if($pos<0) {
            $error = true;
            break;
        }
        $material->setPosition($pos);
        $newMaterials[$index] = $material->serialize();
    }
    
    // Save data
    if(!$error) {
        $druckstudio->getProduct()->setMaterials($newMaterials);
        $druckstudio->saveData();
    }
}


?>