<?php
/*
Extension Name: Guest Welcome Message
Extension Url: http://vanillaforums.org/addon/9/guest-welcome-message
Description: Adds a welcome message to the panel if the person viewing the forum doesn't have an active session.
Version: 4.0
Author: Mark O'Sullivan
Author Url: http://markosullivan.ca/
 *
 *
 * Copyright 2006 Mark O'Sullivan <http://markosullivan.ca/>
 * Copyright 2010 Damien Lebrun <dinoboff@gmail.com>
 *
 * Changes:
 *
 *   4.0:
 *
 *     - GuestWelcome definition is deprecated. It uses GuestWelcomeMessage
 *       definition instead. This definition doesn't need to include the
 *       sign-in and registration URLs.
 *
 */



$Context->SetDefinition(
		"GuestWelcomeMessage",
		'<strong>Welcome Guest!</strong><br />'
			. 'Want to take part in these discussions? If you have an account, '
			. '<a href="%s">sign in now</a>. <br />'
			. 'If you don\'t have an account, '
			. '<a href="%s">apply for one now</a>.');


$GuestWelcomeMessagePage = array(
		"account.php",
		"categories.php",
		"comments.php",
		"index.php",
		"search.php");

if (in_array($Context->SelfUrl, $GuestWelcomeMessagePage)
		&& $Context->Session->UserID == 0
) {
	$SignInUrl = empty($Configuration['SIGNIN_URL']) ?
		GetUrl($Configuration, "people.php")
		: ConcatenatePath(
			$Configuration['BASE_URL'],
			$Configuration['SIGNIN_URL']);
	$RegisterUrl = empty($Configuration['REGISTRATION_URL']) ?
		GetUrl(
			$Configuration,
			"people.php", "", "", "", "",
			"PostBackAction=ApplyForm")
		: ConcatenatePath(
			$Configuration['BASE_URL'],
			$Configuration['REGISTRATION_URL']);
	
	$NoticeCollector->AddNotice(
		sprintf(
			$Context->GetDefinition('GuestWelcomeMessage'),
			$SignInUrl,
			$RegisterUrl));

	unset($SignInUrl, $RegisterUrl);
}

unset($GuestWelcomeMessagePage);
