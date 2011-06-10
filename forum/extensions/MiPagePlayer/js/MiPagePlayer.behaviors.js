jQuery(document).ready(function($) {
	/* Page player */
	$('a[href$=".mp3"]').addClass('playable');

	var tracks = [];
	$('a[href$=".mp3"]').each(function(index) {
		var url = $(this).attr('href');
		$(this).attr('name', 'track-' + index);
		$.data(this, 'jp-index', index);
		var trackName = url.replace(/^.*[\/\\]/g, '').replace(/%20/, ' ');;
		tracks.push({
			name: trackName,
			mp3: url,
			element: $(this),
			available: 'available'
		});
	});

	if (tracks.length > 0) {
		playlist = new Playlist('page', tracks, {});
		$('a[href$=".mp3"]').click(function(event) {
			event.preventDefault();
			if ($(this).hasClass('playing')) {
				$(this).removeClass('playing').addClass('paused');
				$('.jp-pause').click();
			} else {
				$('a[href$=".mp3"]').removeClass('paused');
				playlist.playlistConfig($.data(this, 'jp-index'));
				$('.jp-play').click();
			}
			$('#jp_interface_page').show('slide');
		});
	
		$('.jp-playlist-toggle').click(function() {
			playlist.displayPlaylist();
			$('#jp_playlist_page').toggle('slide');
		});
		$('.jp-ui-controls .close').click(function() {
			$('#jp_interface_page').hide();
		});
		$('.jp-ui-controls .collapse-toggle').click(function() {
			$('#jp_interface_page .jp-controls .jp-view').parent().animate({width:'toggle'});
			if ($('#jp_interface_page').hasClass('collapsed')) {
				$('#jp_interface_page').animate({width:'100%'}, function() {
					$('#jp_interface_page .jp-track-info').show();
					$('#jp_interface_page .jp-timer').show();
					$('#jp_interface_page .jp-controls .jp-playlist-toggle').show();
					$('#jp_interface_page .jp-progress').animate({height:'show'});
				});
				$('#jp_interface_page').removeClass('collapsed');
				$(this).html('-');
			} else {
				$('#jp_interface_page .jp-track-info').hide();
				$('#jp_interface_page .jp-timer').hide();
				$('#jp_interface_page .jp-controls .jp-playlist-toggle').hide();
				$('#jp_playlist_page').animate({height:'hide'}, function() {
					$('#jp_interface_page').animate({width:'14%'}, function() {
						$('#jp_interface_page .jp-progress').animate({height:'hide'});
					});
				});
				$('#jp_interface_page').addClass('collapsed');
				$(this).html('+');
			}
		});
		
		$('#jquery_jplayer_page').jPlayer({
			swfPath: configuration.BASEURL + 'extensions/MiJQuery/js/jquery/jplayer/',
			cssSelectorAncestor: '#jp_interface_page'
		});
		$('#jquery_jplayer_page').bind($.jPlayer.event.ready + '.pagePlayer', function(event) {
			playlist.playlistInit(false);
		});
		$('#jquery_jplayer_page').bind($.jPlayer.event.ended + '.pagePlayer', function(event) {
			playlist.playlistNext(true);
		});
		$('#jquery_jplayer_page').bind($.jPlayer.event.error + '.pagePlayer', function(event) {
			playlist.playlist[playlist.current].available = 'unavailable';
			$(playlist.playlist[playlist.current].element).addClass('unavailable').attr('title', 'Média indisponible : ' + event.jPlayer.error.message);
			playlist.playlistConfig(playlist.current + 1);
			playlist.displayPlaylist();
		});
		
		$('#jp_interface_page').show('slide');
	}
});