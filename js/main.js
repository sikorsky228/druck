/*
 * DRUCKSTUDIO - MAIN JAVASCRIPT ENGINE
 *
 */


// --------------------------------------------------------------------------------------------------------------------
// GLOBAL SETTINGS
// --------------------------------------------------------------------------------------------------------------------
var ajax_lock = false;
var ajax_timer = "";
var cropbox_slider_min = 100;
var cropbox_slider_max = 500;
var isVertical = 0;

    /*Counter*/

    if($('.ds-productlist li').hasClass('filled')){
        if(sessionStorage['file']){
            $('.ds-manager-tab.ds-file #filesCounter').show().text(sessionStorage['file']);        
        }
        if (sessionStorage['inst']){
            $('.ds-manager-tab.ds-instagram #instCounter').show().text(sessionStorage['inst']);            
        }
        if (sessionStorage['fb']){
            $('.ds-manager-tab.ds-facebook #fbCounter').show().text(sessionStorage['fb']);    
        }

    }else{
        sessionStorage.clear();
    }
// --------------------------------------------------------------------------------------------------------------------
// START
// --------------------------------------------------------------------------------------------------------------------
$(function(){
    DS_cleanURL();
    DS_initGallery();
    DS_initManager();
    DS_initDebug();
    DS_initSave();
    DS_cancelOrder();
    DS_productList();
    DS_responsive();
    DS_additionalFunction();
    //facebook
    DS_initFacebook();
});


function DS_productList() {
    $('.ds-button-remove').click(function(){
        var prodEntry = $(this).closest('tr');
        if(!checkElement(prodEntry)) return false;
        var PID = $(prodEntry).attr('data-id');
        DS_ajax("php/ajax/product-remove.php",{'PID':PID},function(data){
            if(data.status.type=="success") {
                $(prodEntry).remove();
            }
            else {
                DS_dialog(data.status.value,'ds-error ds-remove-error');
            }
        },true);
    });
}

function DS_cleanURL() {
    var oldURL = window.location.href;
    var newURL = oldURL;
    strStart = newURL.indexOf('?');
    strStart = (strStart>0) ? strStart : 0;
    newURL = newURL.substring(strStart);
    newURL = newURL.replace(/&state=new/gi,'');
    newURL = newURL.replace(/state=new&/gi,'');
    history.replaceState({},'',newURL);
}

function DS_initDebug() {
    $('.ds-debug').click(function(){
        $('.ds-debug-info').toggleClass('active');
        if($('.ds-debug-info').hasClass('active')) {
            $('.ds-debug-info').html("");
            DS_ajax("php/ajax/debug.php","",function(data){
                $('.ds-debug-info').html(data.html);
            },true);
        }
    });
}

function DS_additionalFunction(){
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
    //$('[data-toggle="tooltip"]').tooltip();

}
$(document).on('click', '#enable-next-step', function(){
    if ($(this).is(':checked')) {

        $('#next-step').removeAttr('disabled'); //enable input

    } else {
        $('#next-step').attr('disabled', true); //disable input
    }
});

$(document).on('click', '.md-close', function(){
    if($(this).find("a").length) {
        var url = $(this).find("a").attr('href');
        window.location.replace(url);
    }
    var modal = $( '#modal-1' );
    modal.removeClass('md-show');
});

$(document).on('mouseenter', '.squarebox-inner', function(){
    $(this).find('.edit-image').stop().animate({
        'top': $(this).height() / 2 - 35,
        'left': $(this).width() / 2 - 35,
        'opacity': 1
    }, 160);
    $(this).find('.count-number').css({
        'top': $(this).height() / 2 - 17,
        'left': 0,
        'opacity': 1
    });
    $(this).find('.arr').css({
        'left': $(this).width() / 2 - 13,
        'opacity': 1
    });
});
$(document).on('mouseleave', '.squarebox-inner', function(){
    $(this).find('.edit-image').stop().animate({'top': -25, 'opacity': 0}, 260);
    if($(this).find('.count').val() == 0){
        $(this).find('.count-number').css('opacity',0);
    }else{
        $(this).find('.count-number').css({'top': 0, 'left': 0});
    }
});

/*
$(document).ready(function(){
    $('.ds-manager').css('height',$('#ds-productlist-box').height()-20);
});
$( window ).resize(function() {
    $('.ds-manager').css('height',$('#ds-productlist-box').height()-20);
});

*/
function DS_initSave() {
    $(".ds-button-order").click(function(){
        var totalCount = $( "#ds-productlist-box li.filled" ).length;
        var modal = $( '#modal-1' );
        if(totalCount == 32){
            var modalHtml = '<div class="row"><div class="col-md-12"><h3><img src="/editor/img/icons/Icon-Lineal.png"></h3><div class="modal-text"><p>Denk bitte daran, über all deine Fotos einen prüfenden Blick zu werfen. Dein Produkt wird wie dargestellt gedruckt.</p> </div>' +
                '<div class="modal-second-text"><label class="modal-label"><input type="checkbox" id="enable-next-step">Ja, bitte so drucken</label></div></div></div>' +
                '<div class="row"><div class="col-md-6"><button class="md-close btn btn-default pull-left">ICH SCHAUE NOCHMAL DRÜBER</button></div><div class="col-md-6"><button id="next-step" disabled="disabled" class="md-close btn btn-primary ds-button-blue pull-right">OKAY</button></div></div>'
        }else{
            var modalHtml = '<div class="row"> <div class="col-md-12"> <h3><img src="img/icons/Icon-Warndreieck.png"></h3> <div class="modal-text"><p>da hat sich leider ein Fehler eingeschlichen...</p></div> </div> </div> <div class="modal-second-text"> <div class="row">' +
                '<div class="col-md-4">Fehlermeldung: </div> <div class="col-md-8">Du hast zu wenig Bilder für das ausgewählte Produkt hochgeladen</div> </div> <div class="modal-second-text"> <div class="row"> <div class="col-md-12" style="text-align: center">' +
                '<p>vielleicht hilft dir unsere <a href="#">F.A.Q</a> bei deinem Problem</p> </div> </div> </div> <div class="row"> <div class="col-md-6"> <a href="mailto:hallo@druckstud.io" style="display: block;margin: 0 auto;font-size: 0.8em;padding: 20px 40px;" class="md-close btn btn-default pull-left">SUPPORT ANSCHREIBEN</a> </div>' +
                '<div class="col-md-6"> <a style="display: block;margin: 0 auto;font-size: 0.8em;padding: 20px 40px;" class="md-close btn btn-primary ds-button-blue pull-right">FENSTER SCHLIEßEN</a> </div> </div>'
        }

        $('#modal-1 .md-content div').html(modalHtml);
        $('.md-trigger').click();
        $('#next-step').click(function (){
            DS_ajax("php/ajax/product-save.php","",function(data){
                if(data.status.type=="success") {
                    if(data.html) {
                        modalHtml = data.html;
                        $('#modal-1 .md-content div').html(modalHtml);
                        $('.md-trigger').click();
                    }
                }
                else {
                    DS_dialog(data.status.value,'ds-error ds-remove-error');
                }
            },true,true);
        })

    });
}

function DS_cancelOrder() {
    $(".ds-button-cancelOrder").click(function(){
        var url = '/shop';
        window.location.replace(url);
    });
}


