var Playlist = function(instance, playlist, options) {
	var self = this;

	this.instance = instance; // String: To associate specific HTML with this playlist
	this.playlist = playlist; // Array of Objects: The playlist
	this.options = options; // Object: The jPlayer constructor options for this playlist

	this.current = 0;

	this.cssId = {
		jPlayer: "jquery_jplayer_",
		interface: "jp_interface_",
		playlist: "jp_playlist_"
	};
	this.cssSelector = {};

	$.each(this.cssId, function(entity, id) {
		self.cssSelector[entity] = "#" + id + self.instance;
	});

	if(!this.options.cssSelectorAncestor) {
		this.options.cssSelectorAncestor = this.cssSelector.interface;
	}

	$(this.cssSelector.jPlayer).jPlayer(this.options);

	$(this.cssSelector.interface + " .jp-previous").click(function() {
		self.playlistPrev();
		$(this).blur();
		return false;
	});

	$(this.cssSelector.interface + " .jp-next").click(function() {
		self.playlistNext();
		$(this).blur();
		return false;
	});
};

Playlist.prototype = {
		displayPlaylist: function() {
			var self = this;
			$(this.cssSelector.playlist + " ol").empty();
			for (i=0; i < this.playlist.length; i++) {
				var listitem = '';
				if (self.current == i) {
					listItem = (i === this.playlist.length-1) ? "<li class='jp-playlist-last jp-playlist-current "+ this.playlist[i].available +"'>" : "<li class='jp-playlist-current "+ this.playlist[i].available +"'>";
				} else {
					listItem = (i === this.playlist.length-1) ? "<li class='jp-playlist-last "+ this.playlist[i].available +"'>" : "<li class='"+ this.playlist[i].available +"'>";
				}
				listItem += "<a href='" + this.playlist[i].mp3 + "' id='" + this.cssId.playlist + this.instance + "_item_" + i +"' tabindex='1' class='track'>"+ this.playlist[i].name +"</a>";
				listItem += '<span class="more">';
				listItem += "[<a target='_blank' href='" + this.playlist[i].mp3 + "' title='Télécharger le fichier' class='download'>D</a>]";
				listItem += "[<a target='_blank' href='https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(this.playlist[i].mp3) + "&t="+ encodeURIComponent(this.playlist[i].name) +"' title='Partager ce morceau' class='share'>P</a>]";
				if ($(this.playlist[i].element).attr('x-mi-sourceUrl')) {
					listItem += "[<a target='_blank' href='" + $(this.playlist[i].element).attr('x-mi-sourceUrl') + "' title='Accéder à la discussion ou le morceau a été posté initialement' class='source'>S</a>]";
				}
				listItem += '</span>';
				listitem += "</li>";

				// Associate playlist items with their media
				$(this.cssSelector.playlist + " ol").append(listItem);
				$(this.cssSelector.playlist + " a.share").popupWindow({centerScreen: true, windowName: 'mi-share', height:350});
//				$(this.cssSelector.playlist + "_item_" + i).data("index", i).parent().click(function() {
//					$(this).find('a.track').click();
//				});
				$(this.cssSelector.playlist + "_item_" + i).data("index", i).click(function(event) {
					event.stopPropagation();
					if (window.webkitNotifications) {
						window.webkitNotifications.requestPermission(function() {});
					}
					var index = $(this).data("index");
					if(self.current !== index) {
						self.playlistChange(index);
					} else {
						$(self.cssSelector.jPlayer).jPlayer("play");
					}
					$(this).blur();
					return false;
				});
			}
		},
		playlistInit: function(autoplay) {
			if(autoplay) {
				this.playlistChange(this.current);
			} else {
				this.playlistConfig(this.current);
			}
		},
		playlistConfig: function(index) {
			if (this.playlist[index] != undefined) {
				$(this.cssSelector.playlist + "_item_" + this.current).removeClass("jp-playlist-current").parent().removeClass("jp-playlist-current");
				$(this.cssSelector.playlist + "_item_" + index).addClass("jp-playlist-current").parent().addClass("jp-playlist-current");
				$(this.playlist[this.current].element).removeClass('playing').removeClass('current').removeClass('paused');
				$(this.playlist[index].element).addClass('playing').addClass('current');
				this.current = index;
				$('.jp-track-info').html(this.playlist[this.current].name);
				$('.jp-view').attr('href', '#track-' + this.current);
				$(this.cssSelector.jPlayer).jPlayer("setMedia", this.playlist[this.current]);
			}
		},
		playlistChange: function(index) {
			this.playlistConfig(index);
			$(this.cssSelector.jPlayer).jPlayer("play");
		},
		playlistNext: function(autoplay) {
			var index = (this.current + 1 < this.playlist.length) ? this.current + 1 : 0;
			if (autoplay) {
				this.playlistChange(index);
			} else {
				this.playlistConfig(index);
			}
		},
		playlistPrev: function(autoplay) {
			var index = (this.current - 1 >= 0) ? this.current - 1 : this.playlist.length - 1;
			if (autoplay) {
				this.playlistChange(index);
			} else {
				this.playlistConfig(index);
			}
		}
	};
