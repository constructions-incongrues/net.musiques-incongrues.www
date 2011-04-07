/**
 *
 *  This plugin was created by Ziyad Saeed (myschizobuddy at gmail dot com)
 *
 *  @version: 0.6.3
 *
 *  NOTE: This plugin uses (and requires) the Flash MP3 Player found at:
 *  http://jeroenwijering.com/?item=Flash_Single_MP3_Player
 *
 *  Audio Usage:
 *  ------------
 *  $('a[@href$=".mp3"]').jQmedia('mp3');
 *
 *  The sample script above will turn an anchor like this:
 *  <a href="song.mp3">Play my song!</a>
 *
 *  into a div like this:
 *  <div class="media mp3"><embed ...   />Play my song!/div>
 *
 *  Video Usage:
 *  ------------
 *  $('a[@href^="http://www.youtube.com/"]').jQmedia('youtube');
 *	$('a[@href^="http://video.google."]').jQmedia('google');
 *	$('a[@href^="http://vids.myspace.com/"]').jQmedia('myspace');
 *	$('a[@href^="http://www.ifilm.com/"]').jQmedia('ifilm');
 *
 *  Playlist (podcast) Usage:
 *  -------------------------
 *  $('a[@href$=".xml"]').jQmedia('playlist');
 *  XML playlist should conform to either XPSF (http://www.xspf.org/quickstart/) or RSS standard
 *  Album artwork is supported
 *  Sample playlist is provided
 *
 *  Options:
 *  -------
 *  w:200          //width of video or mp3player or podcast player
 *  h:200          //height of video or podcast player
 *  bc:0x000       //backcolor of mp3player and podcast player
 *  fc:0xFFF       //frontcolor of mp3player and podcast player
 *  lc:0xCCC       //highlight color of mp3player and podcast player
 *  autostart:true //autoplay the podcast player
 *  shuffle:true   //shuffle music for the podcast player
 *  tn:true        //Albumart thumbnail for the podcast player use <image> tag in the playlist.xml file
 *  <a class="w:200 h:200 ....." href=""></a>
 */