// --------------------------------------------------------------------------------------------------------------------
// GALLERY
// --------------------------------------------------------------------------------------------------------------------
function DS_initGallery() {
    // Sorting of gallery
    var imgList, imgData, MID;
    
    $(".ds-productlist").sortable({
        update: function(event,ui) {
            DS_sortMaterials();
        },
        stop: function(event){
			$('.ds-productlist .filled').removeAttr( 'style' );
        },
        placeholder: {
        element: function(currentItem) {
            return $("<div></div>")[0];
        },
        update: function(event,ui) {
            	return;
            }
    	}
        
    });
    $(".ds-productlist").disableSelection();
    // Initialize Materials
    DS_initMaterialList();

}

function DS_sortMaterials() {
    imgData = {};
    imgList = $('.ds-productlist > li');
    $(imgList).each(function(pos){
        MID = $(this).attr('data-id');
        imgData[MID] = pos;
    });
    DS_ajax("php/ajax/material-sort.php",{'imgPos':imgData});
}

function DS_initMaterialList() {
    // Open Lightbox via click
    var materialClickTimer = "";
    var materialClickLock = true;
    $(".ds-productlist .ds-entry .edit-image").unbind('click').click(function(event){
        if(event.which != 1) return false;
        var entryObj = $(this).closest('.ds-entry');
        if(!$(entryObj).hasClass('filled')) return false;

            var MID = $(entryObj).attr('data-id');
            var ajaxData = {'MID' : MID};
            DS_ajax("php/ajax/material-lightbox.php",ajaxData,function(data){
                if(data.html) {
                    //console.log(data.html);
                    $('#ds-overlay').html(data.html);
                    DS_initMaterialLightbox();
                }else{
                    console.log('error data.html');
                }
            },true,true);
    });
    // Removing material of gallery


    $(".ds-productlist .ds-entry-remove").click(function(event){
        if(event.which != 1) return false;
        var entryObj = $(this).closest('.ds-entry');
        if(!$(entryObj).hasClass('filled')) return false;
        // Remove via ajax
        var MID = $(entryObj).attr('data-id');
        var ajaxData = {'MID' : MID};
        DS_ajax("php/ajax/material-remove.php",ajaxData,function(data){
            if(data.status.type=="success") {             
                var material = $('.ds-productlist .ds-entry[data-id='+MID+']');
                $(material).remove();
                if(!checkElement($('.ds-productlist .ds-entry[data-id='+MID+']'))) {
                    $('.ds-productlist').append('<li class="ds-entry ds-square"><div class="ds-product-box squarebox"><div class="squarebox-inner"></div></div></li>');
                    materialCountDecrease();
                    var materialName = $(entryObj).attr('data-name');
                    var image = $('.ds-file-list li img[data-name='+materialName+']');
                    var count = image.siblings('.count').val();
                    image.siblings('.count-number').text(count-1);
                    image.siblings('.count').val(count-1);
                    if(image.closest('.ds-manager-content').hasClass('ds-file')){
                        var a = sessionStorage['file'];
                        a--;
                        sessionStorage.setItem('file',a);
                        if (a == 0){
                            $('.ds-manager-tab.ds-file #filesCounter').hide();
                        } else{
                            $('.ds-manager-tab.ds-file #filesCounter').show().text(sessionStorage['file']);
                        }
                    }
                    if(image.closest('.ds-manager-content').hasClass('ds-instagram')){
                        var a = sessionStorage['inst'];
                        a--;
                        sessionStorage.setItem('inst',a);
                        if(a ==0){
                            $('.ds-manager-tab.ds-instagram #instCounter').hide();
                        }else{
                            $('.ds-manager-tab.ds-instagram #instCounter').show().text(sessionStorage['inst']);
                        }
                    }
                    if(image.closest('.ds-manager-content').hasClass('ds-facebook')){
                        var a = sessionStorage['fb'];
                        a--;
                        sessionStorage.setItem('fb',a);
                        if (a == 0){
                            $('.ds-manager-tab.ds-facebook #fbCounter').hide();
                        }else{
                            $('.ds-manager-tab.ds-facebook #fbCounter').show().text(sessionStorage['fb']);
                        }
                    }
                }
            }
            else {
                DS_dialog(data.status.value,'ds-error ds-remove-error');
            }
        },true,true);

    });
}


// --------------------------------------------------------------------------------------------------------------------
// MATERIAL LIGHTBOX
// --------------------------------------------------------------------------------------------------------------------
function DS_initMaterialLightbox() {
    $('.ds-lightbox-exit').click(DS_lightboxClose);
    $('.ds-lightbox-action .ds-button-cancel').click(DS_lightboxClose);
    $('.ds-lightbox-action .ds-button-reset').click(DS_lightboxReset);
    $('.ds-lightbox-action .ds-button-save').click(DS_lightboxSave);
    $('.ds-lightbox-cropbox img').load(DS_cropImageStart);
    DS_initForm();
}

// Lightbox speichern
function DS_lightboxSave() {
    // Daten sammeln
    var MID = $('#ds-material-full').attr('data-id');
    var zoom = $('#ds-cropimage-zoom').attr('data-zoom');
    var angle = $('#ds-cropimage-zoom').attr('data-angle');
    $('#ds-cropimage-zoom').attr('data-zoom',zoom);
    var cropImage = $('.ds-lightbox-cropbox img');
    var offset_x = $(cropImage).attr('data-posx');
    var offset_y = $(cropImage).attr('data-posy');
    var ajaxData = {
        'MID' : MID,
        'zoom' : zoom,
        'angle' : angle,
        'offset': {'0':offset_x,'1':offset_y}
    };
    console.log(ajaxData);
    // Save material changes via ajax
    DS_ajax("php/ajax/material-save.php",ajaxData,function(data){
        if(data.status.type=="success") {
            // Refresh image in gallery
            var MID = parseInt(data.data.MID);
            var fileURL = data.data.file;
            if(data.data.refresh && MID>=0 && fileURL) {
                var imageObj = $('.ds-productlist li[data-id='+MID+'] .squarebox-inner > img');
                if(checkElement(imageObj)) {
                    //secure url
                    fileURL = "images.php?id="+fileURL;
                    $(imageObj).attr('src',fileURL);
                    location.reload();
                }
            }
            // ZEIGE TOOLTIP ERFOLGREICH !!
            $('.ds-lightbox-action .ds-button-cancel').click();
        } else {
            DS_dialog(data.status.value,"ds-error ds-material-save");
        }
    },true,true);
}

