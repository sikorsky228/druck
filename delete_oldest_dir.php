<?php
  $dirs = glob('/home/www/druckstudio/ministripes/uploads/*');
  $time_now   = time();

function removeDirectory($dir) {
			 	$files = glob($dir . '/*');
				foreach ($files as $file) {
					is_dir($file) ? removeDirectory($file) : unlink($file);
				}
				rmdir($dir);
			print_r($dir);
	        print_r("<br>");
			 	return;
			}


  foreach ($dirs as $dir){
    if (is_dir($dir)){
      if ($time_now - filemtime($dir) >= 86400 * 14) // 14 days
	      	{
	      	removeDirectory($dir);	      		
    	}
    }
}

?>