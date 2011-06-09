jQuery(document).ready(function($) {
	/* Page player */
	$('a[href$=".mp3"]').addClass('playable');

	var tracks = [];
	$('a[href$=".mp3"]').each(function(index) {
		var url = $(this).attr('href');
		$(this).attr('name', 'track-' + index);
		$.data(this, 'jp-index', index);
		tracks.push({
			name: url.replace(/^.*[\/\\]/g, ''),
			mp3: url,
			element: $(this)
		});
	});

	if (tracks.length > 0) {
		playlist = new Playlist('page', tracks, {});
		$('a[href$=".mp3"]').click(function(event) {
			event.preventDefault();
			playlist.playlistChange($.data(this, 'jp-index'));
			$('#jp_interface_page').show('slide');
		});
	
		$('.jp-playlist-toggle').click(function() {
			playlist.displayPlaylist();
			$('#jp_playlist_page').toggle('slide');
		});
		$('.collapse-toggle').click(function() {
			$('#jp_interface_page .jp-track-info').animate({width:'toggle'});
			$('#jp_interface_page .jp-timer').animate({width:'toggle'});
			$('#jp_interface_page .jp-progress').animate({height:'toggle'});
			$('#jp_interface_page .jp-controls .jp-previous').animate({width:'toggle'});
			$('#jp_interface_page .jp-controls .jp-next').animate({width:'toggle'});
			$('#jp_interface_page .jp-controls .jp-view').parent().animate({width:'toggle'});
			$('#jp_interface_page .jp-controls .jp-playlist-toggle').animate({width:'toggle'});
			if ($('#jp_interface_page').hasClass('collapsed')) {
				$('#jp_interface_page').animate({width:'100%'});
				$('#jp_interface_page').removeClass('collapsed');
			} else {
				$('#jp_interface_page').animate({width:'10%'});
				$('#jp_interface_page').addClass('collapsed');
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
			playlist.playlistNext();
		});
		$('#jquery_jplayer_page').bind($.jPlayer.event.error + '.pagePlayer', function(event) {
			$(playlist.playlist[playlist.current].element).removeClass('playable').addClass('unavailable').attr('title', 'Média indisponible : ' + event.jPlayer.error.message);
			playlist.playlistConfig(playlist.current + 1);
		});
		
		$('#jp_interface_page').show();
	}
});