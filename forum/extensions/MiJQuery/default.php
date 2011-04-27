<?php
// Helpers
$extensionName = basename(dirname(__FILE__));

// jQuery - http://www.jquery.com
$Head->AddScript('http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js');

// jQuery Tools - http://flowplayer.org/tools/ 
$Head->AddScript('http://cdn.jquerytools.org/1.2.5/all/jquery.tools.min.js');

// jQuery Thumbs - http://joanpiedra.com/jquery/thumbs/
$Head->AddScript(sprintf('extensions/%s/js/jquery/thumbs/jquery.thumbs.js', $extensionName));
$Head->AddStyleSheet(sprintf('extensions/%s/js/jquery/thumbs/jquery.thumbs.css',$extensionName));