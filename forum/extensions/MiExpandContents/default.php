<?php
/*
 Extension Name: MiExpandContents
 Extension Url: https://github.com/contructions-incongrues
 Description: Automatically expand links to viewable content in discussions
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */

// Activate extension when view a discussion
if ($Context->SelfUrl == 'comments.php') {
	$Head->AddScript('extensions/MiExpandContents/js/jquery/embedly/jquery.embedly.min.js');
	
	if ($Configuration['FEATURES']['oembed']['restricted']) {
		if (in_array($Context->Session->UserID, $Configuration['FEATURES']['oembed']['uids'])) {
			$Head->AddScript('extensions/MiExpandContents/js/behaviors-beta.js');
			$Head->AddStyleSheet('extensions/MiExpandContents/css/MiExpandContents.css');
		} else {
			$Head->AddScript('extensions/MiExpandContents/js/behaviors.js');
			include($Configuration['EXTENSIONS_PATH']."JQuery/default.php");
			include($Configuration['EXTENSIONS_PATH']."JQmedia/default.php");
		}
	} else {
		$Head->AddScript('extensions/MiExpandContents/js/behaviors-beta.js');
	}
}

if (ForceIncomingString('PostBackAction', '') == 'oEmbed') {
	// TODO : how to without a layout
}