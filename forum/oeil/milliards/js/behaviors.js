$(document).ready(function() {
	$('#top img').load(function() {
		$('#top img').show('slide', {direction: 'left'});
		if ($('#middle img').attr('complete') && $('#bottom img').attr('complete')) {
			$('body').css('backgroundImage', '');
		}
	});
	$('#middle img').load(function() {
		$('#middle img').show('slide', {direction: 'right'});
		if ($('#top img').attr('complete') && $('#bottom img').attr('complete')) {
			$('body').css('backgroundImage', '');
		}
	});
	$('#bottom img').load(function() {
		$('#bottom img').show('slide', {direction: 'left'});
		if ($('#middle img').attr('complete') && $('#top img').attr('complete')) {
			$('body').css('backgroundImage', '');
		}
	});
	$('a#random').click(function(event) {
		event.preventDefault();
		$('body').css('backgroundImage', 'url(images/static/loader.gif)');
		$('input#permalinkUrl').hide('fade');
		$('#top img').hide('slide', {direction: 'right'});
		$('#middle img').hide('slide', {direction: 'left'});
		$('#bottom img').hide('slide', {direction: 'right'}, function() {
			$.getJSON($('a#random').attr('href'), null, function(data, textStatus, jqXHR) {
				// Reload identity parts
				$('#top img').attr('src', 'images/parts/1/' + data.top);
				$('#middle img').attr('src', 'images/parts/2/' + data.middle);
				$('#bottom img').attr('src', 'images/parts/3/' + data.bottom);

				// Update permalink
				$('a#permalink').attr('href', urlRoot + '/'+'?part1='+data.top+'&part2='+data.middle+'&part3='+data.bottom);
				$('input#permalinkUrl').val(urlRoot + '/' + '?part1=' + data.top + '&part2=' + data.middle + '&part3=' + data.bottom);

				// Update download link
				$('a#download').attr('href', 'download.php'+'?part1='+data.top+'&part2='+data.middle+'&part3='+data.bottom);
			});
		});
	});
	
	$('a#permalink').click(function(event) {
		event.preventDefault();
		$('input#permalinkUrl').toggle('fade');
	});

	setTimeout("$('#info, #bubble').fadeOut('slow')", 10000);
	$('#content').hover(function() {
		$('#info, #bubble').fadeIn();
	});

	$('#info').mouseleave(function() {
		$('#info, #bubble').fadeOut();
	});
});