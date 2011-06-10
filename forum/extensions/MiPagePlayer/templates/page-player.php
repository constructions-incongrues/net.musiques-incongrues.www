<div id="jquery_jplayer_page" class="jp-jplayer" style="display:none;"></div>
<div id="jp_interface_page" class="jp-interface" style="display:none;">
	<div class="jp-progress">
		<div class="jp-seek-bar">
			<div class="jp-play-bar"></div>
		</div>
	</div>

	<div class="inner">
		<span class="jp-controls">
			<span class="jp-play"><img src="<?php echo $commentGrid->Context->Configuration['WEB_ROOT'] ?>extensions/MiPagePlayer/img/play.png" /></span>
			<span class="jp-pause"><img src="<?php echo $commentGrid->Context->Configuration['WEB_ROOT'] ?>extensions/MiPagePlayer/img/pause.png" /></span> 
			<span class="jp-previous"><img src="<?php echo $commentGrid->Context->Configuration['WEB_ROOT'] ?>extensions/MiPagePlayer/img/previous.png" /></span>
			<span class="jp-next"><img src="<?php echo $commentGrid->Context->Configuration['WEB_ROOT'] ?>extensions/MiPagePlayer/img/next.png" /></span>
			<span class="jp-playlist-toggle"><img src="<?php echo $commentGrid->Context->Configuration['WEB_ROOT'] ?>extensions/MiPagePlayer/img/playlist.png" /></span>
		</span>
	
		<span class="separator"></span>
	
		<span class="jp-track-info"></span>
		
		<span class="separator"></span>
		
		<span class="jp-timer">
			<span class="jp-current-time"></span>
			<span> / </span>
			<span class="jp-duration"></span>
		</span>

		<span class="separator"></span>
		
		<span class="jp-ui-controls">
			<span class="collapse-toggle">-</span>
			<span class="close">x</span>
		</span>

	</div>
	<div id="jp_playlist_page" class="jp-playlist" style="display:none;"><ul></ul></div>
</div>