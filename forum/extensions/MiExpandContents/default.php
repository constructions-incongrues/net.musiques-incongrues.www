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
	$Head->AddScript('extensions/MiExpandContents/js/behaviors.js');
}

if (ForceIncomingString('PostBackAction', '') == 'oEmbed') {
	// TODO : how to without a layout
}