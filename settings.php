<?php

// PATHES
define('DS_PATH_HOST','http://druckstud.io');
define('DS_PATH_BASE','/ministripes/');
define('DS_PATH_ABS_BASE',DS_PATH_HOST.DS_PATH_BASE);
define('DS_PATH_UPLOAD','uploads/');
define('DS_UPLOAD_ORIGINAL','original/');
define('DS_UPLOAD_THUMB','thumb/');
define('DS_UPLOAD_TMP','uploader/');
define('DS_UPLOAD_PRINT','/print/');

// SETTINGS
define('DS_SETUP_TIMELIMIT',300);

define('DS_INST_MORE_IMAGES', 12);

/*  test app localhost  */
define('DS_INST_KEY', '6237babf7c3348b7af4a32b181b66e77');
define('DS_INST_SECRET', '67b2c178b05d4301aaec6a8c47e1718d');
define('DS_INST_CALLBACK', 'http://druckstud.io/ministripes/index.php');

/*  test app webiprog  */
/*define('DS_INST_KEY', '26f45723e1e94248b45fb6a9fb0cfd3f');
define('DS_INST_SECRET', '9bd78ee169ca48cbaa91dcee5b42f7b2');
define('DS_INST_CALLBACK', 'http://localhost/editor/index.php');*/


/*  production app instagram   */
/*define('DS_INST_KEY', 'f3588986d7a34134a1a8d91683fc723f');
define('DS_INST_SECRET', '74aec8cfb2cf41c3b5ec085f0285cad4');
define('DS_INST_CALLBACK', 'http://localhost/editor/index.php');*/

//FACEBOOK
//define('DS_FACEBOOK_APP_ID', '236455700058256');
define('DS_FACEBOOK_APP_ID', '1611019499204402');

// DATABASE
define('DS_DB_HOST','localhost');
define('DS_DB_NAME','web28_db1');
define('DS_DB_USER','web28_1');
define('DS_DB_PASS','6CLZgsC4Y3GV');

// TABLES
$wp_prefix = 'b1mNf_';
$ds_prefix = $wp_prefix.'v12_';
define('DS_TABLE_MATERIAL',$ds_prefix.'material');
define('DS_TABLE_MATERIAL_OPTIONS',$ds_prefix.'material_options');
define('DS_TABLE_PRODUCT',$ds_prefix.'product');
define('DS_TABLE_PRODUCT_SETTINGS',$ds_prefix.'product_settings');
define('DS_TABLE_PRODUCT_TYPE',$ds_prefix.'product_type');

?>
