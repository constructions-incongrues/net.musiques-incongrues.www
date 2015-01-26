$(document).ready(function() {

	$('#Discussions li.Discussion a').click(function() {
		$(this).parents('li.Discussion').removeClass('Unread');
		$(this).parents('li.Discussion').removeClass('NewComments');
		$(this).parents('li.Discussion').addClass('Read');
		$(this).parents('li.Discussion').addClass('NoNewComments');
		if ($('#Discussions li.Discussion.Unread').length == 0 && $('#Discussions li.Discussion.NewComments').length == 0) {
			favicon.change(configuration.BASEURI + 'extensions/MiInfiniteScroll/img/favicon1.png');
		}
	});
	

window.poll = function () {
    $('#NewsPoller').load(configuration.BASEURI + 'discussions/ #Discussions li.Discussion', function() {
        var ids = [];
        var newTimestamp = window.timestamp;
	var newTopics = false;
        $('#NewsPoller li.Discussion').each(function() {
            if (parseInt($(this).attr('x-timestamp')) > window.timestamp && ids[$(this).attr('id')] == undefined) {
                ids[$(this).attr('id')] = true;
                if (parseInt($(this).attr('x-timestamp')) > newTimestamp) {
                    newTimestamp = $(this).attr('x-timestamp');
		    newTopics = true;
                }
                $('#' + $(this).attr('id')).remove();
                $('#Discussions').prepend($(this));
		$('#Discussions li.Discussion a').click(function() {
			$(this).parents('li.Discussion').removeClass('Unread');
			$(this).parents('li.Discussion').removeClass('NewComments');
			$(this).parents('li.Discussion').addClass('Read');
			$(this).parents('li.Discussion').addClass('NoNewComments');
			if ($('#Discussions li.Discussion.Unread').length == 0 && $('#Discussions li.Discussion.NewComments').length == 0) {
				favicon.change(configuration.BASEURI + 'extensions/MiInfiniteScroll/img/favicon1.png');
			}
		});
            }
        });
        window.timestamp = newTimestamp;
	$('#NewsPoller').empty();

	if (newTopics) {
		favicon.animate([configuration.BASEURI + 'extensions/MiInfiniteScroll/img/favicon1.png', configuration.BASEURI + 'extensions/MiInfiniteScroll/img/favicon2.png']);
	}
    });
};
$('#Discussions').append($('<div id="NewsPoller" style="display:none;" />'));
setInterval('window.poll()', window.poller_interval);

    $('ol.PageList, .PageInfo p, div.ContentInfo.Bottom').hide();
    $('#Discussions').append($('<div style="color:red;" id="loader">CHARGEMENT DES DISCUSSIONS SUIVANTES, UN PEU DE PATIENCE :)</div>'));
    $('#loader').hide();
    $(window).scroll(function() {
        if ($(window).scrollTop() == $(document).height() - $(window).height()) {
            $('#loader').show();
            var numPage =  $('ol.PageList li:last a').attr('href').match(/discussions\/(\d+)/)[1];
            numPage++;
            $('<div />').load($('ol.PageList li:last a').attr('href') + ' #Discussions .Discussion', function() {
                $(this).appendTo('#Discussions');
                $('ol.PageList li:last a').attr('href', $('ol.PageList li:last a').attr('href').replace(/discussions\/\d+/, 'discussions/' + numPage));
                $('#loader').remove();
                $('#Discussions').append($('<div style="color:red;" id="loader">CHARGEMENT DES DISCUSSIONS SUIVANTES, UN PEU DE PATIENCE :)</div>'));
            });
        }
    });
});

