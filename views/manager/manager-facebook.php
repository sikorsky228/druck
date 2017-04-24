
<script>
	var DS_INSTAGRAM = <?php echo !empty($_SESSION['DS_INSTAGRAM']) ? json_encode($_SESSION['DS_INSTAGRAM']) : json_encode(array()); ?>;
</script>

<div id="dsFacebookPanel">
    <div class="not_logged" style="display:none;">
        <a href='javascript:;' class="ds_fb_login" onclick="DS_makeFacebookLogin()">
	        <div class="btn btn-default ds-button-blue">
	            <span class="visible-lg-block">Mit Facebook verbinden</span>
	        </div>
        </a>
    </div>
    <div class="logged" style="display:none;">
            <div id="dsFBAlbums">
                <a href='javascript:;' class="ds_fb_login" onclick="DS_makeFacebookLogout()"><div class="ds-facebook-infobox ds-yellow">
                        <span>LOGOUT</span>
                    </div></a>
                <div class="ds-file-list ds-facebook-list container-fluid">
                    <ul class="row" id="dcFacebookLine">

                    </ul>
                </div>
                <li class="ready col-xs-4 col-sm-3 col-md-3 col-lg-4">
                    <button type="button" class="ds-facebook-more-button" onclick="DS_loadFacebookAlbums()" style="display:none;">More</button></li>
            </div>
            <div id="dsFBPhotos" style="display:none;">
                <div class="row">
                    <div class="col-sm-10">
                        <a href="javascript:;" class="ds-back-to-albums" onclick="DS_FacebookView()">&larr; Zurück zu den Alben</a>
                    </div>
                </div>
                <form id="ds-file-form">
                    <div class="ds-file-list ds-facebook-list container-fluid">
                        <ul class="row" id="dcFacebookPhotos">

                        </ul>
                    </div>
                    <li class="ready col-xs-4 col-sm-3 col-md-3 col-lg-4">
                        <button type="button" class="ds-facebook-more-pic-button" onclick="" style="display:none;">More</button>
                    </li>
                </form>
            </div>
    </div>
</div>
