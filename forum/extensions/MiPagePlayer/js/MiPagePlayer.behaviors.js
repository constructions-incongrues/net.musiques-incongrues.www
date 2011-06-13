jQuery(document).ready(function($) {
	/* Page player */
	$('a[href$=".mp3"]').addClass('playable');

	// Collect MP3 links on page
	var tracks = [];
	$('a[href$=".mp3"]').each(function(index) {
		if ($(this).hasClass('mi-player-skip')) {
			// Continue
			return true;
		}
		var url = $(this).attr('href');
		$(this).attr('name', 'track-' + index);
		$.data(this, 'jp-index', index);
		var trackName = decodeURIComponent(url).replace(/^.*[\/\\]/g, '');
		if ($(this).attr('x-mi-trackName')) {
			trackName = $(this).attr('x-mi-trackName');
		}
		tracks.push({
			name: trackName,
			mp3: url,
			element: $(this),
			available: 'available'
		});
	});
	
	// Setup page player
	if (tracks.length > 0) {
		playlist = new Playlist('page', tracks, {
			swfPath: configuration.BASEURI + 'extensions/MiJQuery/js/jquery/jplayer',
			cssSelectorAncestor: '#jp_interface_page',
			solution: 'html, flash'
		});

		// Player events
		// -- Player successfully loaded
		$('#jquery_jplayer_page').bind($.jPlayer.event.ready + '.pagePlayer', function(event) {
			// Draw playlist (TODO : rename corresponding method)
			playlist.displayPlaylist();
			playlist.playlistConfig(0);

			// Store initial player state
			$.data($('#jquery_jplayer_page')[0], 'playing', false);
			
			// Show footer player
			$('#jp_interface_page').show('slide');
		});
		
		// Footer controls
		// -- Playlist button
		$('span.jp-button.playlist').click(function(event) {
			$('#jp_playlist_page').animate({height:'toggle'});
		});
		
		// -- Close button
		$('span.jp-button.close').click(function() {
			$('#jp_playlist_page').hide();
			$('#jp_interface_page').animate({height:'hide'});
		});

		// Player events
		// -- play
		$('#jquery_jplayer_page').bind($.jPlayer.event.play + '.pagePlayer', function(event) {
			$.data($('#jquery_jplayer_page')[0], 'playing', true);
			$.jwNotify({
				image: 'http://img96.imageshack.us/img96/46/faviconxa.png',
			    title: "► Radio Incongrue",
			    body: '♫ ' + playlist.playlist[playlist.current].name + ' ♫',
			    timeout: 3000
			});
		});
		
		// -- pause
		$('#jquery_jplayer_page').bind($.jPlayer.event.pause + '.pagePlayer', function(event) {
			$.data($('#jquery_jplayer_page')[0], 'playing', false);
		});
		
		// -- ended
		$('#jquery_jplayer_page').bind($.jPlayer.event.ended + '.pagePlayer', function(event) {
			playlist.playlistNext(true);
		});
		
		// -- error
		$('#jquery_jplayer_page').bind($.jPlayer.event.error + '.pagePlayer', function(event) {
			playlist.playlist[playlist.current].available = 'unavailable';
			$(playlist.playlist[playlist.current].element).addClass('unavailable').attr('title', 'Média indisponible : ' + event.jPlayer.error.message);
			playlist.playlistNext($.data($('#jquery_jplayer_page')[0], 'playing'));
			playlist.displayPlaylist();
		});
		
		$('a[href$=".mp3"]').click(function(event) {
			event.preventDefault();
			if ($(this).hasClass('playing')) {
				$(this).removeClass('playing').addClass('paused');
				$('#jquery_jplayer_page').jPlayer('pause');
			} else {
				$('a[href$=".mp3"]').removeClass('paused');
				playlist.playlistConfig($.data(this, 'jp-index'));
				if (window.webkitNotifications) {
					window.webkitNotifications.requestPermission(function() {});
				}
				$('#jquery_jplayer_page').jPlayer('play');
			}
		});
		
		$('#jp_interface_page .jp-play').click(function() {
			if (window.webkitNotifications) {
				window.webkitNotifications.requestPermission(function() {});
			}
		});
	}
});