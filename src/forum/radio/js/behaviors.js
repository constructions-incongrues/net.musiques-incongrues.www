// Mootools
window.addEvent('domready', function() {
    var player = new FlowerSoundPlayer({
    	swfLocation:'assets/scripts/SoundPlayer.swf',
    	controlImages:{previous:'assets/images/previous.png',next:'assets/images/next.png',play:'assets/images/play.png',pause:'assets/images/pause.png'},
    	seekbarSpcStyle: {'position':'relative','background-color':'#000','height':'3px','width':'100%','margin-top':'4px','overflow':'hidden'},
    	seekbarStyle: {'position':'absolute','background-color':'#c00','height':'3px','width':'0%','cursor':'pointer','z-index':'10'},
    	positionStyle: {'position':'absolute','left':'0%','width':'3px','height':'3px','background-color':'#fc0','z-index':'15'},
    });

    player.addEvent('ready', function() {
        this.createPagePlayer('player');
        $$('#loader').each(function(el){el.setStyle('display','none');});
        $$('.flower_soundplayer_title').grab($$('.flower_soundplayer_controls'));
        $$('.flower_soundplayer_title').grab($$('.flower_soundplayer_time'));
    });

    $$('.tracks-date').each(function(el) {
		var date = prettyDate(el.title);
		if (date != undefined) {
        	el.innerHTML = date;
		}
    });
});