// Lightbox zurücksetzen
function DS_lightboxReset() {
    var zoom = $('#ds-cropimage-zoom').attr('data-dzoom');
    var angle = $('#ds-cropimage-zoom').attr('data-dangle');
    $('#ds-cropimage-zoom').attr('data-zoom',zoom);
    $('#ds-cropimage-zoom').attr('data-angle',angle);
    var cropImage = $('.ds-lightbox-cropbox img');
    var offset_x = $(cropImage).attr('data-dposx');
    var offset_y = $(cropImage).attr('data-dposy');
    //$(cropImage).css('transform','rotate(' + angle + 'deg)');
    if((angle)/90 === 1 || (angle)/90 === -3){
        $('.ds-lightbox .squarebox-inner img').css('transform','translateY(-100%) rotate(' + angle + 'deg)');
        $('.ds-lightbox .squarebox-inner img').css('transform-origin','left bottom');
        isVertical = 1;
    }else if((angle)/90 === 3 || (angle)/90 === -1){
        $('.ds-lightbox .squarebox-inner img').css('transform','translateX(-100%) rotate(' + angle + 'deg)');
        $('.ds-lightbox .squarebox-inner img').css('transform-origin','top right');
        isVertical = 1;
    }else{
        $('.ds-lightbox .squarebox-inner img').css('transform','rotate(' + angle + 'deg)');
        $('.ds-lightbox .squarebox-inner img').css('transform-origin','center center');
        isVertical = 0;
    }
    $(cropImage).attr('data-posx',offset_x);
    $(cropImage).attr('data-posy',offset_y);
    $('#ds-cropimage-zoom').slider("value",zoom);
}

// Lightbox schließen
function DS_lightboxClose() {
    $(this).closest('.ds-lightbox').remove();
}
//calculatePPI(img_nH,cbox_h,img_zoom_h)
function calculatePPI(actual_image_height,intial_height,height){
   var heightR = height * (155/400);
    var print_height_cm = 4.1;
    var result = 0;
    var ratio = actual_image_height/161;
    if(actual_image_height >= 161){
        if (heightR < actual_image_height * ratio) {
            result = 101;
        } else {
            result = 0;
        }
    } else {
        result = 0;
    }
    return result;
}
// Cropbox - Bildbewegung
function DS_cropImageStart() {
    $('.ds-img-loading').show();
    var cropImage = $('.ds-lightbox-cropbox img');
    var angle = 0;
    var cropBox = $(cropImage).parent();
    var cbox_w = $(cropBox).width();
    var cbox_h = $(cropBox).height();
    var img_w = $(cropImage).width();
    var img_h = $(cropImage).height();
    var img_nW = $(cropImage).get(0).naturalWidth;
    var img_nH = $(cropImage).get(0).naturalHeight;
    var img_scale = (img_nH==0) ? 0 : (img_nW / img_nH);
    // Dragging
    var pos = 0.0;
    //Angle
    angle = $('#ds-cropimage-zoom').attr('data-angle');
    if((angle)/90 === 1 || (angle)/90 === -3){
        $('.ds-lightbox .squarebox-inner img').css('transform','translateY(-100%) rotate(' + angle + 'deg)');
        $('.ds-lightbox .squarebox-inner img').css('transform-origin','left bottom');
        isVertical = 1;
    }else if((angle)/90 === 3 || (angle)/90 === -1){
        $('.ds-lightbox .squarebox-inner img').css('transform','translateX(-100%) rotate(' + angle + 'deg)');
        $('.ds-lightbox .squarebox-inner img').css('transform-origin','top right');
        isVertical = 1;
    }else{
        $('.ds-lightbox .squarebox-inner img').css('transform','rotate(' + angle + 'deg)');
        $('.ds-lightbox .squarebox-inner img').css('transform-origin','center center');
        isVertical = 0;
    }
    DS_cropImageDragInit(isVertical);

    // Zooming
    var crop_img_zoom_w = 0;
    var crop_img_zoom_h = 0;
    var zoomValue = $('#ds-cropimage-zoom').attr('data-zoom');

    if(zoomValue<cropbox_slider_min || zoomValue>cropbox_slider_max || !zoomValue || zoomValue=="undefined") {
        zoomValue = cropbox_slider_min;
    }
    $('#ds-cropimage-zoom').slider({
        value:zoomValue,
        min:cropbox_slider_min,
        max:cropbox_slider_max,
        step:1,
        slide: function(event,ui) {
            zoomValue = ui.value;
            DS_cropImageDragFn(event,ui);
            $('#ds-cropimage-zoom').attr('data-zoom',zoomValue);
        },
        change: function(event,ui) {
            zoomValue = ui.value;
            DS_cropImageDragFn(event,ui);
            $('#ds-cropimage-zoom').attr('data-zoom',zoomValue);


            if(calculatePPI(img_nH,cbox_h,img_zoom_h) < 100){
                $('.resizer-warning-image').show();
            }else{
                $('.resizer-warning-image').hide();
            }
        },
        create: function(event,ui) {
            DS_cropImageDragFn(event,ui);
            $('.ds-img-loading').hide();
        }
    });

    $('.ds-lightbox .ds-rotate-left').click(function(){
        angle = $('#ds-cropimage-zoom').attr('data-angle');
        angle = parseInt(angle) - 90;
        if (angle == -360) {
            angle = 0;
        }
        $('#ds-cropimage-zoom').attr('data-angle',angle);
        if(Math.abs(angle)/90 == 1){
            $('.ds-lightbox .squarebox-inner img').css('transform','translateX(-100%) rotate(' + angle + 'deg)');
            $('.ds-lightbox .squarebox-inner img').css('transform-origin','top right');
            isVertical = 1;
        }else if(Math.abs(angle)/90 == 3){
            $('.ds-lightbox .squarebox-inner img').css('transform','translateY(-100%) rotate(' + angle + 'deg)');
            $('.ds-lightbox .squarebox-inner img').css('transform-origin','left bottom');
            isVertical = 1;
        }else{
            $('.ds-lightbox .squarebox-inner img').css('transform','rotate(' + angle + 'deg)');
            $('.ds-lightbox .squarebox-inner img').css('transform-origin','center center');
            isVertical = 0;
        }
        DS_cropImageDragInit(isVertical); //experiment
    });
    $('.ds-lightbox .ds-rotate-right').click(function(){
        angle = $('#ds-cropimage-zoom').attr('data-angle');
        angle = parseInt(angle) + 90;
        if (angle == 360) {
            angle = 0;
        }
        $('#ds-cropimage-zoom').attr('data-angle',angle);
        if(Math.abs(angle)/90 == 1){
            $('.ds-lightbox .squarebox-inner img').css('transform','translateY(-100%) rotate(' + angle + 'deg)');
            $('.ds-lightbox .squarebox-inner img').css('transform-origin','left bottom');
            isVertical = 1;
        }else if(Math.abs(angle)/90 == 3){
            $('.ds-lightbox .squarebox-inner img').css('transform','translateX(-100%) rotate(' + angle + 'deg)');
            $('.ds-lightbox .squarebox-inner img').css('transform-origin','top right');
            isVertical = 1;
        }else{
            $('.ds-lightbox .squarebox-inner img').css('transform','rotate(' + angle + 'deg)');
            $('.ds-lightbox .squarebox-inner img').css('transform-origin','center center');
            isVertical = 0;
        }
        DS_cropImageDragInit(isVertical); //experiment
    });


    // Buttons to set zooming 
    $('.ds-lightbox .ds-zoombutton.ds-button-plus').click(function(){
        var slider = $('#ds-cropimage-zoom');
        var sliderVal = slider.slider( "value" );
        sliderVal += 25;
        slider.slider( "value", sliderVal );
    });
    $('.ds-lightbox .ds-zoombutton.ds-button-minus').click(function(){
        var slider = $('#ds-cropimage-zoom');
        var sliderVal = slider.slider( "value" );
        sliderVal -= 25;
        slider.slider( "value", sliderVal );
    });

    // Dragging Function
    function DS_cropImageDragFn(event,ui) {
        // Breite Bilder
        if(img_scale > 1) {
            img_zoom_h = cbox_h * (zoomValue/100); 
            img_zoom_w = (img_scale==0) ? 0 : Math.ceil(img_zoom_h * img_scale);
        }
        // Hohe Bilder
        else {
            img_zoom_w = cbox_w * (zoomValue/100);
            img_zoom_h = Math.ceil(img_zoom_w / img_scale);

        }
        if(calculatePPI(img_nH,cbox_h,img_zoom_h) < 100){
            $('.resizer-warning-image').show();
        }else{
            $('.resizer-warning-image').hide();
        }
        $(cropImage).width(img_zoom_w);
        $(cropImage).height(img_zoom_h);
        DS_cropImageDragInit(isVertical);
    }
    function DS_cropImageDragInit(isVertical) {
        // Calculation Basic
        var cropPosXMax = 0;
        var cropPosXMin = 0;
        var cropPosYMax = 0;
        var cropPosYMin = 0;
        var cropPosStart = 0;
        cbox_w = $(cropBox).width();
        cbox_h = $(cropBox).height();
        img_w = $(cropImage).width();
        img_h = $(cropImage).height();
        // Calculation
        // Set Max/Min to prevent drag out of range
        cropPosStart = {
            left : $(cropImage).attr('data-posx'),
            top : $(cropImage).attr('data-posy')
        };
        cropPosStart = DS_cropImageOffset(cropImage,cropPosStart,isVertical);
        $(cropImage).css('left',cropPosStart.left+"px");
        $(cropImage).css('top',cropPosStart.top+"px");
        if(isVertical == 1){
            cropPosYMin = (cbox_w - img_w);
            cropPosXMin = (cbox_h - img_h);
        }else{
            cropPosXMin = (cbox_w - img_w);
            cropPosYMin = (cbox_h - img_h);
        }
        // Drag-Handler
        $(cropImage).draggable({
            scroll: false,
            stop: function(event, ui) {
                // Position für Bild nach Verschiebung neu berechnen
                pos = DS_cropImagePos(cropImage,isVertical);
                pos.left = pos.left.toFixed(4);
                pos.top = pos.top.toFixed(4);
                $(cropImage).attr('data-posx',pos.left);
                $(cropImage).attr('data-posy',pos.top);
            },
            drag: function(event, ui) {
                ui.position.left = Math.min( ui.position.left, cropPosXMax );
                ui.position.left = Math.max( ui.position.left, cropPosXMin );
                ui.position.top = Math.min( ui.position.top, cropPosYMax );
                ui.position.top = Math.max( ui.position.top, cropPosYMin );
            }
        });


    }
}

