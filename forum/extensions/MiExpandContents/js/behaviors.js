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
	$('a[href$=".mp3"]').each(function(indexInArray, valueOfElement) {
		$(this).wrap('<div class="mi-inline-player" />');
		$(this).before('<h4>'+ $(this).text() +'</h4>');
		$(this).after($('<audio id="audio-'+ indexInArray +'" controls="controls" src="'+$(this).attr('href')+'"></audio>'));
		$(this).remove();
	});
});