<?php
use MetzWeb\Instagram\Instagram;

//Get Instagram Manager (if present)
$manager = $druckstudio->getManager();
$instagram_manager = $manager->getInstagram();

if(!isset($instagram_manager)) {
?>
    <a href='<?php echo $instagram->getLoginUrl() ?>'>
        <div class="btn btn-default ds-button-blue">
            <span class="visible-lg-block">Mit Instagram verbinden</span>
        </div>
    </a>
<?php } else {?>
		<form id="ds-file-form">
            <div class="ds-file-list ds-instagram-list container-fluid">
                <ul class="row">
                    <?php
                    foreach($instagram_manager->getImageURLs() as $url) {
                        ?>
                        <li class="ready col-xs-4 col-sm-3 col-md-3 col-lg-4">
                            <div class="ds-file-box squarebox">
                                <div class="squarebox-inner">
                                    <input type="hidden" class="count" value="0">
                                    <div class="squarebox-bg"></div>
                                    <div class="arr arrow-up"></div>
                                    <div class="arr arrow-down"></div>
                                    <span class="count-number">0</span>
                                    <?php
                                    if (array_key_exists($url, $_SESSION['DS_INSTAGRAM'])) {
                                        ?>
                                        <img src="images.php?id=<?php echo $_SESSION['DS_INSTAGRAM'][$url] ?>" data-name="<?php echo array_pop( explode('/', explode('.', $_SESSION['DS_INSTAGRAM'][$url])[0]) );?>" data-instagram="false"/>
                                        <?php
                                    }else{
                                        ?>
                                        <img src="<?php echo $url ?>" data-name="<?php echo $url ?>" data-instagram="1"/>
                                        <?php
                                    }
                                    ?>

                                </div>

                            </div>
                        </li>
                        <?php
                    }
                    ?>
                    
                </ul>
            </div>
                    <li class="ready col-xs-4 col-sm-3 col-md-3 col-lg-4">
                    <button class="ds-instagram-more-button"></button></li>
         </form>
<?php }?>

       
