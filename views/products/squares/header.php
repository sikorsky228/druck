<?php
/*
 * SQUARE PRINTS - HEADER
 * 
 */

if(DS_INDEX_CHECK != "DS_LOADED") exit();

$product = $druckstudio->getProduct();
$productType = $product->getType();

$prodName = $productType->getFullname();
$materialMin = (int)$productType->getSetting('quantity-min');
$materialCount = (int)$product->getMaterialCount();
$materialCountText = sprintf('
<i class="fa fa-picture-o"></i> &nbsp;
<span class="ds-material-count">%d</span>
<span class="visible-lg-inline-block visible-md-inline-block"> / <span class="ds-material-min">%d</span>
</span>',$materialCount,$materialMin);
$prodPrice = sprintf("%.2f &euro;",$productType->getBaseprice());

?>

<header class="ds-header">
    <div class="ds-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 ds-product-title">
                    <div class="btn">
                        <i class="fa fa-th-large"></i>&nbsp;
                        <?php echo $prodName ?>
                    </div>
                </div>
<!--                <div class="clearfix visible-xs-block" style="height:50px;">&nbsp;</div>-->
                <div class="col-sm-2 col-xs-6 ds-product-number">
                    <span class="btn btn-default">
                        <?php echo $materialCountText ?>
                    </span>
                </div>
                <div class="col-sm-2 col-xs-6 ds-cartinfo">
                    <span class="btn btn-default">
                        <?php echo $prodPrice ?>
                    </span>
                </div>
                <div class="col-lg-2 col-sm-3 col-xs-4 ds-product-cancelOrder">
                    <div class="btn btn-default ds-button-cancelOrder">
                        <span class="visible-lg-block">ABBRECHEN</span>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-3 col-xs-4 ds-product-order">
                    <div class="btn btn-default btn-primary ds-button-order ds-button-blue">
                        <span class="visible-lg-block">BESTELLUNG ABSCHICKEN</span>
                        <span class="visible-md-block visible-sm-block visible-xs-block">SPEICHERN</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>