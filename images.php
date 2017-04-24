<?php

	$file = $_GET['id'];
	header('Content-type: image/jpg');
	$content = implode('', file($file));
	echo $content;
?>