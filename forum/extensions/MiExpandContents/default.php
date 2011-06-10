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
	$Head->AddScript('extensions/MiExpandContents/js/behaviors.js');
	$Head->AddStyleSheet('extensions/MiExpandContents/css/MiExpandContents.css');
	if(!in_array(ForceIncomingString("PostBackAction", ""), array('Releases')))
	{
		if ($Context->Configuration['FEATURES']['pagePlayer']['restricted'] && !in_array($Context->Session->UserID, $Context->Configuration['FEATURES']['pagePlayer']['uids'])) {
			$Head->AddScript('extensions/vanilla-releases/js/soundmanager2/script/soundmanager2.js');
			$Head->AddScript('extensions/MiExpandContents/js/inlineplayer.js');
		}
	}
}