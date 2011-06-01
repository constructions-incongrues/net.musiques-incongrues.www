jQuery(document).ready(function($) {
	$('a[href$=".jpg"]').each(function() {
		$(this).after($('<img src="'+ $(this).attr('href') +'" />')).remove();
	});
	$('a[href$=".jpeg"]').each(function() {
		$(this).after($('<img src="'+ $(this).attr('href') +'" />')).remove();
	});
	$('a[href$=".gif"]').each(function() {
		$(this).after($('<img src="'+ $(this).attr('href') +'" />')).remove();
	});
	$('a[href$=".png"]').each(function() {
		$(this).after($('<img src="'+ $(this).attr('href') +'" />')).remove();
	});
	$('a[href$=".tiff"]').each(function() {
		$(this).after($('<img src="'+ $(this).attr('href') +'" />')).remove();
	});
	$('a[href$=".tif"]').each(function() {
		$(this).after($('<img src="'+ $(this).attr('href') +'" />')).remove();
	});
	$('a[href$=".pict"]').each(function() {
		$(this).after($('<img src="'+ $(this).attr('href') +'" />')).remove();
	});
	$('a[href$=".bmp"]').each(function() {
		$(this).after($('<img src="'+ $(this).attr('href') +'" />')).remove();
	});
	
	$('a[href$=".mp3"]').each(function() {
		var player = '<div style="background-color:red;">&nbsp;</div>';
//		.jPlayer({
//			swfPath: configuration.BASEURL + 'extensions/MiJQuery/js/jquery/jplayer/',
//			solution: 'flash, html',
//			ready: function() {
////				$(this).jPlayer('setMedia', {mp3: $(this).attr('href')});
//			}
//		});
		$(player).jPlayer();
		$(this).after(player);
	});
});