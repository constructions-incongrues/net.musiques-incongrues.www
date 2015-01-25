<?php
/*
Extension Name: Friendly Urls
Extension Url: http://vanillaforums.org/addon/30/friendly-urls
Description: Make your Vanilla installation use search-engine-friendly URLs.
Version: 1.0.1
Author: Mark O'Sullivan
Author Url: http://markosullivan.ca/
 *
 *
 * Copyright 2006 Mark O'Sullivan <http://markosullivan.ca/>
 * Copyright 2010 Damien Lebrun <dinoboff@gmail.com>
 *
 */

if (!defined('IN_VANILLA')) exit();


$Context->SetDefinition(
		'FriendlyUrlRememder',
		'<strong>Friendly Url is not yet configured.</strong> <br/>'
			. 'Copy the content of '
			. '<var>extensions/FriendlyUrls/apache.conf</var> '
			. 'to <var>/Path/To/Forum/Root/.htaccess</var>; <br/>'
			. ' then add <code>'
			. '$Configuration[\'URL_BUILDING_METHOD\'] = \'mod_rewrite\';'
			. '</code> to <var>conf/settings.php</var>.');

$FriendlyUrlPage = array(
		"account.php",
		"categories.php",
		"comments.php",
		"index.php",
		"search.php");


if ($Configuration['URL_BUILDING_METHOD'] !== 'mod_rewrite'
	&& in_array($Context->SelfUrl, $FriendlyUrlPage)
	&& $Context->Session->User->Permission(
			'PERMISSION_CHANGE_APPLICATION_SETTINGS')
) {
	$NoticeCollector->AddNotice($Context->GetDefinition('FriendlyUrlRememder'));
}
