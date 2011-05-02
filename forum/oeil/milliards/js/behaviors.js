$(document).ready(function() {
	$('#top img, #middle img, #bottom img').load(function() {
		if ($('#top img').attr('complete') && $('#middle img').attr('complete') && $('#bottom img').attr('complete')) {
			$('#top img, #middle img, #bottom img').show('fade');
		}
	});
	$('a#random').click(function(event) {
		event.preventDefault();
		$('input#permalinkUrl').hide('fade');
		$('#top img, #middle img').hide('fade');
		$('#bottom img').hide('fade', function() {
			$.getJSON($('a#random').attr('href'), null, function(data, textStatus, jqXHR) {
				// Reload identity parts
				$('#top img').attr('src', 'images/parts/1/' + data.top);
				$('#middle img').attr('src', 'images/parts/2/' + data.middle);
				$('#bottom img').attr('src', 'images/parts/3/' + data.bottom);

				// Update permalink
				$('a#permalink').attr('href', urlRoot + '/'+'?part1='+data.top+'&part2='+data.middle+'&part3='+data.bottom);
				$('input#permalinkUrl').val(urlRoot+'/'+'?part1='+data.top+'&part2='+data.middle+'&part3='+data.bottom);

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