<?php
/*
 * SQUARE PRINTS - TEMPLATE - MANAGER FOR MATERIALS
 *
 */

?>

<div class="ds-manager">
    <ul class="ds-manager-tabs" role="tablist">
        <li class="ds-manager-tab ds-file <?php if(!isset($instagram_manager) && !isset($facebook_manager)) {echo 'active';}?>" role="presentation">
            <a class="nosel" href="#ds-manager-file" aria-controls="ds-managaer-file" role="tab" data-toggle="tab">
                <i class="ds-icon ds-icon-file"></i><!--<span class="ds-li-text">FOTOS UPLOADEN</span>-->
                <span class="counter-block" id="filesCounter"></span>
            </a>
        </li>
        
        <li class="ds-manager-tab ds-instagram <?php if(isset($instagram_manager)){echo 'active';}?>" role="presentation">
            <a class="nosel" href="#ds-manager-instagram" aria-controls="ds-managaer-instagram" role="tab" data-toggle="tab">
                <i class="fa fa-instagram fa-2x" aria-hidden="true"></i><!--<span class="ds-li-text">INSTAGRAM</span>-->
                <div class="counter-block" id="instCounter"></div>
            </a>
        </li>
        
        <li class="ds-manager-tab ds-facebook <?php if(isset($facebook_manager)){echo 'active';}?>" role="presentation">
            <a class="nosel" href="#ds-manager-facebook" aria-controls="ds-managaer-facebook" role="tab" data-toggle="tab">
                <i class="ds-icon ds-icon-facebook fa fa-facebook fa-2x"></i><!--<span class="ds-li-text">FACEBOOK</span>-->
                <div class="counter-block" id="fbCounter"></div>
            </a>
        </li>
        <li class="ds-manager-tab ds-faq <?php if(isset($faq_manager)){echo 'active';}?>" role="presentation">
            <a class="nosel" target="blank" href="/hilfe-faq/" aria-controls="ds-managaer-faq">
                <i class="ds-icon ds-icon-faq fa fa-question fa-2x" ></i><!--<span class="ds-li-text">FACEBOOK</span>-->
            </a>
        </li>
    </ul>
    <div class="clear"></div>
    <div class="tab-content">
        <div class="ds-manager-content ds-file tab-pane fade <?php if(!isset($instagram_manager)) {echo 'in active';}?>" id="ds-manager-file" role="tabpanel">
            <?php include "manager-file.php"; ?>
        </div>
        
        <div class="ds-manager-content ds-instagram tab-pane fade <?php if(isset($instagram_manager)){echo 'in active';}?>" id="ds-manager-instagram" role="tabpanel">
            <?php include "manager-instagram.php"; ?>
        </div>
        
        <div class="ds-manager-content ds-facebook tab-pane fade <?php if(isset($facebook_manager)){echo 'in active';}?>" id="ds-manager-facebook" role="tabpanel">
            <?php include "manager-facebook.php"; ?>
        </div>
    </div>
</div>