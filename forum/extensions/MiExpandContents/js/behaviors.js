jQuery(document).ready(function($) {
	$('a[href$=".jpg"]').each(function() {
		$(this).after($('<img src="'+ $(this).attr('href') +'" />')).remove();
	});
	$('a[href$=".gif"]').each(function() {
		$(this).after($('<img src="'+ $(this).attr('href') +'" />')).remove();
	});

});