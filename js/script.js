$( document ).ready(function() {
	$('.close-message').click(function(){
		$('.message-block').hide(400);
	});
	$('.open-message').click(function(){
		$('.message-block').show(400);
	});
});