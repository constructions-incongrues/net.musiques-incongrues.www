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
});