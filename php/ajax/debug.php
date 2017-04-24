<?php

$html = print_r($_SESSION['DS_DATA'],true);
$html = "<pre>$html</pre>";

// Save Status
$status = array(
    'type' => "success",
    'value' => ""
);

// Save Output for Ajax-Response
$return = array(
    'status' => $status,
    'html' => $html
);

?>