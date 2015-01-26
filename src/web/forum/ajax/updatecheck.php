<?php
/*
* Copyright 2003 Mark O'Sullivan
* This file is part of Vanilla.
* Vanilla is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Vanilla is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code is available at www.vanilla1forums.com
* Contact Mark O'Sullivan at mark [at] lussumo [dot] com
*
* Description: File used by the Extension management form to handle turning extensions on and off
*/

include('../appg/settings.php');
include('../appg/init_ajax.php');

// Process the ajax request
$PostBackKey = ForceIncomingString('PostBackKey', '');
$ExtensionKey = ForceIncomingString('ExtensionKey', '');
$RequestName = ForceIncomingString('RequestName', '');
$SafeRequestName = htmlentities($RequestName);

if ($PostBackKey != '' && $PostBackKey != $Context->Session->GetCsrfValidationKey()) {
	echo $SafeRequestName.'|[ERROR]'.$Context->GetDefinition('ErrPostBackKeyInvalid');
} else if ($RequestName == 'Core') {
	// Ping the Lussumo server with core version information
	$CurrentVersion = OpenUrl($Context->Configuration['UPDATE_URL'].'?name=Vanilla', $Context);

	// Also record that the check occurred
	$SettingsFile = $Context->Configuration['APPLICATION_PATH'].'conf/settings.php';
	$ConfigurationManager = $Context->ObjectFactory->NewContextObject($Context, "ConfigurationManager");
	$ConfigurationManager->DefineSetting('LAST_UPDATE', mktime(), 1);
	$ConfigurationManager->SaveSettingsToFile($SettingsFile);

	// Spit out the core message
	if ($CurrentVersion <= APPLICATION_VERSION) {
		echo 'First|'.$Context->GetDefinition('ApplicationStatusGood');
	} else if ($CurrentVersion > APPLICATION_VERSION) {
		echo 'First|[OLD]'.str_replace(array('\\1','\\2'), array($CurrentVersion, $Context->Configuration['UPDATE_URL'].'../'), $Context->GetDefinition('NewVersionAvailable'));
	}
} else {
	// Load all extensions for version information
	$Extensions = DefineExtensions($Context, true);
	if (!is_array($Extensions)) {
		echo $SafeRequestName.'|[ERROR]'.$Context->WarningCollector->GetPlainMessages();
	} elseif (count($Extensions) > 0) {
		// All of the extensions were loaded successfully.
		// Ping the Lussumo server with the next extension
		$CheckExtension = '';
		while (list($ExtensionKey, $Extension) = each($Extensions)) {
			if ($RequestName == 'First') {
				$CheckExtension = $ExtensionKey;
				$RequestName = '';
				break;
			} else if ($RequestName == $ExtensionKey) {
				$RequestName = '[NEXT]';
			} else if ($RequestName == '[NEXT]') {
				$CheckExtension = $ExtensionKey;
				$RequestName = '';
				break;
			}
		}

		// Ping the CheckExtension value if it isn't empty
		if ($CheckExtension != '') {
			$Extension = $Extensions[$CheckExtension];

			// Ping the Lussumo server with extension version information
			$CurrentVersion = OpenUrl($Context->Configuration['UPDATE_URL'].'?name='.unhtmlspecialchars($Extension->Name), $Context);
			if ($CurrentVersion == "UNKNOWN") {
				echo $CheckExtension.'|[UNKNOWN]'.$Context->GetDefinition('ExtensionStatusUnknown');
			} else if ($CurrentVersion <= $Extension->Version) {
				echo $CheckExtension.'|[GOOD]'.$Context->GetDefinition('ExtensionStatusGood');
			} elseif ($CurrentVersion >= $Extension->Version) {
				$ExtensionName = $Extension->Name;
				$ExtensionURL = str_replace(' ', '', $ExtensionName);
				$ExtensionURL = $Context->Configuration['UPDATE_URL'].'../extensions/'.$ExtensionURL.'/';
				echo $CheckExtension.'|[OLD]'.str_replace(array('\\1','\\2'), array($CurrentVersion, $ExtensionURL), $Context->GetDefinition('NewVersionAvailable'));
			}
		} else {
			if ($RequestName == '[NEXT]') {
				echo 'COMPLETE';
			} else {
				echo $RequestName.'Failed to get extension name from ajax call.';
			}
		}
	} else {
		echo 'COMPLETE';
	}
}
$Context->Unload();
?>