<?php 
$path = "http://druckstud.io/ministripes/uploads"; //directory outside doc root on the server somewhere 
$file = "file_name.jpg"; 
$image = $path.$file; 
header("Content-Type: image/jpeg"); 
@readfile($image); 
?>