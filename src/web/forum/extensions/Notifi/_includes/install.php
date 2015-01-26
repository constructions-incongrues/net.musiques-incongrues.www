<?php

// If the installed version is lower than 2.0 or new run this whole thing once
if (empty($Configuration['NOTIFI_INSTALL_V3_COMPLETE'])) {
	$Errors = 0;

	// Create the Notifi table
	$NotifiCreate = "
		CREATE TABLE IF NOT EXISTS `" . $Context->Configuration['DATABASE_TABLE_PREFIX'] . "Notifi` (
		`NotifiID` int(11) NOT NULL auto_increment,
		`UserID` int(11) NOT NULL,
		`Method` varchar(10) NOT NULL,
		`SelectID` int(11) NOT NULL,
		PRIMARY KEY  (`NotifiID`));
	";
	if (!mysql_query($NotifiCreate, $Context->Database->Connection)) {
		$Errors = 1;
	}

	// Create the User column SubscribeOwn
	$result = mysql_query("SHOW columns FROM " . $Context->Configuration['DATABASE_TABLE_PREFIX'] . "User like 'SubscribeOwn'");
	if (mysql_num_rows($result) == 0) {
		$NotifiCreate = "
			ALTER TABLE `" . $Context->Configuration['DATABASE_TABLE_PREFIX'] . "User`
			ADD `SubscribeOwn` TINYINT( 1 ) DEFAULT 1;
		";
		if (!mysql_query($NotifiCreate, $Context->Database->Connection)) {
			$Errors = 1;
		}
	}

	// Create the User column SubscribeComment
	$result = mysql_query("SHOW columns FROM " . $Context->Configuration['DATABASE_TABLE_PREFIX'] . "User like 'SubscribeComment'");
	if (mysql_num_rows($result) == 0) {
		$NotifiCreate = "
			ALTER TABLE `" . $Context->Configuration['DATABASE_TABLE_PREFIX'] . "User`
			ADD `SubscribeComment` TINYINT( 1 ) DEFAULT 1;
		";
		if (!mysql_query($NotifiCreate, $Context->Database->Connection)) {
			$Errors = 1;
		}
	}

	// Create the User column Notified
	$result = mysql_query("SHOW columns FROM " . $Context->Configuration['DATABASE_TABLE_PREFIX'] . "User like 'Notified'");
	if (mysql_num_rows($result) == 0) {
		$NotifiCreate = "
			ALTER TABLE `" . $Context->Configuration['DATABASE_TABLE_PREFIX'] . "User`
			ADD `Notified` TINYINT( 1 ) DEFAULT 0;
		";
		if (!mysql_query($NotifiCreate, $Context->Database->Connection)) {
			$Errors = 1;
		}
	}

	// Create the User column SubscribedEntireForum
	$result = mysql_query("SHOW columns FROM " . $Context->Configuration['DATABASE_TABLE_PREFIX'] . "User like 'SubscribedEntireForum'");
	if (mysql_num_rows($result) == 0) {
		$NotifiCreate = "
			ALTER TABLE `" . $Context->Configuration['DATABASE_TABLE_PREFIX'] . "User`
			ADD `SubscribedEntireForum` TINYINT( 1 ) DEFAULT 0;
		";
		if (!mysql_query($NotifiCreate, $Context->Database->Connection)) {
			$Errors = 1;
		}
	}

	// Create the User column KeepEmailing
	$result = mysql_query("SHOW columns FROM " . $Context->Configuration['DATABASE_TABLE_PREFIX'] . "User like 'KeepEmailing'");
	if (mysql_num_rows($result) == 0) {
		$NotifiCreate = "
			ALTER TABLE `" . $Context->Configuration['DATABASE_TABLE_PREFIX'] . "User`
			ADD `KeepEmailing` TINYINT( 1 ) DEFAULT 0;
		";
		if (!mysql_query($NotifiCreate, $Context->Database->Connection)) {
			$Errors = 1;
		}
	}

	// Create the Category column Subscribeable
	$result = mysql_query("SHOW columns FROM " . $Context->Configuration['DATABASE_TABLE_PREFIX'] . "Category like 'Subscribeable'");
	if (mysql_num_rows($result) == 0) {
		$NotifiCreate = "
			ALTER TABLE `" . $Context->Configuration['DATABASE_TABLE_PREFIX'] . "Category`
			ADD `Subscribeable` INT( 1 ) DEFAULT 0;
		";
		if (!mysql_query($NotifiCreate, $Context->Database->Connection)) {
			$Errors = 1;
		}
	}

	// Create admin configuration settings which can then be controlled in the Extension Options -> Notification page under the Settings tab
	if (empty($Context->Configuration['NOTIFI_ALLOW_ALL'])) {
		AddConfigurationSetting($Context, 'NOTIFI_ALLOW_ALL', '1');
		AddConfigurationSetting($Context, 'NOTIFI_ALLOW_DISCUSSION', '1');
		AddConfigurationSetting($Context, 'NOTIFI_ALLOW_CATEGORY', '1');
	}
	if (empty($Context->Configuration['NOTIFI_AUTO_ALL'])) {
		AddConfigurationSetting($Context, 'NOTIFI_AUTO_ALL', '0');
	}
	if (empty($Context->Configuration['NOTIFI_ALLOW_BBCODE'])) {
		AddConfigurationSetting($Context, 'NOTIFI_ALLOW_BBCODE', '0');
		AddConfigurationSetting($Context, 'NOTIFI_FORMAT_PLAINTEXT', '0');
	}
	if (!$Errors) {
		AddConfigurationSetting($Context, 'NOTIFI_INSTALL_V3_COMPLETE', '1');
	}
}
?>