<?php
// Helpers
$extensionName = basename(dirname(__FILE__));

// JS configuration
$Head->AddScript(sprintf('extensions/%s/js/configuration.js', $extensionName));

// jQuery - http://www.jquery.com
$Head->AddScript(sprintf('extensions/%s/js/jquery/jquery-1.6.1.min.js', $extensionName));

// jQuery Thumbs - http://joanpiedra.com/jquery/thumbs/
$Head->AddScript(sprintf('extensions/%s/js/jquery/thumbs/jquery.thumbs.js', $extensionName));
$Head->AddStyleSheet(sprintf('extensions/%s/js/jquery/thumbs/jquery.thumbs.css',$extensionName));

# jQuery Waypoints - http://imakewebthings.github.com/jquery-waypoints/
$Head->AddScript(sprintf('extensions/%s/js/jquery/waypoints/waypoints.js', $extensionName));

# jQuery jPlayer - http://www.jplayer.org/
$Head->AddScript(sprintf('extensions/%s/js/jquery/jplayer/jquery.jplayer.min.js', $extensionName));

# jQuery Viewport - http://www.appelsiini.net/projects/viewport
$Head->AddScript(sprintf('extensions/%s/js/jquery/viewport/jquery.viewport.js', $extensionName));

# jQuery popupWindow - http://swip.codylindley.com/popupWindowDemo.html
$Head->AddScript(sprintf('extensions/%s/js/jquery/popupWindow/jquery.popupWindow.js', $extensionName));

# jQuery jwNotify - http://plugins.jquery.com/project/desktop-notification
$Head->AddScript(sprintf('extensions/%s/js/jquery/jwnotify/jquery.jwNotify.js', $extensionName));
