<?php
// Helpers
$extensionName = basename(dirname(__FILE__));

// JS configuration
$Head->AddScript(sprintf('extensions/%s/js/configuration.js', $extensionName));

// jQuery - http://www.jquery.com
$Head->AddScript(sprintf('extensions/%s/js/jquery/jquery-1.5.2.min.js', $extensionName));

// jQuery Thumbs - http://joanpiedra.com/jquery/thumbs/
$Head->AddScript(sprintf('extensions/%s/js/jquery/thumbs/jquery.thumbs.js', $extensionName));
$Head->AddStyleSheet(sprintf('extensions/%s/js/jquery/thumbs/jquery.thumbs.css',$extensionName));

# jQuery Waypoints - http://imakewebthings.github.com/jquery-waypoints/
$Head->AddScript(sprintf('extensions/%s/js/jquery/waypoints/waypoints.js', $extensionName));

# jQuery jPlayer - http://www.jplayer.org/
$Head->AddScript(sprintf('extensions/%s/js/jquery/jplayer/jquery.jplayer.min.js', $extensionName));