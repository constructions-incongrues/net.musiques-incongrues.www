<?php
	// If the installed version is lower than 1.1 or new run this whole thing once
	if (empty($Configuration['WHISPERFI_INSTALL_COMPLETE_11'])) {
		$Errors = 0;

		// Create the User column WhisperNotification
		$result = mysql_query("SHOW columns FROM ".$Context->Configuration['DATABASE_TABLE_PREFIX']."User like 'WhisperNotification'");
		if (mysql_num_rows($result) == 0) {
			$WhisperfiCreate = "ALTER TABLE `".$Context->Configuration['DATABASE_TABLE_PREFIX']."User`
					 ADD `WhisperNotification` TINYINT( 1 ) DEFAULT 1;";
			if (!mysql_query($WhisperfiCreate, $Context->Database->Connection)) {
				$Errors = 1;
			}
		}

		// Create the Comment column WhisperRead
		$result = mysql_query("SHOW columns FROM ".$Context->Configuration['DATABASE_TABLE_PREFIX']."Comment like 'WhisperRead'");
		if (mysql_num_rows($result) == 0) {
			$WhisperReadCreate = "ALTER TABLE `".$Context->Configuration['DATABASE_TABLE_PREFIX']."Comment`
					 ADD `WhisperRead` TINYINT( 1 ) DEFAULT 0;";
			if (!mysql_query($WhisperReadCreate, $Context->Database->Connection)) {
				$Errors = 1;
			}

			// If it worked up to this point, set all current whispers as read
			if ($Errors == 0) {
				$WhisperReadPopulate = "UPDATE ".$Context->Configuration['DATABASE_TABLE_PREFIX']."Comment SET WhisperRead = 1 WHERE WhisperUserID != 0";
				if (!mysql_query($WhisperReadPopulate, $Context->Database->Connection)) {
					$Errors = 1;
				}
			}
		}
	
		// Create admin configuration settings which can then be controlled in the Extension Options -> Notification page under the Settings tab
		if (empty($Context->Configuration['WHISPERFI_AUTO_ALL'])) {
			AddConfigurationSetting($Context, 'WHISPERFI_AUTO_ALL', '0');
		}
		if (!$Errors) {
			AddConfigurationSetting($Context, 'WHISPERFI_INSTALL_COMPLETE', '1');
		}
	}
?>