<?php
/*
 Extension Name: MiInfiniteScroll
 Extension Url: https://github.com/contructions-incongrues
 Description: Infinite scrolling for discussions and comments
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */
if ($Context->SelfUrl == 'index.php') {
	$Head->AddScript(sprintf('extensions/%s/js/favicon.js', basename(dirname(__FILE__))));
	$Head->AddScript(sprintf('extensions/%s/js/behaviors.js?v='.$Context->Configuration['RELEASE_TAG'], basename(dirname(__FILE__))));
}
