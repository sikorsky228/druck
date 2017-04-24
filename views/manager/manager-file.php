<?php
/*
 * SQUARE PRINTS - TEMPLATE - FILE MANAGER
 *
 */

// Get File Managaer Data
$manager = $druckstudio->getManager();
$fileManager = $manager->getUploader();
$files = $fileManager->getFiles();
$tmpDir = $product->getDir('uploader');

?>

        <form id="ds-file-form">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <input id="ds-file-uploadfield" class="hidden" type="file" name="files[]" multiple="" />
                        <div class="ds-file-uploadbox">
                            <p>
                                <i class="fa fa-upload fa-3x" aria-hidden="true"></i>
                            </p>
                            <span class="visible-md-block visible-lg-block">
                                <div class="btn btn-default ds-button-blue">
                                    <span class="visible-lg-block">Meine fotos importieren</span>
                                </div>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ds-file-list container-fluid">
                <ul class="row">
                    <?php
                    foreach($files as $image) {

                        if($image['manualUpload']){
                            $imageName = pathinfo($image['name'],PATHINFO_FILENAME);
                            $imageSrc = $tmpDir.$image['name'];
                            $imageSize = getimagesize($imageSrc);
                            $imageWide = ($imageSize[0] < $imageSize[1]) ? "portrait" : "landscape";
                            ?>
                            <li class="ready col-xs-4 col-sm-3 col-md-3 col-lg-4">
                                <div class="ds-file-box squarebox">
                                    <div class="squarebox-inner">
                                        <input type="hidden" class="count" value="0">
                                        <div class="squarebox-bg"></div>
                                        <div class="arr arrow-up"></div>
                                        <div class="arr arrow-down"></div>
                                        <span class="count-number">0</span>
                                        <img src="images.php?id=<?php echo $imageSrc ?>" data-name="<?php echo $imageName ?>" data-instagram="0" />
                                    </div>
                                </div>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </form>