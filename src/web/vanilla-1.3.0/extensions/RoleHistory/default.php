<?php
/*
Extension Name: Role History
Extension Url: http://vanillaforums.org/addon/72/role-history
Description: Adds a complete summary of all role changes to each user's account profile.
Version: 1.0
Author: Mark O'Sullivan
Author Url: http://www.markosullivan.ca/

 * Copyright 2003, 2004, 2005, 2006 Mark O'Sullivan
 * Copyright 2010 Damien Lebrun
 *
 * @license GPLv2 http://lussumo-vanilla.googlecode.com/svn/trunk/src/gpl.txt
 */

if (!defined('IN_VANILLA')) {
	exit();
}


$Context->SetDefinition("RoleHistory", "Role history");
$Context->SetDefinition(
		"NoRoleHistory",
		"This user does not appear to have been assigned to any roles.");
$Context->SetDefinition(
		"RoleAssignedByX",
		"Role assigned by //1 with the following notes:");


if ($Context->SelfUrl == "account.php") {

	class RoleHistory extends Control {
		var $History;	// The history data for the specified user

		function RoleHistory(&$Context, &$UserManager, $UserID) {
			$this->PostBackAction = ForceIncomingString("PostBackAction", "");
			$this->Name = "RoleHistory";
			$this->Control($Context);
			if ($this->PostBackAction == "") {
				$this->History = $UserManager->GetUserRoleHistoryByUserId($UserID);
			}
		}

		function Render() {
			$this->CallDelegate("PreRender");
			if ($this->Context->WarningCollector->Count() == 0
				&& $this->PostBackAction == ""
				&& $this->Context->Database->RowCount($this->History) > 0
			) {
				$ThemeDir = dirname(__FILE__) . '/theme/';
				if (version_compare(APPLICATION_VERSION, '1.2', '<')) {
					$ThemePath = $ThemeDir . 'RoleHistory_account_role_history.php';
				} else {
					$ThemePath = ThemeFilePath(
						$this->Context->Configuration,
						'RoleHistory_account_role_history.php',
						$ThemeDir);
				}
				include($ThemePath);
			}
			$this->CallDelegate("PostRender");
		}
	}

	// Don't reload objects if you don't need to
	// (ie. If another extension has already loaded it)
	if (!@$UserManager) {
		$UserManager =
			$Context->ObjectFactory->NewContextObject($Context, "UserManager");
	}

	$AccountUserID = ForceIncomingInt("u", $Context->Session->UserID);
	if (!@$AccountUser) {
		$AccountUser = $UserManager->GetUserById($AccountUserID);
	}
	
	$Page->AddRenderControl(
			$Context->ObjectFactory->NewContextObject(
					$Context,
					"RoleHistory",
					$UserManager,
					$AccountUserID),
			$Configuration["CONTROL_POSITION_BODY_ITEM"]);
}
