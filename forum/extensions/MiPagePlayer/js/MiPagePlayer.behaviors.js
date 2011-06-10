jQuery(document).ready(function($) {
	/* Page player */
	$('a[href$=".mp3"]').addClass('playable');

	var tracks = [];
	$('a[href$=".mp3"]').each(function(index) {
		if ($(this).hasClass('mi-player-skip')) {
			// Continue
			return true;
		}
		var url = $(this).attr('href');
		$(this).attr('name', 'track-' + index);
		$.data(this, 'jp-index', index);
		var trackName = url.replace(/^.*[\/\\]/g, '').replace(/%20/, ' ');
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
				if (window.webkitNotifications) {
					window.webkitNotifications.requestPermission(function() {});
				}
				$('.jp-play').click();
			}
			$('#jp_interface_page').show('slide');
		});
	
		$('.jp-playlist-arrow, .jp-playlist-count').click(function() {
			playlist.displayPlaylist();
			$('#jp_playlist_page').toggle('slide');
			if ($('.jp-playlist-arrow').html() == '↑') {
				$('.jp-playlist-arrow').html('↓');
			} else {
				$('.jp-playlist-arrow').html('↑');
			}
		});
		
		$('.jp-ui-controls .close').click(function() {
			$('#jp_interface_page').animate({height:'hide'});
		});
		$('.jp-ui-controls .collapse-toggle').click(function() {
			$('#jp_interface_page .jp-controls .jp-view').parent().animate({width:'toggle'});
			if ($('#jp_interface_page').hasClass('collapsed')) {
				$('#jp_interface_page').animate({width:'100%'}, function() {
					$('#jp_interface_page .jp-track-info').show();
					$('#jp_interface_page .jp-timer').show();
					$('#jp_interface_page .jp-playlist-count').show();
					$('#jp_interface_page .jp-playlist-arrow').show();
					$('#jp_interface_page .jp-progress').animate({height:'show'});
				});
				$('#jp_interface_page').removeClass('collapsed');
				$(this).html('-');
			} else {
				$('#jp_interface_page .jp-track-info').hide();
				$('#jp_interface_page .jp-timer').hide();
				$('#jp_interface_page .jp-playlist-count').hide();
				$('#jp_interface_page .jp-playlist-arrow').hide();
				$('#jp_playlist_page').animate({height:'hide'}, function() {
					$('#jp_interface_page').animate({width:'14%'}, function() {
						$('#jp_interface_page .jp-progress').animate({height:'hide'});
					});
				});
				$('#jp_interface_page').addClass('collapsed');
				$(this).html('+');
			}
		});
		
		$('#jp_interface_page .jp-play').click(function() {
			if (window.webkitNotifications) {
				window.webkitNotifications.requestPermission(function() {});
			}
		});
		$('#jp_playlist_page li').live('mouseenter', function() {
			$(this).find('span.more').show();
		});
		$('#jp_playlist_page li').live('mouseleave', function() {
			$(this).find('span.more').hide();
		});
		
		$('#jquery_jplayer_page').jPlayer({
			swfPath: configuration.BASEURL + 'extensions/MiJQuery/js/jquery/jplayer/',
			cssSelectorAncestor: '#jp_interface_page'
		});
		$('#jquery_jplayer_page').bind($.jPlayer.event.ready + '.pagePlayer', function(event) {
			$('.jp-playlist-count').html(playlist.playlist.length + ' tracks');
		});
		$('#jquery_jplayer_page').bind($.jPlayer.event.ended + '.pagePlayer', function(event) {
			playlist.playlistNext(true);
		});
		$('#jquery_jplayer_page').bind($.jPlayer.event.play + '.pagePlayer', function(event) {
			$.jwNotify({
				image: 'http://img96.imageshack.us/img96/46/faviconxa.png',
			    title: "► Radio Incongrue",
			    body: '♫ ' + playlist.playlist[playlist.current].name + ' ♫',
			    timeout: 10000
			});
//			$.jwNotifyHTML({
//			    url: configuration.BASEURL + 'extensions/MiPagePlayer/popup.php?track='+encodeURIComponent(playlist.playlist[playlist.current].name),
//			    timeout: 10000
//			});
		});
		$('#jquery_jplayer_page').bind($.jPlayer.event.error + '.pagePlayer', function(event) {
			playlist.playlist[playlist.current].available = 'unavailable';
			$(playlist.playlist[playlist.current].element).addClass('unavailable').attr('title', 'Média indisponible : ' + event.jPlayer.error.message);
			playlist.playlistChange(playlist.current + 1);
			playlist.displayPlaylist();
		});
		
		$('#jp_interface_page').show('slide');
	}
});