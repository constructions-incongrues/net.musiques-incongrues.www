<?php
/*
Extension Name: Hide Success
Extension Url: http://vanillaforums.org/addon/44/hide-success
Description: Makes "Your changes were saved" messages disappear with a shrink effect after a moment.
Version: 1.1
Author: Mark O'Sullivan
Author Url: http://www.markosullivan.ca/
 *
 * Copyright 2006 Mark O'Sullivan <http://www.markosullivan.ca/>
 * Copyright 2010 Damien Lebrun <dinoboff@gmail.com>
 * 
 */


if (!in_array($Context->SelfUrl, array('settings.php', 'account.php'))) {
	return;
}

if (version_compare(APPLICATION_VERSION, '1.2', '<')) {
	$Head->AddScript('extensions/HideSuccess/functions.js');
	$Head->AddString("
      <script type=\"text/javascript\">
         // Initialize hide effects
         var EffectTimer;
         var Height = -1;
         setTimeout(\"ExecuteEffect('Success', 'HideEffect', 9);\", 2000);
      </script>
      ");
} else {
	$Head->AddScript('extensions/HideSuccess/jquery.hidesuccess.js', '~', 310);
}
