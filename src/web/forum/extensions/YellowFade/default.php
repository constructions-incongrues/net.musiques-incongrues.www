<?php
/*
Extension Name: Yellow Fade
Extension Url: http://vanillaforums.org/addon/136/yellow-fade
Description: Adds the YellowFade effect on some stuff.
Version: 0.2
Author: Michael Raichelson
Author Url: http://michaelraichelson.com/
 *
 * Copyright 2005 Michael Raichelson <http://michaelraichelson.com/hacks/vanilla/>
 * Copyright 2006, 2007 Mark O'Sullivan <mark@vanillaforums.com>
 * Copyright 2010 Damien Lebrun <dinoboff@gmail.com>
 *
 */

if (!defined('IN_VANILLA')) {
	exit();
}

if (in_array(
		$Context->SelfUrl,
		array(
			"index.php",
			"categories.php",
			"comments.php",
			"search.php",
			"post.php",
			"account.php",
			"settings.php"
			)
		)
){
	if (version_compare(APPLICATION_VERSION, '1.2') < 0) {
		$Head->AddScript('extensions/YellowFade/functions.js', '~');
	} else {
		$Head->AddScript('js/jquery-ui/jquery.effects.core.js', '~', 251);
		$Head->AddScript('js/jquery-ui/jquery.effects.highlight.js', '~', 252);
		$Head->AddScript('extensions/YellowFade/jquery.yellowfade.js', '~', 260);
	}
}
