<?php
/*
 * SQUARE PRINTS - MATERIAL FULL VIEW / MATERIAL SETTING
 *
 */

// Bild-Information

$MID = $_POST['MID'];
$image_original = $_POST['image_original'];


$image_ratio = $_POST['image_wide'] ? ' landscape' : ' portrait';


$image_ratio = ' landscape';

// Offset & Zoom
$offset = $_POST['image_offset'];
$x = is_numeric($offset[0]) ? $offset[0] : 0.0;
$y = is_numeric($offset[1]) ? $offset[1] : 0.0;
$zoom = is_numeric($_POST['image_zoom']) ? $_POST['image_zoom'] : 0.0;
$angle = is_numeric($_POST['image_angle']) ? $_POST['image_angle'] : 0;

?>

<div class="ds-lightbox" id="ds-material-full" data-id="<?php echo $MID ?>">
    <div class="ds-inner">
        <div class="ds-lightbox-exit"></div>
        <div class="ds-lightbox-content">
            <form class="ds-lightbox-form">
                
                
                <div class="ds-lightbox-cropbox squarebox ds-lightbox-cropbox-vintage-h">
                    <div class="squarebox-inner">
                        <img src="images.php?id=<?php echo $image_original; ?>" class="<?php echo $image_ratio; ?>" data-posx="<?php echo $x; ?>" data-posy="<?php echo $y; ?>" data-dposx="<?php echo $x; ?>" data-dposy="<?php echo $y; ?>" />
                        <div class="ds-img-loading"><i class="fa fa-circle-o-notch fa-spin loader"></i></div>
                    </div>
                </div>
                
                <!--
                <div class="ds-lightbox-cropbox squarebox ds-lightbox-cropbox-vintage-h">
                    <div class="squarebox-inner">
						<img style="display:none;" id="vintage12lightbox_image" src="<?php echo $image_original; ?>" class="<?php echo $image_ratio; ?>" data-dangle="<?php echo $angle ?>" data-dzoom="<?php echo $zoom ?>" data-posx="<?php echo $x; ?>" data-posy="<?php echo $y; ?>" data-dposx="<?php echo $x; ?>" data-dposy="<?php echo $y; ?>" />
						<canvas id="vintage12canvas" width="410" height="410" ></canvas>
						<div class="ds-img-loading"><i class="fa fa-circle-o-notch fa-spin loader"></i></div>
                    </div>
                </div>
                -->
                
                
                <div class="resizer-warning-image" data-toggle="tooltip" title="" data-placement="top" data-original-title="deine aktuelle Bildauswahl ist zu groß! dadurch leidet die Druckqualität."><img src="/editor/img/icons/Warndreieck.png"></div>
                <div class="ds-lightbox-settings">
                    <div class="ds-lightbox-zoom">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-xs-2 col-sm-2 col-md-2">
                                    <div class="ds-rotate-right">
                                        <i class="fa fa-repeat"></i>
                                    </div>
                                </div>
                                <div class="col-xs-1 col-sm-1 col-md-1">
                                    <div class="ds-zoombutton ds-button-minus">
                                        <i class="fa fa-minus"></i>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div id="ds-cropimage-zoom" data-angle="<?php echo $angle ?>" data-dangle="<?php echo $angle ?>" data-zoom="<?php echo $zoom ?>" data-dzoom="<?php echo $zoom ?>"></div>
                                </div>
                                <div class="col-xs-1 col-sm-1 col-md-1">
                                    <div class="ds-zoombutton ds-button-plus">
                                        <i class="fa fa-plus"></i>
                                    </div>
                                </div>
                                <div class="col-xs-2 col-sm-2 col-md-2">
                                    <div class="ds-rotate-left">
                                        <i class="fa fa-undo"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ds-lightbox-action">
                    <div class="ds-button ds-button-cancel fleft">
                        <span class="hidden-xs">ABBRECHEN</span>
                        <span class="visible-xs-block"><i class="fa fa-times"></i></span>
                    </div>
                    <div class="ds-button ds-button-reset fleft">
                        <span class="hidden-xs">ZUR&Uuml;CKSETZEN</span>
                        <span class="visible-xs-block"><i class="fa fa-undo"></i></span>
                    </div>
                    <div class="ds-button ds-button-save fleft">
                        <span class="hidden-xs">SPEICHERN</span>
                        <span class="visible-xs-block"><i class="fa fa-check"></i></span>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    </div>
</div>
