<?php
/*
Extension Name: IP History
Extension Url: http://vanillaforums.org/addon/10/ip-history
Description: Adds a complete summary of all IP addresses a user has ever had (and who has shared those IP addresses) to their profile. Note: you must have IP logging turned on in order to record and display this data.
Version: 1.0.2
Author: Mark O'Sullivan
Author Url: http://www.markosullivan.ca/

Copyright 2003 - 2005 Mark O'Sullivan
This file is part of Vanilla.
Vanilla is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
Vanilla is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
The latest source code is available at www.vanilla1forums.com
Contact Mark O'Sullivan at mark [at] lussumo [dot] com

Installation Notes:
In order to have IP Addresses recorded, you will need to set the LOG_ALL_IPS
configuration option to 1 in your conf/settings.php file, like so:

$Configuration['LOG_ALL_IPS'] = '1';
*/
if (!defined('IN_VANILLA')) exit();

$Context->SetDefinition("CommentPostedFromX", "Comment posted from //1");
$Context->SetDefinition("IpHistory", "IP history");
$Context->SetDefinition("XTime", "//1 time");
$Context->SetDefinition("XTimes", "//1 times");
$Context->SetDefinition("IpAlsoUsedBy", "This IP address has also been used by the following users:");
$Context->SetDefinition("IpNotShared", "This IP address has not been shared by any other users.");
$Context->SetDefinition("NoIps", "This user does not appear to have logged any IP addresses.");

if ($Context->SelfUrl == "account.php") {

	// Displays a user's IP history (for admins only)
	class IpHistory extends Control {
		var $History;		// The user's IP history data

		function IpHistory(&$Context, &$UserManager, $UserID) {
			$this->Name = "IpHistory";
			$this->PostBackAction = ForceIncomingString("PostBackAction", "");
			$this->Control($Context);
			$this->History = false;
			if ($this->Context->Session->User) {
				if ($this->Context->Session->User->Permission("PERMISSION_IP_ADDRESSES_VISIBLE") && $this->PostBackAction == "") $this->History = $UserManager->GetIpHistory($UserID);
			}
		}

		function Render() {
			if ($this->History && $this->PostBackAction == "") {
				$DefaultThemeDir = dirname(__FILE__) . '/';
				if (version_compare(APPLICATION_VERSION, '1.2', '<')) {
					$ThemeFile = $DefaultThemeDir . 'account_ip_history.php';
				} else {
					$ThemeFile = ThemeFilePath(
							$this->Context->Configuration,
							'account_ip_history.php',
							$DefaultThemeDir);
				}
				include($ThemeFile);
			}
		}
	}

	// Don't reload objects if you don't need to (ie. If another extension has already loaded it)
	if (!@$UserManager) $UserManager = $Context->ObjectFactory->NewContextObject($Context, "UserManager");
	$AccountUserID = ForceIncomingInt("u", $Context->Session->UserID);
	if (!@$AccountUser) $AccountUser = $UserManager->GetUserById($AccountUserID);
	$Page->AddRenderControl($Context->ObjectFactory->NewContextObject($Context, "IpHistory", $UserManager, $AccountUserID), $Configuration["CONTROL_POSITION_BODY_ITEM"]);
}

// Displays the user's ip address next to his/her comment if the viewing user is an administrator
if ($Context->SelfUrl == "comments.php") {
	function CommentGrid_ShowIPAddress(&$CommentGrid) {
		if ($CommentGrid->Context->Session->UserID > 0) {
			if ($CommentGrid->Context->Session->User->Permission("PERMISSION_IP_ADDRESSES_VISIBLE")) {
				$Comment = $CommentGrid->DelegateParameters["Comment"];
				$CommentList = &$CommentGrid->DelegateParameters["CommentList"];
				$CommentList .= str_replace("//1", $Comment->RemoteIp, $CommentGrid->Context->GetDefinition("CommentPostedFromX"));
			}
		}
	}

	$Context->AddToDelegate("CommentGrid",
			"PreCommentOptionsRender",
			"CommentGrid_ShowIPAddress");
}