// Prozentuale Position vom zugeschnittenen Bild im Verhältnis zum Originalbild
// Rückgabe = Float Wert (0.0 - 1.0)
function DS_cropImagePos(cropImage,isVertical) {
    if(!checkElement(cropImage)) return false;
    var cropBox = $(cropImage).parent();
    var cbox_h = $(cropBox).height();
    var cbox_w = $(cropBox).width();
    var img_w = $(cropImage).width();
    var img_h = $(cropImage).height();
    var img_wide = (img_w > img_h) ? true : false;
    var pos_size = 0;
    var img_offset = {left:0,top:0};
    // Calc X-Offset
    img_offset.left = Math.abs( $(cropImage).position().left );
    if(isVertical == 1){
        pos_size = (img_h - cbox_h);
    }else{
        pos_size = (img_w - cbox_w);
    }
    img_offset.left = (pos_size <= 0) ? 0 : (img_offset.left / pos_size);
    // Calc Y-Offset
    img_offset.top = Math.abs( $(cropImage).position().top );
    if(isVertical == 1){
        pos_size = (img_w - cbox_w);
    }else{
        pos_size = (img_h - cbox_h);
    }
    img_offset.top = (pos_size <= 0) ? 0 : (img_offset.top / pos_size);
    return img_offset;
}

// Offset (Verschiebung) für zugeschnittene Bilder berechnen
// Rückgabewert ist die Größe des linken Abstand vom Bildausschnitt zum Originalbild (in Pixel) 
function DS_cropImageOffset(cropImage,pos_offset,isVertical) {
    if(!checkElement(cropImage)) return false;
    if(!pos_offset.left || !pos_offset.top) return false;
    var cropBox = $(cropImage).parent();
    var cbox_w = $(cropBox).width();
    var cbox_h = $(cropBox).height();
    var img_w = $(cropImage).width();
    var img_h = $(cropImage).height();
    var img_wide = (img_w > img_h) ? true : false;
    var pos_size = 0;
    var img_offset = {left:0,top:0};
    // Calc X-Offset
    if(isVertical == 1){
        pos_size = (cbox_h - img_h);
    }else{
        pos_size = (cbox_w - img_w);
    }
    img_offset.left = (pos_size >= 0) ? 0 : Math.round(pos_size * pos_offset.left);
    // Calc Y-Offset
    if(isVertical == 1){
        pos_size = (cbox_w - img_w);
    }else{
        pos_size = (cbox_h - img_h);
    }
    img_offset.top = (pos_size >= 0) ? 0 : Math.round(pos_size * pos_offset.top);

    return img_offset;
}


// --------------------------------------------------------------------------------------------------------------------
// MANAGER
// --------------------------------------------------------------------------------------------------------------------
function DS_initManager() {
    DS_managerButtons();
    DS_managerCount();
    DS_managerUploader();
}

function DS_managerButtons() {
    DS_manager_instagram_button();
    var DS_file_list = $(".ds-file-list li");
    if($(DS_file_list).size()>0) $(".ds-file-actionbar").addClass('active');
    else $(".ds-file-actionbar").removeClass('active');
    $(DS_file_list).unbind('click').click(DS_managerSelect);
}

