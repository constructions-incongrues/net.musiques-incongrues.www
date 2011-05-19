<?php
// Helpers
$extensionName = basename(dirname(__FILE__));

// jQuery - http://www.jquery.com
$Head->AddScript(sprintf('extensions/%s/js/jquery/jquery-1.5.2.min.js', $extensionName));

// jQuery Thumbs - http://joanpiedra.com/jquery/thumbs/
$Head->AddScript(sprintf('extensions/%s/js/jquery/thumbs/jquery.thumbs.js', $extensionName));
$Head->AddStyleSheet(sprintf('extensions/%s/js/jquery/thumbs/jquery.thumbs.css',$extensionName));