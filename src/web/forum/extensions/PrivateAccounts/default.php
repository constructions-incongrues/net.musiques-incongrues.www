<?php
/*
Extension Name: Private Accounts
Extension Url: http://vanillaforums.org/addon/28/private-accounts
Description: Blocks unauthenticated users from browsing user accounts on a public forum.
Version: 1.0.1
Author: Mark O'Sullivan
Author Url: http://www.markosullivan.ca/
 *
 * Copyright 2006 Mark O'Sullivan <http://www.markosullivan.ca/>
 * Copyright 2010 Damien Lebrun <dinoboff@gmail.com>
 * 
 */

if ($Context->SelfUrl == 'account.php' && $Context->Session->UserID == 0) {
	header('location:'.GetUrl($Configuration, 'index.php'));
	die();
}