$(document).on('click', '.arrow-up', function()
{
    //Check filetype
    var fileType;
    var inst = 0;
    if ($('li.ds-manager-tab.active').hasClass('ds-file')){
        fileType = 'file';
    }
    if ($('li.ds-manager-tab.active').hasClass('ds-instagram')){
        fileType = 'inst'
    }
    if ($('li.ds-manager-tab.active').hasClass('ds-facebook')){
        fileType = 'fb';
    }

//DS_managerFileAdd();
    var that = $(this);
    var selFiles = $(this).siblings('img');
    if(!$(this).parent().hasClass("ds-file-box-border")) {
        $(this).parent().addClass("ds-file-box-border")
    };
    // Checking if number of selection is legal
    var selFilesCount = $(selFiles).size();
    var gallerySlotsLeft = $('.ds-productlist li').size() - $('.ds-productlist li.filled').size();
    if(selFilesCount<=0 || selFilesCount>gallerySlotsLeft || gallerySlotsLeft=="NaN" || selFilesCount=="NaN") {
        var modalHtml = '<div class="row"> <div class="col-md-12"> <h3><img src="img/icons/Icon-Warndreieck.png"></h3> <div class="modal-text"><p>da hat sich leider ein Fehler eingeschlichen...</p></div> </div> </div> <div class="modal-second-text"> <div class="row">' +
            '<div class="col-md-4">Fehlermeldung: </div> <div class="col-md-8">Du hast zu wenig Bilder für das ausgewählte Produkt hochgeladen</div> </div> <div class="modal-second-text"> <div class="row"> <div class="col-md-12" style="text-align: center">' +
            '<p>vielleicht hilft dir unsere <a href="#">F.A.Q</a> bei deinem Problem</p> </div> </div> </div> <div class="row"> <div class="col-md-6"> <a href="mailto:hallo@druckstud.io" style="display: block;margin: 0 auto;font-size: 0.8em;padding: 20px 40px;" class="md-close btn btn-default pull-left">SUPPORT ANSCHREIBEN</a> </div>' +
            '<div class="col-md-6"> <a style="display: block;margin: 0 auto;font-size: 0.8em;padding: 20px 40px;" class="md-close btn btn-primary ds-button-blue pull-right">FENSTER SCHLIEßEN</a> </div> </div>'

        $('#modal-1 .md-content div').html(modalHtml);
        $('.md-trigger').click();
        //DS_dialog("Es wurden "+selFilesCount+" Bilder ausgew�hlt, aber es k�nnen nur "+gallerySlotsLeft+" Bilder in das Produkt �bernommen werden.","ds-error ds-text-fileadd");
        return false;
    }
    // Send File-Adding
    var DS_images = {};
    var inst = 0;
    $(selFiles).each(function(index) {
        DS_images[index] = $(this).attr('src');
        inst = $(this).attr('data-instagram');
    });
    
    var ajaxData = {
        'DS_IMAGES' : DS_images,
        'DS_INSTAGRAM' : inst
    };
    DS_ajax("php/ajax/manager-file-add.php",ajaxData,function(data){
        var gallery_entry,gallery_box,gallery_image,imageRefDate;
        var gallery_addCode = '<div class="ds-entry-remove"><i class="fa fa-times" aria-hidden="true"></i> </div> <div class="edit-image"><i class="fa fa-wrench" aria-hidden="true"></i></div><div class="squarebox-bg"></div>';
        var gallery_addCodeError = '<div class="ds-entry-remove"><i class="fa fa-times" aria-hidden="true"></i> </div> <div class="edit-image"><i class="fa fa-wrench" aria-hidden="true"></i></div> <div class="warning-image" data-toggle="tooltip" title="deine aktuelle Bildauswahl ist zu groß! dadurch leidet die Druckqualität." data-placement="bottom"><img src="/editor/img/icons/Warndreieck.png"></div><div class="squarebox-bg"></div>';
        $(data.data.images).each(function(index,material){
            if(material.status == "success") {
                              
                //console.log("DS_INSTAGRAM", JSON.parse(data.ds_instagram));
                if ( typeof(data.ds_instagram) != 'undefined' ) DS_INSTAGRAM = JSON.parse(data.ds_instagram);
                
                material.thumb = "images.php?id=" + material.thumb;

                gallery_box = $("<div/>");
                gallery_box_inner = $("<div/>");
                gallery_image = $("<img/>");

                gallery_entry = $('.ds-productlist li:not(.filled)').get(0);
                if(!checkElement(gallery_entry)) return false;
                $(gallery_entry).find('.ds-product-box').remove();
                $(gallery_entry).addClass('filled').attr('data-id',material.id);
                $(gallery_entry).addClass('filled').attr('data-name',material.imageName);

                //set file type
                $(gallery_entry).attr('data-type',fileType);
                //start counter
                //add filetype to cache
                sessionStorage.setItem($(gallery_entry).attr('data-id'), fileType);

                if(sessionStorage[fileType] != undefined){
                    var temp = sessionStorage[fileType];
                    temp++;
                    sessionStorage.setItem(fileType, temp);
                    if (fileType == 'file'){
                        $('.ds-manager-tab.ds-file #filesCounter').show().text(sessionStorage[fileType]);
                    }
                    if (fileType == 'inst'){
                        $('.ds-manager-tab.ds-instagram #instCounter').show().text(sessionStorage[fileType]);
                    }
                    if (fileType == 'fb'){
                        $('.ds-manager-tab.ds-facebook #fbCounter').show().text(sessionStorage[fileType]);
                    }
                //location.reload();
                }else{
                    var temp = 0;
                    temp++;
                    sessionStorage.setItem(fileType, temp);
                    if (fileType == 'file'){
                        $('.ds-manager-tab.ds-file #filesCounter').show().text(sessionStorage[fileType]);
                    }
                    if (fileType == 'inst'){
                        $('.ds-manager-tab.ds-instagram #instCounter').show().text(sessionStorage[fileType]);
                    }
                    if (fileType == 'fb'){
                        $('.ds-manager-tab.ds-facebook #fbCounter').show().text(sessionStorage[fileType]);
                    }
                }

                $(gallery_box).addClass('ds-product-box squarebox');
                $(gallery_image).attr('src',material.thumb).appendTo( $(gallery_box_inner) ); 

                $(gallery_box_inner).addClass('squarebox-inner').appendTo( $(gallery_box) );
                $(gallery_box).appendTo( $(gallery_entry) );
                if (material.msg == 'dpi-error') {
                    $(gallery_addCodeError).insertAfter( $(gallery_box).find('img') );
                } else {
                    $(gallery_addCode).insertAfter( $(gallery_box).find('img') );
                }
                /*
                if(material.instagram){
                    selFiles.attr('data-name',material.imageName);
                    //selFiles.attr('data-instagram','false');
                    console.log(material);
                    selFiles.attr('src', material.upload);
                }
                */
                materialCountIncrease();
                var total = parseInt(that.siblings('.count').val())+1;
                that.siblings('.count').val(total);
                that.siblings('.count-number').text(total);
            }
            else {
                var errorMsg = material.msg;
                DS_dialog(errorMsg,'ds-error ds-error-materialAdd');
            }
        });
        //DS_managerSelectNone();
        DS_initMaterialList();
    },true,true);
});

