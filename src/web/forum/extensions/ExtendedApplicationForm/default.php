<?php
/*
Extension Name: Extended Application Form
Extension Url: http://vanillaforums.org/addon/69/extended-application-form
Description: Extends the application form so that the user's first and last name are required. It also checks that the user actually clicked to read the Terms of Service.
Version: 1.0.1
Author: Mark O'Sullivan
Author Url: http://markosullivan.ca/

Copyright 2003 - 2005 Mark O'Sullivan
This file is part of Lussumo's Software Library.
Lussumo's Software Library is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
Lussumo's Software Library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
The latest source code is available at www.vanilla1forums.com
Contact Mark O'Sullivan at mark [at] lussumo [dot] com
*/

if (!defined('IN_VANILLA')) exit();


$Context->SetDefinition("FirstName", "First name");
$Context->SetDefinition("LastName", "Last name");
$Context->SetDefinition("FirstNameLower", "first name");
$Context->SetDefinition("LastNameLower", "last name");
$Context->SetDefinition("ErrReadTOS", "You must READ the terms of service.");


if (!$Context->SelfUrl == 'people.php' 
	|| !in_array(
		ForceIncomingString('PostBackAction', ''),
		array('ApplyForm', 'Apply'))
) {
	return;
}


// Add the Real name inputs to the application form
function ApplicationForm_AddRealNameInputs($ApplyForm) {
	$DefaultThemeDir = dirname(__FILE__) . '/theme/';
	if (version_compare(APPLICATION_VERSION, '1.2', '<')) {
		$ThemeFile = $DefaultThemeDir . 'ExtendedApplicationForm_aply_form.php';
	} else {
		$ThemeFile = ThemeFilePath(
				$ApplyForm->Context->Configuration,
				'ExtendedApplicationForm_aply_form.php',
				$DefaultThemeDir);
	}
	include($ThemeFile);
}

$Context->AddToDelegate('ApplyForm',
		'PreInputsRender',
		'ApplicationForm_AddRealNameInputs');


// Add the requirements to the membership application processing
function ApplicationForm_AddRequirements(&$ApplyForm) {
	$SafeUser = $ApplyForm->Applicant;
	$SafeUser->FormatPropertiesForDatabaseInput();
	Validate($ApplyForm->Context->GetDefinition('FirstNameLower'), 1, $SafeUser->FirstName, 50, "", $ApplyForm->Context);
	Validate($ApplyForm->Context->GetDefinition('LastNameLower'), 1, $SafeUser->LastName, 50, "", $ApplyForm->Context);
	// Make sure that they actually read the terms of service
	if (!$SafeUser->ReadTerms) $ApplyForm->Context->WarningCollector->Add($ApplyForm->Context->GetDefinition("ErrReadTOS"));
}

$Context->AddToDelegate('ApplyForm',
		'PreCreateUser',
		'ApplicationForm_AddRequirements');


// Remove the existing javascript for this page and add my own
$Head->ClearScripts();
$Head->AddString('
	<script language="javascript">
		function PopTermsOfService(Url) {
			var frm = document.getElementById("ApplicationForm");
			if (frm) frm.ReadTerms.value = "1";
			window.open(Url, "TermsOfService", "toolbar=no,status=yes,location=no,menubar=no,resizable=yes,height=600,width=400,scrollbars=yes");
		}
	</script>
	', 0, 1);

// Add the requirement to the identity form on the account page as well
function IdentityForm_AddRequirements(&$IdentityForm) {
	$SafeUser = $IdentityForm->User;
	$SafeUser->FormatPropertiesForDatabaseInput();
	Validate($IdentityForm->Context->GetDefinition('FirstNameLower'), 1, $SafeUser->FirstName, 50, '', $IdentityForm->Context);
	Validate($IdentityForm->Context->GetDefinition('LastNameLower'), 1, $SafeUser->LastName, 50, '', $IdentityForm->Context);
}

$Context->AddToDelegate('IdentityForm',
		'PreSaveIdentity',
		'IdentityForm_AddRequirements');

