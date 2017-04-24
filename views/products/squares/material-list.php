<?php
/*
 * SQUARE PRINTS - MATERIAL LIST
 *
 */
// Product-Base
$product = $druckstudio->getProduct();
// Image-List
$tmpSquares = $product->getMaterials();
// Settings
$settings = $product->getType()->getSettings();
$maxEntry = $settings['quantity-max'];

// Bilder umsortieren
$squares = array();
foreach($tmpSquares as $id=>$square) {
    $pos = $square->getPosition();
    $squares[$pos] = array(
        'id' => $id,
        'data' => $square
    );
}
ksort($squares);

// Zeitstempel
$nowTime = time();

?>
<div class="container-fluid">
    <ol class="ds-productlist row">
        <?php
            $count = 0;
          foreach($squares as $index=>$square) {

                  $imageURL =  $product->getThumb($square['id']); 
                $count++;
        ?>
        <li class="ds-entry ds-square filled" data-id="<?php echo $square['id']; ?>" data-name="<?php echo explode('.', $square['data']->getFile())[0]?>">
            <div class="ds-product-box squarebox">
                <div class="squarebox-inner">
                    <img src="images.php<? echo "?id=" . $imageURL; ?>">
                    <div class="ds-entry-remove">
                       <i class="fa fa-times" aria-hidden="true"></i>
                        <!--<img src="/editor/img/icons/Papierkorb.png">-->
                    </div>
                    <div class="edit-image">
                    	<!--<img src="/editor/img/icons/Stift.png">-->
                        <i class="fa fa-wrench" aria-hidden="true"></i>
                    </div>
                    <div class="warning-image" style="display: <?php if(!checkImage($product->getOriginal($square['id']))) { ?>block<?php }else{ ?>none<?php } ?>;" data-toggle="tooltip" title="deine aktuelle Bildauswahl ist zu groß! dadurch leidet die Druckqualität." data-placement="bottom">
                    <img src="/editor/img/icons/Warndreieck.png">
                    </div>
                    <div class="squarebox-bg"></div>
                </div>
            </div>
        </li>
        <?php
            }
            for($count; $count<$maxEntry; $count++) {
        ?>
        <li class="ds-entry ds-square">
            <div class="ds-product-box squarebox"><div class="squarebox-inner"></div></div>
        </li>
        <?php        
            }
        ?>
    </ol>
</div>