$(document).on('click', '.arrow-down', function()
{

//Check filetype
    var fileType;
    if ($('li.ds-manager-tab.active').hasClass('ds-file')){
        fileType = 'file'
    }
    if ($('li.ds-manager-tab.active').hasClass('ds-instagram')){
        fileType = 'inst'
    }
    if ($('li.ds-manager-tab.active').hasClass('ds-facebook')){
        fileType = 'fb'
    }


    var that = $(this);
    var checkThumbImage = parseInt(that.siblings('.count').val());
    var selFiles = $(this).siblings('img');
    // Checking if number of selection is legal
    var selFilesCount = $(selFiles).size();
    var imageName = $(this).siblings('img').attr('data-name');
    var inst = $(this).siblings('img').attr('data-instagram');
    if(selFilesCount<=0 || selFilesCount=="NaN") {
        var modalHtml = '<div class="row"> <div class="col-md-12"> <h3><img src="img/icons/Icon-Warndreieck.png"></h3> <div class="modal-text"><p>da hat sich leider ein Fehler eingeschlichen...</p></div> </div> </div> <div class="modal-second-text"> <div class="row">' +
            '<div class="col-md-4">Fehlermeldung: </div> <div class="col-md-8">Du hast zu wenig Bilder für das ausgewählte Produkt hochgeladen</div> </div> <div class="modal-second-text"> <div class="row"> <div class="col-md-12" style="text-align: center">' +
            '<p>vielleicht hilft dir unsere <a href="#">F.A.Q</a> bei deinem Problem</p> </div> </div> </div> <div class="row"> <div class="col-md-6"> <a href="mailto:hallo@druckstud.io" style="display: block;margin: 0 auto;font-size: 0.8em;padding: 20px 40px;" class="md-close btn btn-default pull-left">SUPPORT ANSCHREIBEN</a> </div>' +
            '<div class="col-md-6"> <a style="display: block;margin: 0 auto;font-size: 0.8em;padding: 20px 40px;" class="md-close btn btn-primary ds-button-blue pull-right">FENSTER SCHLIEßEN</a> </div> </div>'

        $('#modal-1 .md-content div').html(modalHtml);
        $('.md-trigger').click();
        //DS_dialog("Es wurden keine Bilder ausgew�hlt!","ds-error ds-text-fileAdd");
        return false;
    }
    // L�schen
    var DS_images = {};
    $(selFiles).each(function(index) {
        DS_images[index] = $(this).attr('src');
    });
    var ajaxData = {
        'DS_IMAGES' : DS_images
    };
    if(checkThumbImage <= 0){
        if(inst == 1 || inst === 'false')
            return;
        DS_ajax("php/ajax/manager-file-remove.php",ajaxData,function(data){
            if(data.status.type == "success") {
                var files = data.data.report;
                $(files).each(function(index,fileInfo){
                    if(fileInfo.status != "error") {
                        $('.ds-file-list li img[data-name="'+fileInfo.file+'"]').closest('li').remove();
                    }
                });
                //DS_managerSelectNone();
                var total = parseInt(that.siblings('.count').val())-1;
                that.siblings('.count').val(total);
                that.siblings('.count-number').text(total);
                
                if ( typeof(data.ds_instagram) != 'undefined' ) DS_INSTAGRAM = JSON.parse(data.ds_instagram);
            }
            else {
                var errorMsg = data.status.value;
                DS_dialog(errorMsg,'ds-error ds-error-fileRemove');
            }
        });
    }else{
        var entryObj = $( "#ds-productlist-box li[data-name='"+imageName+"']" ).last();
        //if(!$(entryObj).hasClass('filled')) return false;
        // Remove via ajax
        var MID = $(entryObj).attr('data-id');
        var ajaxData = {'MID' : MID};
        DS_ajax("php/ajax/material-remove.php",ajaxData,function(data){
            if(data.status.type=="success") {
                var material = $('.ds-productlist .ds-entry[data-id='+MID+']');
                $(material).remove();
                if(!checkElement($('.ds-productlist .ds-entry[data-id='+MID+']'))) {
                    $('.ds-productlist').append('<li class="ds-entry ds-square"><div class="ds-product-box squarebox"><div class="squarebox-inner"></div></div></li>');
                    materialCountDecrease();
                }

                if ( typeof(data.ds_instagram) != 'undefined' ) DS_INSTAGRAM = JSON.parse(data.ds_instagram);

                //Add to cache
                if(sessionStorage[fileType] != undefined){
                    var temp = sessionStorage[fileType];
                    temp--;
                    sessionStorage.setItem(fileType, temp);
                    if (fileType == 'file'){
                        if (temp == 0){
                            $('.ds-manager-tab.ds-file #filesCounter').hide();
                        }else{
                            $('.ds-manager-tab.ds-file #filesCounter').show().text(sessionStorage[fileType]);
                        }
                    }
                    if (fileType == 'inst'){
                        if (temp == 0){
                            $('.ds-manager-tab.ds-instagram #instCounter').hide();
                        }else{
                            $('.ds-manager-tab.ds-instagram #instCounter').show().text(sessionStorage[fileType]);
                        }
                    }
                    if (fileType == 'fb'){
                        if (temp == 0){
                            $('.ds-manager-tab.ds-facebook #fbCounter').hide();
                        }else{
                            $('.ds-manager-tab.ds-facebook #fbCounter').show().text(sessionStorage[fileType]);
                        }
                    }
                }

                
                if ( typeof(data.ds_instagram) != 'undefined' ) DS_INSTAGRAM = JSON.parse(data.ds_instagram);
            }
            else {
                DS_dialog(data.status.value,'ds-error ds-remove-error');
            }
        },true,true);
        var total = parseInt(that.siblings('.count').val())-1;
        that.siblings('.count').val(total);
        that.siblings('.count-number').text(total);
        if(total==0){
            if($(this).parent().hasClass("ds-file-box-border")) {
                $(this).parent().removeClass("ds-file-box-border")
            };
        }
    }
});

// FILE SELECTION
function DS_managerSelect() {
    if(!$(this).hasClass('ready')) return false;
    // Selection
    // $(this).toggleClass("selected");
    var selFilesCount = $('.ds-manager-content .ds-file-list li.selected').size();
    DS_fileSelCheckCount();
    // Show action buttons
    if(selFilesCount>0) {
        DS_managerActionButtonOpen();
    }
    // Hide action buttons
    else {
        DS_managerActionButtonClose();
    }
}

function DS_managerActionButtonOpen() {
    $('.ds-file-select-action').stop().slideDown(250);
}
function DS_managerActionButtonClose() {
    $('.ds-file-select-action').stop().slideUp(250);
}
function DS_fileSelCheckCount() {
    var selFilesCount = $('.ds-manager-content .ds-file-list li.selected').size();
    var gallerySlotsLeft = $('.ds-productlist li').size() - $('.ds-productlist li.filled').size();
    if(selFilesCount>gallerySlotsLeft) {
        $('.ds-file .ds-error-addfile').slideDown(250);
        return false;
    } else {
        $('.ds-file .ds-error-addfile').slideUp(250);
        return true;
    }
}

function DS_managerCount() {
    var allUploadedImage = $('#ds-file-form .ds-file-list li img');
    $.each(allUploadedImage, function (index, image) {
        var totalCount = $( "#ds-productlist-box li[data-name='"+$(image).attr('data-name')+"']" ).length;
        $(image).siblings('.count-number').text(totalCount);
        $(image).siblings('.count').val(totalCount);
        if(totalCount > 0){
            if(!$(image).parent().hasClass("ds-file-box-border")) {
                $(image).parent().addClass("ds-file-box-border")
            };
        }else{
            $(image).siblings('.count-number').css('opacity', 0);
        }
    });
}

