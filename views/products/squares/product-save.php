<?php
/*
 * SQUARE PRINTS - PRODUCT SAVE RESPOND
 *
 */

if (array_key_exists('logs', $_POST)) {
    $logs = $_POST['logs'];
}

?>

<!--<div class="ds-lightbox ds-product-savebox">-->
<!--    <div class="ds-inner">-->
<!--        <div class="ds-lightbox-content">-->
            <?php
                if($_POST['saveStatus']) {
            ?>
            <div class="row">
                <div class="col-md-12">
                    <h3><img src="img/icons/Icon-Haken.png"></h3>
                    <div class="modal-text">
                        <h1 style="color: #6cabc3">DANKE DU ROCKST!</h1>
                        <p>Dein Produkt wurde erfolgreich in den Warenkorb abgelegt</p>
                    </div>
                    <hr style="border: none;height: 15px;">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <button class="md-close md-close btn btn-default pull-left"><a href="/warenkorb/">ZUM WARENKORB</a></button>
                </div>
                <div class="col-md-6">
                    <button class="md-close btn btn-default btn-primary ds-button-blue pull-right"><a href="/shop/">WEITER EINKAUFEN</a></button>
                </div>
            </div>
            <?php
                }
                else {
            ?>

            <?php
                }
            ?>