jQuery.fn.jqmedia = function(sourceVideo){
    return this.each(function(){
		var $this = jQuery(this);
		var cls = this.className;
		var caption = $this.text();
		var videoIDTemp = new Array();
		var url = $this.attr('href');

		/*
		Added purely for the Vanilla extension.  Thanks to Dinoboff.
		http://lussumo.com/community/?CommentID=61830*/

		var jqmediaPath = new PathFinder();
		jqmediaPath.webRoot = jqmediaPath.getRootPath('a', 'href', /people\.php.*$/);
		//Initialize the mediaplayer path
		var mediaplayer = jqmediaPath.webRoot + 'extensions/JQmedia/mediaplayer.swf';

		var embedParam = {
				src:'',
				width:'',
				height:'',
				name:'',
				flashvars:'',
				type:'application/x-shockwave-flash',
				pluginspage: 'http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash',
				wmode:'transparent',
                                autoplay:'false',
			getYoutube: function(cls,videoID) {
				this.name='youtube';
				this.src='http://www.youtube.com/v/' + videoID;
				this.width= ((cls.match(/w:(\d+)/)||[])[1]) || '425';
				this.height= ((cls.match(/h:(\d+)/)||[])[1]) || '350';
				this.flashvars='';},
			getVimeo: function(cls,videoID) {
				this.name='vimeo';
				this.src='http://vimeo.com/moogaloop.swf?clip_id=' + videoID;
				this.width= ((cls.match(/w:(\d+)/)||[])[1]) || '425';
				this.height= ((cls.match(/h:(\d+)/)||[])[1]) || '350';
				this.flashvars='';},
			getDailymotion: function(cls,videoID) {
				this.name='dailymotion';
				this.src='http://www.dailymotion.com/swf/' + videoID;
				this.width= ((cls.match(/w:(\d+)/)||[])[1]) || '425';
				this.height= ((cls.match(/h:(\d+)/)||[])[1]) || '350';
				this.flashvars='';},
			getGoogle : function(cls,videoID){
				this.name="googleVideo";
				this.src='http://video.google.com/googleplayer.swf?docId=' + videoID;
				this.width=((cls.match(/w:(\d+)/)||[])[1]) || '400';
				this.height=((cls.match(/h:(\d+)/)||[])[1]) || '326';
				this.flashvars=''; },
			getMyspace : function(cls,videoID){
				this.name="myspace";
				this.src='http://lads.myspace.com/videos/vplayer.swf';
				this.width=((cls.match(/w:(\d+)/)||[])[1]) || '430';
				this.height=((cls.match(/h:(\d+)/)||[])[1]) || '346';
				this.flashvars='m=' + videoID + '&type=video';},
			getIfilm : function(cls,videoID){
				this.name="ifilm";
				this.src='http://www.ifilm.com/efp';
				this.width=((cls.match(/w:(\d+)/)||[])[1]) || '448';
				this.height=((cls.match(/h:(\d+)/)||[])[1]) || '365';
				this.flashvars='flvbaseclip='+ videoID + '&';},
			getRevver : function(cls,videoID){
				this.name="Revver";
				this.src='http://flash.revver.com/player/1.0/player.swf';
				this.width=((cls.match(/w:(\d+)/)||[])[1]) || '480';
				this.height=((cls.match(/h:(\d+)/)||[])[1]) || '392';
				this.flashvars='mediaId='+ videoID + '&affiliateId=0&allowFullScreen=true';},
			getBrightcove : function(cls,videoID){
				this.name="bcPlayer";
				this.src='http://www.brightcove.tv/playerswf';
				this.width=((cls.match(/w:(\d+)/)||[])[1]) || '486';
				this.height=((cls.match(/h:(\d+)/)||[])[1]) || '412';
				this.flashvars='allowFullScreen=true&initVideoId='+ videoID + '&servicesURL=http://www.brightcove.tv&viewerSecureGatewayURL=https://www.brightcove.tv&cdnURL=http://admin.brightcove.com&autoStart=false';},
			getStage6 : function(cls,videoID){
				this.name="Stage6";
                                this.type="video/divx";
				this.src='http://video.stage6.com/' + videoID + '/.divx';
				this.width=((cls.match(/w:(\d+)/)||[])[1]) || '720';
				this.height=((cls.match(/h:(\d+)/)||[])[1]) || '480';
                                this.custommode='Stage6';
				this.pluginspage="http://go.divx.com/plugin/download/";},
                        getMp3 : function(cls,videoID){
				this.name="mp3";
				this.src=mediaplayer;
				this.width=((cls.match(/w:(\d+)/)||[])[1]) || '200';
				this.height=((cls.match(/h:(\d+)/)||[])[1]) || '20';
				this.backcolor=((cls.match(/bc:(0x)?([0-9a-fA-F]+)/)||[])[1]) || '0x000000';       // background color
            	this.frontcolor=((cls.match(/fc:(0x)?([0-9a-fA-F]+)/)||[])[1]) ||'0xFFFFFF';       // foreground color (buttons)
            	this.lightcolor=((cls.match(/lc:(0x)?([0-9a-fA-F]+)/)||[])[1]) ||'0xCC0066';       // hoverover button color
				this.flashvars='file='+ videoID + '&lightcolor='+ this.lightcolor+ '&backcolor='+this.backcolor+'&frontcolor='+this.frontcolor+'&showdigits=false';},
			getPlaylist : function(cls,videoID){
				this.name="playlist";
				this.src=mediaplayer;
				this.width=((cls.match(/w:(\d+)/)||[])[1]) || '200';
				this.height=((cls.match(/h:(\d+)/)||[])[1]) || '400';
				this.backcolor=((cls.match(/bc:(0x)?([0-9a-fA-F]+)/)||[])[1]) || '0x000000';       // background color
            	this.frontcolor=((cls.match(/fc:(0x)?([0-9a-fA-F]+)/)||[])[1]) ||'0xFFFFFF';       // foreground color (buttons)
            	this.lightcolor=((cls.match(/lc:(0x)?([0-9a-fA-F]+)/)||[])[1]) ||'0xCC0066';       // hoverover button color
            	this.autostart=((cls.match(/autostart:(true|false)/)||[])[1]) || 'false';     // autostart playlist on page load
            	this.shuffle=((cls.match(/shuffle:(true|false)/)||[])[1]) || 'false';       	  // shuffle playlist
            	this.thumbsinplaylist=((cls.match(/tn:(true|false)/)||[])[1]) || 'false';       	  // shuffle playlist
            	this.displayheight=((cls.match(/dh:([0-9]+)/)||[])[1]) || '150';       	  // shuffle playlist
            	this.overstretch=((cls.match(/o:(true|false|fit|none)/)||[])[1]) || 'true';       	  // shuffle playlist
				this.flashvars='file='+ videoID + '&lightcolor='+ this.lightcolor+ '&backcolor='+this.backcolor+'&frontcolor='+this.frontcolor+'&autostart='+this.autostart+'&shuffle='+this.shuffle+'&displayheight='+this.displayheight+'&thumbsinplaylist='+this.thumbsinplaylist+'&overstretch='+this.overstretch;}
		};

		//Get the VideoId depending on source
		switch(sourceVideo){
			case 'youtube' :
				videoIDTemp = url.match(/(watch\?)?v(=|\/)([A-Za-z0-9_-]+)/)||[];
				embedParam.getYoutube(cls,videoIDTemp[videoIDTemp.length-1]);
				break;
			case 'vimeo' :
				videoIDTemp = url.match(/vimeo.com\/(.*)/)||[];
				embedParam.getVimeo(cls,videoIDTemp[videoIDTemp.length-1]);
				break;
			case 'dailymotion' :
				videoIDTemp = url.match(/video\/(.*)/)||[];
				embedParam.getDailymotion(cls,videoIDTemp[videoIDTemp.length-1]);
				break;
			case 'google' :
				videoIDTemp = url.match(/(googleplayer.swf|videoplay)\?docid=([0-9-]+)/)||[];
				embedParam.getGoogle(cls,videoIDTemp[videoIDTemp.length-1]);
				break;
			case 'myspace' :
				videoIDTemp = url.match(/videoid=([0-9-]+)/)||[];
				embedParam.getMyspace(cls,videoIDTemp[videoIDTemp.length-1]);
				break;
			case 'ifilm' :
				videoIDTemp = url.match(/video\/([0-9-]+)/)||[];
				embedParam.getIfilm(cls,videoIDTemp[videoIDTemp.length-1]);
				break;
			case 'revver' :
				videoIDTemp = url.match(/watch\/([0-9-]+)/)||[];
				embedParam.getRevver(cls,videoIDTemp[videoIDTemp.length-1]);
				break;
			case 'brightcove' :
				videoIDTemp = url.match(/title=([0-9-]+)/)||[];
				embedParam.getBrightcove(cls,videoIDTemp[videoIDTemp.length-1]);
				break;
			case 'stage6' :
				videoIDTemp = url.match(/video\/([0-9-]+)/)||[];
				embedParam.getStage6(cls,videoIDTemp[videoIDTemp.length-1]);
				break;
			case 'playlist' :
				embedParam.getPlaylist(cls,url);
				break;
			default:
				break;
		};

		//embed code isn't a W3C standard, but it works across all browsers. object code is just a pain :)
		// you can easily change the code and add object tag
		var embedCode = ['<embed  '];
        embedCode.push('name="'+ embedParam.name + '" width="' + embedParam.width + '" height="' + embedParam.height +'" ');
        embedCode.push('src="' + embedParam.src + '" type="' + embedParam.type + '" pluginspage="' + embedParam.pluginspage +'" autoplay="' + embedParam.autoplay +'" ');
        if(sourceVideo != "stage6") {
            embedCode.push('flashvars="' + embedParam.flashvars + '" wmode="' + embedParam.wmode + '" ');
        }
        embedCode.push('></embed>');

		//Add caption
        if (caption && sourceVideo!='playlist') embedCode.push('<p><a rel="nofollow" target="_blank" href="' + url+ '">' + caption + '</a></p>');

        // convert anchor to div
        var $el = jQuery('<div style="width:'+ embedParam.width +'px;" class="media ' + embedParam.name + '"></div>');
        $this.after($el).remove();
        $el.html(embedCode.join(''));

        // Eolas workaround for IE (Thanks Kurt!)
        if(jQuery.browser.msie){ $el[0].outerHTML = $el[0].outerHTML; }
	});
};