// FILE UPLOADER
function DS_managerUploader() {
    var uploadElem = $('#ds-file-uploadfield');
    var uploaderFileList = $('#ds-manager-file .ds-file-list ul');
    var ajaxData = {
        'DS_AJAX_REQUEST' : true,
        'DS_AJAX_URL' : './php/ajax/manager-file-upload.php'
    };
    $(uploadElem).fileupload({
        sequentialUploads: true,
        url: './index.php',
        dataType: 'json',
        type: 'POST',
        formData: ajaxData,
        done: function (e, data) {
            if(data.result.status.type == "success") {
                
                if ( typeof(data.ds_instagram) != 'undefined' ) DS_INSTAGRAM = JSON.parse(data.ds_instagram);
                
                var fileBox = $(data.context).find('.ds-file-box .squarebox-inner');
                if(checkElement(fileBox)) {
                    var imageRefDate = new Date();
                    var img_src = unescape(data.result.data.src);
                    img_src = "images.php?id="+img_src;
                    var img_dir = unescape(data.result.data.dir);
                    var img_name = encodeURIComponent(data.result.data.name);
                    var img_wide = (data.result.data.wide) ? 'landscape' : 'portrait';
                    var img = $("<img/>").attr('src',img_src).attr('data-name',img_name).addClass(img_wide).appendTo( $(fileBox) );
                    DS_managerButtons();
                    return true;
                }
            }
            // Error handling - illegal upload or no container found
            $(data.context).remove();
            var errorMsg = data.result.status.value;
            DS_dialog(errorMsg,'ds-error ds-error-upload');
        },
        add: function(e, data) {
            var uploadingElem = $('<li/>').addClass('col-xs-4 col-sm-3 col-md-3 col-lg-4').append(
                $('<div/>').addClass('ds-file-box squarebox').append(
                    $('<div/>').addClass('squarebox-inner').append(
                        $('<div/>').addClass('squarebox-bg'),
                        $('<input>').attr({
                            type: 'hidden',
                            class: 'count',
                            value: '0'
                        }),
                        $('<div/>').addClass('arr arrow-up'),
                        $('<div/>').addClass('arr arrow-down'),
                        $('<span/>').addClass('count-number').text('0').css('opacity',0),
                        $('<div/>').addClass('ds-uploading').height('100%')
                    )
                )
            );
            $.each(data.files, function (index, file) {
                data.context = $(uploadingElem).appendTo('#ds-manager-file .ds-file-list ul');
            });
            data.submit();
        },
        progress: function(e, data) {
            var processBox = $(data.context).find('.ds-uploading');
            var progress = (1 - (data.loaded/data.total)) * 100;
            $(processBox).stop().animate({height:progress+"%"},500,'linear',function(){
                if(progress<=0) {
                    $(processBox).remove();
                    $(data.context).addClass('ready');
                }

            });
        },
        dropZone: $('.ds-file-uploadbox'),
        paramName: 'ds_uploads[]'
    });

    // Activate Uploadbox function
    $('.ds-file-uploadbox').click(function(){
        $('#ds-file-uploadfield').click();
    });
}

//Instagram more Button
function DS_manager_instagram_button() {
    var buttonName = ".ds-instagram-more-button";
    $(buttonName).click(function(event) {
        var lastImagesToShow = $(".ds-instagram-list").find(".count").length;
        var ajaxData = {'lastImagesToShow' : lastImagesToShow};
        DS_ajax('php/ajax/manager-instagram-more.php',ajaxData,function(response){
            showNextInstagramImages(response);
        },false,true);
        event.preventDefault();
    });
    $(buttonName).text("MORE");
    $(buttonName).css('color', 'white');
}

function showNextInstagramImages(response) {
    var html = "<ul class='row'>";
    for (var i = 0; i < response.length; i++) {
        console.log("NEXT URL: "+response[i]);
        html = html+"<li class='ready col-xs-4 col-sm-3 col-md-3 col-lg-4'><div class='ds-file-box squarebox'>" +
            "<div class='squarebox-inner'>"+
            "<input type='hidden' class='count' value='0'>"+
            "<div class='squarebox-bg'></div>"+
            "<div class='arr arrow-up'></div>"+
            "<div class='arr arrow-down'></div>"+
            "<span class='count-number'>0</span>"+
            "<img src='"+response[i].url+"' data-name='"+response[i].dataName+"' data-instagram='1' /></div></div></li>"
    }
    html = html+"</ul>";
    $("div.ds-instagram-list").html(html);
    DS_managerCount();
}


// --------------------------------------------------------------------------------------------------------------------
// AJAX COMMUNICATION 
// --------------------------------------------------------------------------------------------------------------------
function DS_ajax(url,data,ajaxCallback,lock,loading) {
    if(!url || url=="undefined") return false;
    if(lock != true) lock = false;
    if(loading != true) loading = false;
    // AJAX LOCKING
    if(lock===true) {
        if(ajax_lock) {
            ajax_timer = setTimeout(function(){ajax_lock = false;},2000);
            return false;
        }
        // LOCK [ON]
        ajax_lock = true;
    }
    // AJAX DATA
    var ajaxData = {
        "DS_AJAX_REQUEST" : true,
        "DS_AJAX_URL" : url,
        "DS_AJAX_DATA" : data
    };
    // LOADING SCREEN [ON]
    if(loading===true){
        $('body').append('<div class="ds-loading"><i class="fa fa-circle-o-notch fa-spin fa-5x"></i></div>');
    }
    // AJAX SEND
    $.ajax({
        url: "index.php",
        type: "POST",
        data: ajaxData,
        dataType : 'JSON'
    }).done(function(data){
        if(typeof ajaxCallback === "function") {
            ajaxCallback(data);
        }
    }).always(function(){
        clearTimeout(ajax_timer);
        // LOCK [OFF]
        if(ajax_lock) {
            ajax_lock = false;
        }
        // LOADING SCREEN [OFF]
        if(loading===true){
            $('.ds-loading').remove();
        }
    }).error(function(data) {
        console.log("AJAX FAILED: "+data);
    });
}


// --------------------------------------------------------------------------------------------------------------------
// RESPONSIVE CHANGES
// --------------------------------------------------------------------------------------------------------------------
function DS_responsive() {
    $(window).resize(function(){
        var cropboxElem = $('.ds-lightbox .ds-lightbox-cropbox');
        if(checkElement(cropboxElem)) {
            var sliderVal = $('#ds-cropimage-zoom').slider('value');
            $('#ds-cropimage-zoom').slider('value',sliderVal)
        }
    });
}


// --------------------------------------------------------------------------------------------------------------------
// FORM ELEMENTS
// --------------------------------------------------------------------------------------------------------------------
function DS_initForm() {
    DS_radioSet();
}

function DS_radioSet() {
    $('.ds-form-radiobox .ds-form-radio').click(function(){
        $(this).siblings('.ds-form-radio').removeClass('active');
        $(this).addClass('active');
    })
}

function DS_dialog(dialog_text,dialog_class) {
    if(!dialog_text || dialog_text=="undefined") return false;
    if(!dialog_class || dialog_class=="undefined") dialog_class = "";
    var DS_msg = $('<div/>').addClass('ds-msg '+dialog_class).text(dialog_text).appendTo($('#ds-overlay'));
    var dialog_title = "HINWEIS";
    $(DS_msg).dialog({resizable:false,title:dialog_title});
}


// --------------------------------------------------------------------------------------------------------------------
// HELPERS
// --------------------------------------------------------------------------------------------------------------------
function checkElement(elem) {
    if(!elem || elem=="undefined") return false;
    if($(elem).size()<=0) return false;
    return true;
}

function materialCountDecrease() {
    var materialCount = parseInt( $('.ds-material-count').text() );
    materialCount = (materialCount>0) ? materialCount-1 : 0;
    $('.ds-material-count').text(materialCount);
}
function materialCountIncrease() {
    var materialCount = parseInt( $('.ds-material-count').text() );
    materialCount++;
    $('.ds-material-count').text(materialCount);
}


