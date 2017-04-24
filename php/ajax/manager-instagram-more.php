<?php
$lastImagesToShow = $_REQUEST['DS_AJAX_DATA']['lastImagesToShow'];
$instagram_manager = $druckstudio->getManager()->getInstagram();
$instagram_manager->expandImageList($lastImagesToShow + DS_INST_MORE_IMAGES);
$druckstudio->saveData();
$urlArray = array();
$tempArray = array('url'=>'','dataName'=>'');
// Save Output for Ajax-Response
//$return = $instagram_manager->getImageURLs();
foreach($instagram_manager->getImageURLs() as $url) {
    if (array_key_exists($url, $_SESSION['DS_INSTAGRAM'])) {
        $tempArray['url'] = $_SESSION['DS_INSTAGRAM'][$url];
        $tempArray['dataName'] = array_pop( explode('/', explode('.', $_SESSION['DS_INSTAGRAM'][$url])[0]) );

    }else{
        $tempArray['url'] = $url;
        $tempArray['dataName'] = $url;
    }
    $urlArray[] = $tempArray;
}
$return = $urlArray;
?>