<?php
/*
 * SQUARE PRINTS - PRODUCT TEMPLATE
 *  
 */

if(DS_INDEX_CHECK != "DS_LOADED") exit();

// SETTINGS
$title = "DRUCKSTUDIO / SQUARE PRINTS";

?>
<html>
<head>
    <title><?php echo $title; ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1"/>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
    <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css' />
    <link rel="stylesheet" type="text/css" href="<?php echo DS_PATH_BASE; ?>js/jquery-ui/jquery-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo DS_PATH_BASE; ?>css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo DS_PATH_BASE; ?>css/main.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo DS_PATH_BASE; ?>css/product-squares.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo DS_PATH_BASE; ?>ModalWindowEffects/css/default.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo DS_PATH_BASE; ?>ModalWindowEffects/css/component.css" />
</head>
<body>
    <div id="ds-main">
        <?php require_once("views/products/squares/header.php"); ?>
        <div id="ds-content">
            <div class="ds-wrapper">
                <div class="container-fluid"> 
                    <div id="ds-product" class="ds-squares row">           
                        <div id="ds-productlist-box" class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                            <div class="message-wrap">
                                <i class="fa fa-lightbulb-o fa-2x open-message" aria-hidden="true"></i>
                                <div class="message-block col-md-5 col-xs-9">
                                    <p>Tipp: Du kannst die Bilder einfach drehen, dann bekommst du die Streifen von uns Quer!</p>
                                    <i class="fa fa-times close-message" aria-hidden="true"></i>
                                </div>
                            </div>   
                            <?php require_once("views/products/squares/material-list.php"); ?>
                        </div>
                        <div class="clearfix visible-sm-block"></div>                        
                        <div id="ds-manager-box" class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <?php require_once("views/manager/manager-box.php"); ?>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </div>
    <div class="md-modal md-effect-1" id="modal-1">
        <div class="md-content">
            <div>
                <button class="md-close">ICH SCHAUE NOCHMAL DRUBER</button>
            </div>
        </div>
    </div>
    <div class="container">
        <!-- Top Navigation -->
        <button class="md-trigger" data-modal="modal-1">Fade in &amp; Scale</button>
    </div><!-- /container -->
    <div class="md-overlay"></div><!-- the overlay element -->
    <div id="ds-overlay"></div>
    <?php if($_GET['debug']) { ?>
    <div class="ds-debug">DEBUG</div>
    <div class="ds-debug-info"></div>
    <?php } ?>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>js/script.js"></script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>js/jquery-ui/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>js/jquery.ui.touch-punch.min.js"></script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>js/jquery.fileupload.js"></script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>js/fabric.min.js"></script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>js/main.js"></script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>js/product-squares.js"></script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>ModalWindowEffects/js/modernizr.custom.js"></script>
    <!-- classie.js by @desandro: https://github.com/desandro/classie -->
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>ModalWindowEffects/js/classie.js"></script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>ModalWindowEffects/js/modalEffects.js"></script>
    <!-- for the blur effect -->
    <!-- by @derSchepp https://github.com/Schepp/CSS-Filters-Polyfill -->
    <script>
        // this is important for IEs
        var polyfilter_scriptpath = '/ModalWindowEffects/js/';
    </script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>ModalWindowEffects/js/cssParser.js"></script>
    <script type="text/javascript" src="<?php echo DS_PATH_BASE; ?>ModalWindowEffects/js/css-filters-polyfill.js"></script>

    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script type="text/javascript">
        var DS_FACEBOOK_APP_ID = '<?php echo DS_FACEBOOK_APP_ID; ?>';
    </script>
</body>
</html>
