<?php
/*
 * DRUCKSTUDIO - PRODUKT SPEICHERN
 * 
 * Saves the new order of gallery images after sorting
 *
 */

if(DS_INDEX_CHECK != "DS_LOADED") exit();

// Base data
$PID = (int)$_POST['DS_AJAX_DATA']['PID'];

// Remove Product
if($druckstudio->removeProduct($PID)) {
    $status = array(
        'type' => "success",
        'value' => ""
    );
}
else {
    $status = array(
        'type' => "error",
        'value' => "Es trat ein Fehler auf! Das Produkt konnte nicht entfernt werden!"
    );
}

// Save Output for Ajax-Response
$return = array(
    'status' => $status
);

?>