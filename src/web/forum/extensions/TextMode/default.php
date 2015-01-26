<?php
/*
Extension Name: Text Mode Switch
Extension Url: http://vanillaforums.org/addon/76/text-mode-switch
Description: Allows users to turn html-enabled comments off so that they appear as they were entered.
Version: 2.0.1
Author: Mark O'Sullivan
Author Url: http://www.markosullivan.ca/
 *
 * Copyright 2006 Mark O'Sullivan <http://www.markosullivan.ca/>
 * Copyright 2010 Damien Lebrun <dinoboff@gmail.com>
 */

if (!defined('IN_VANILLA')) {
	exit();
}

if (!isset($Head) || !isset($Context)) {
	return;
}

if (version_compare(PEOPLE_VERSION, '1.1.4') < 0) {
	return;
}


$Context->SetDefinition('TextOnlyModeIsOn', 'Text-only mode is ON');
$Context->SetDefinition('TextOnlyModeIsOff', 'Text-only mode is OFF');
$Context->SetDefinition('TurnOff', 'turn off');
$Context->SetDefinition('TurnOn', 'turn on');
$Context->SetDefinition(
		'DisplayTextOnlyToggle',
		'Display text-only mode toggle on all pages');

// Write the text mode switches
if ($Context->Session->UserID > 0
	&& $Context->Session->User->Preference("ShowTextToggle")
	&& in_array(
		$Context->SelfUrl,
		array(
			"post.php",
			"index.php",
			"categories.php",
			"comments.php",
			"account.php",
			"search.php"
			)
		)
) {

	$Head->AddScript('extensions/TextMode/functions.js', '~', 290);
	$Head->AddStyleSheet('extensions/TextMode/style.css', 'screen');


	if ($Context->Session->User->Preference("HtmlOn")) {
		$CurrentMode = $Context->GetDefinition("TextOnlyModeIsOff");
		$CurrentModeCSS = "OFF";
		$OppositeMode = $Context->GetDefinition("TurnOn");
		$Switch = 0;
	} else {
		$CurrentMode = $Context->GetDefinition("TextOnlyModeIsOn");
		$CurrentModeCSS = "ON";
		$OppositeMode = $Context->GetDefinition("TurnOff");
		$Switch = 1;
	}
	
	$Panel->AddString(
		'<p id="Mode" class="TextMode' . $CurrentModeCSS . '">'
			. $CurrentMode
			. ' (<a href="./" onclick="SwitchTextMode(\''
			. $Configuration['WEB_ROOT'] . "ajax/switch.php', '"
			. $Switch. "', '"
			. $Context->Session->GetCsrfValidationKey()
			. '\'); return false;">' . $OppositeMode
			. "</a>)</p>",
		200);
}

// Make sure that a comment follows the user's preference
// and enables or disabled html where required
function Comment_UserHtmlPreference(&$Comment) {
	if ($Comment->Context->Session->UserID > 0
		&& !$Comment->Context->Session->User->Preference("HtmlOn")
	) {
		$Comment->ShowHtml = 0;
		$Comment->AuthIcon = "";
	}
}

$Context->AddToDelegate("Comment",
		"PreFormatPropertiesForDisplay",
		"Comment_UserHtmlPreference");


if ($Context->SelfUrl == "account.php") {
	
	// Make sure that the pictures aren't displayed on the page
	// if the user has turned text-only mode on
	function Account_HideImages(&$Account) {
		if (!$Account->Context->Session->User->Preference("HtmlOn")) {
			$Account->User->DisplayIcon = "";
			$Account->User->Picture = "";
		}
	}

	$Context->AddToDelegate("Account",
			"PreUsernameRender",
			"Account_HideImages");


	// Add the option to the accounts pag
	if (ForceIncomingString("PostBackAction", "") == "Functionality") {

		function PreferencesForm_AddTextModeSwitch(&$PreferencesForm) {
			$PreferencesForm->AddPreference(
					"ControlPanel",
					"DisplayTextOnlyToggle",
					"ShowTextToggle",
					1);
		}

		$Context->AddToDelegate("PreferencesForm",
				"Constructor",
				"PreferencesForm_AddTextModeSwitch");
	}
}