// --------------------------------------------------------------------------------------------------------------------
// FACEBOOK
// --------------------------------------------------------------------------------------------------------------------
var FB_loggedIn = false;
var FB_limit = 12;
var FB_offset = 0;
var FB_limit_pic = 12;
var FB_offset_pic = 0;
var FB_albums;
var FB_photos;
function DS_initFacebook(){
    //console.log('FACEBOOK');
    //console.log(DS_FACEBOOK_APP_ID);
    FB.init({
        appId  : DS_FACEBOOK_APP_ID,
        status : true, // check login status
        cookie : true, // enable cookies to allow the server to access the session
        xfbml  : true  // parse XFBML
    });

    DS_checkFacebookLogin();

}

function DS_checkFacebookLogin(){
    FB.getLoginStatus(function(response) {
        if (response.status === 'connected') {
            //console.log('Logged in.');
            DS_FacebookIsLogged();
        }
        else {
            //console.log('Not logged');
            //FB.login();
            DS_FacebookNotLogged();
        }
    });
}


function DS_FacebookNotLogged(){
    $('#dsFacebookPanel .not_logged').show();
    $('#dsFacebookPanel .logged').hide();
}

function DS_FacebookIsLogged(){
    $('#dsFacebookPanel .not_logged').hide();
    $('#dsFacebookPanel .logged').show();

    $('#dsFBAlbums').show();
    $('#dsFBPhotos').hide();

    DS_loadFacebookAlbums();
}

function DS_FacebookMoreButton(show){
    if (!show){
        $('#dsFacebookPanel .ds-facebook-more-button').hide();
    }else{
        $('#dsFacebookPanel .ds-facebook-more-button').show();
    }
}

function DS_makeFacebookLogin(){
    FB.login(DS_FacebookIsLogged, {scope: 'user_photos'});
}

function DS_makeFacebookLogout(){
    FB.logout(DS_FacebookNotLogged);
}

function DS_loadFacebookAlbums(){

    FB.api('/me?fields=albums.fields(id,name,cover_photo,photos.fields(name,picture,source)).limit('+FB_limit+').offset('+FB_offset+')', function(response)
    {

        if ( !response.albums ){
            DS_FacebookMoreButton();
            //return false;
        }else {

            FB_offset += FB_limit;

            FB_albums = response.albums.data;

            //console.log('RESPONSE', response.albums.data);

            DS_FacebookFindCovers();

            DS_FacebookShowAlbums();

            if ( !response.albums.paging || !response.albums.paging.next ){
                DS_FacebookMoreButton();
            }else{
                DS_FacebookMoreButton(true);
            }
        }

    });

}

var extend_func = {
    findCover: function () {
        for (var j = 0; j < this.photos.data.length; j++) {
            if (this.photos.data[j].id == this.cover_photo.id)
                this.cover_photo.photo = this.photos.data[j];
        }

        return this;
    }
};

String.prototype.Filename = function(){
    return this.substring(this.lastIndexOf('/')+1).split('.')[0]//.substring(this.lastIndexOf('.')+1);
}

function DS_FacebookFindCovers(){
    for(var i=0; i < FB_albums.length; i++){
        //console.log('I:', i, 'Object:', FB_albums[i]);
        //console.log( Object.getPrototypeOf(FB_albums[i]) );
        FB_albums[i] = $.extend(FB_albums[i], extend_func);
        //console.log(FB_albums[i]);
        FB_albums[i].findCover();
    }
}

function DS_FacebookShowAlbums(){
    //console.log('Albums', FB_albums);

    DS_FacebookView();

    html = '';
    for(var i=0; i<FB_albums.length; i++){
        var A = FB_albums[i];
        html += '<li class="col-xs-4 col-sm-3 col-md-3 col-lg-4" onclick="DS_loadFacebookPhotos('+ A.id+')">';
            html += '<div class="ds-file-box squarebox">';
                html += '<div class="squarebox-inner">';
                    //html += '<input type="hidden" class="count" value="0">';
                    html += '<div class="squarebox-bg-album"></div>';
                    //html += '<div class="arr arrow-up"></div>';
                    //html += '<div class="arr arrow-down"></div>';
                    //html += '<span class="count-number-album">'+ A.photos.data.length+'</span>';
                    //html += '<img src="'+ A.cover_photo.photo.picture+'"/>';
                    html += '<div style="width:100% !important; height:100% !important; background: url('+ A.cover_photo.photo.source+') center center no-repeat; background-size:cover;"></div>';
                    html += '<span class="title-album">' + A.name + '</span>';
                html += '</div>';
            html += '</div>';
        html += '</li>';
    }

    $('#dcFacebookLine').append(html);
}

function DS_FacebookView(view){

    switch(view){
        case 'photos':
            $('#dsFBAlbums').hide();
            $('#dsFBPhotos').show();
            break;
        default:
            $('#dcFacebookPhotos').empty();
            FB_offset_pic = 0;
            $('#dsFBAlbums').show();
            $('#dsFBPhotos').hide();
            break;
    }
}

function DS_FacebookMorePicButton(show, album_id){
    if (!show){
        $('#dsFacebookPanel .ds-facebook-more-pic-button').hide();
    }else{
        $('#dsFacebookPanel .ds-facebook-more-pic-button').attr("onclick", "DS_loadFacebookPhotos("+album_id+")").show();
    }
}

function DS_loadFacebookPhotos(album_id){

    DS_FacebookView('photos');

    FB.api('/'+album_id+'/photos?fields=name,picture,source&limit='+FB_limit_pic+'&offset='+FB_offset_pic+'', function(response)
    {

        if ( !response.data || !response.data.length ){
            DS_FacebookMorePicButton();
            //return false;
        }else {

            FB_offset_pic += FB_limit_pic;

            FB_photos = response.data;

            DS_FacebookShowPhotos();

            if ( !response.paging || !response.paging.next ){
                DS_FacebookMorePicButton();
            }else{
                DS_FacebookMorePicButton(true, album_id);
            }

        }

    });
}

function DS_FacebookShowPhotos(){
    //console.log('Photos', FB_photos);
    
    //console.log('DS_INSTAGRAM', DS_INSTAGRAM);

    html = '';
    for(var i=0; i<FB_photos.length; i++){
        var A = FB_photos[i];
        
        var is_uploaded = DS_INSTAGRAM !== undefined && DS_INSTAGRAM[A.source] !== undefined;
        var url_replace = is_uploaded ? DS_INSTAGRAM[A.source] : A.source;
        var indicator_inst = is_uploaded ? 'false' : '1';

        html += '<li class="ready col-xs-4 col-sm-3 col-md-3 col-lg-4">';
            html += '<div class="ds-file-box squarebox">';
                html += '<div class="squarebox-inner">';
                    html += '<input type="hidden" class="count" value="0">';
                    html += '<div class="squarebox-bg"></div>';
                    html += '<div class="arr arrow-up"></div>';
                    html += '<div class="arr arrow-down"></div>';
                    html += '<span class="count-number">0</span>';
                    html += '<div style="width:100% !important; height:100% !important; background: url('+ A.source+') center center no-repeat; background-size:cover;"></div>';
                    html += '<img src="' + url_replace + '" data-name="' + url_replace.Filename() + '" data-instagram="' + indicator_inst + '" style="display:none;"/>';
                html += '</div>';
            html += '</div>';
        html += '</li>';
    }

    $('#dcFacebookPhotos').append(html);
    
    DS_managerCount()
}
