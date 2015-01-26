<?php
	/*
	Extension Name: Whisperfi
	Extension Url: http://vanillaforums.org/addon/435/whisperfi
	Description: An extension that displays a notification at the top of the Discussions page when a user has received a whisper.
	Version: 1.1.1
	Author: Klaus Burton
	Author Url: http://www.redskiesdesign.com/
	*/

	// Include the file that handles installations and upgrades. Separated just for the sake of organisation.
	if ($Context->Session->User->UserID > 0) {
		include('_includes/install.php');
	}

	CreateArrayEntry($Context->Dictionary, 'YouHaveBeenWhisperedIn',  'You have been whispered in the following discussions:');

	// If user is currently viewing a whisper, mark it read
	if ($Context->Configuration['ENABLE_WHISPERS']) {
		if (in_array($Context->SelfUrl, array('comments.php'))) {
			if ($Context->Session->User->UserID > 0 && (whisperfiCheck($Context,'WhisperNotification') == 1 || $Context->Configuration['WHISPERFI_AUTO_ALL'])) {
				// Mark whispers in this discussion as read
				$DiscussionID = ForceIncomingInt("DiscussionID", "0");
				mysql_query("
					UPDATE ".$Context->Configuration['DATABASE_TABLE_PREFIX']."Comment AS Comment
					LEFT JOIN ".$Context->Configuration['DATABASE_TABLE_PREFIX']."Discussion as Discussion
					ON Comment.DiscussionID = Discussion.DiscussionID
					SET Comment.WhisperRead = 1
					WHERE Comment.WhisperRead = '0' AND Discussion.DiscussionID = '".$DiscussionID."' AND (Discussion.WhisperUserID = '".$Context->Session->UserID."') OR (Comment.WhisperUserID = '".$Context->Session->UserID."')
				",$Context->Database->Connection);
			}
		}
	}

	if ($Context->Configuration['ENABLE_WHISPERS']) {
		if (in_array($Context->SelfUrl, array('comments.php', 'index.php', 'categories.php'))) {
			if ($Context->Session->User->UserID > 0 && (whisperfiCheck($Context,'WhisperNotification') == 1 || $Context->Configuration['WHISPERFI_AUTO_ALL'])) {
				$showNotice = "no";
				global $showNotice;
				$msg = "";
				global $msg;

				$result = "SELECT Discussion.DiscussionID AS DiscussionID,
						Discussion.TotalWhisperCount AS WhisperCount,
						Discussion.Name AS Name,
						Comment.WhisperUserID AS WhisperUserID,
						Comment.WhisperRead AS WhisperRead
					FROM ".$Context->Configuration['DATABASE_TABLE_PREFIX']."Comment AS Comment LEFT JOIN ".$Context->Configuration['DATABASE_TABLE_PREFIX']."Discussion AS Discussion ON Comment.DiscussionID = Discussion.DiscussionID
					WHERE 
						(Comment.WhisperRead = 0 AND Comment.WhisperUserID = '".$Context->Session->User->UserID."') OR
						(Comment.WhisperRead = 0 AND Discussion.WhisperUserID = '".$Context->Session->User->UserID."' AND Discussion.DateLastWhisper >= Comment.DateCreated)";
				$checkresults = mysql_query($result);
				if ($checkresults) {
					while ($row = mysql_fetch_row($checkresults)) {
						// Get the last viewed comment in this discussion
						$result = mysql_query("SELECT CountComments FROM ".$Context->Configuration['DATABASE_TABLE_PREFIX']."UserDiscussionWatch WHERE UserID = '".$Context->Session->User->UserID."' AND DiscussionID = '".$row[0]."'",$Context->Database->Connection);
						$row2 = mysql_fetch_row($result);
						$lastViewedComment = $row2[0];

						// Work out which page and comment to take the user to
						$commentsPerPage = $Context->Configuration['COMMENTS_PER_PAGE'];
						if (empty($lastViewedComment)) {
							$lastViewedComment = '1';
						}
						$pageNumber = ceil($lastViewedComment/$commentsPerPage);
						$jumpToComment = $lastViewedComment-($pageNumber.'0'-10);

						$msg .= '<br /><a href="'.$Context->Configuration['BASE_URL'].'comments.php?DiscussionID='.$row[0].'&page='.$pageNumber.'#Item_'.$jumpToComment.'">'.$row[2].'</a>';
						$showNotice = "yes";
					}
				}
				if ($showNotice == "yes") {
					$NoticeCollector->AddNotice($Context->GetDefinition('YouHaveBeenWhisperedIn').$msg);
				}
			}
		}
	}

	function whisperfiCheck($Context,$Target) {
		$result = mysql_query("SELECT $Target FROM ".$Context->Configuration['DATABASE_TABLE_PREFIX']."User WHERE UserID = '".$Context->Session->UserID."'",$Context->Database->Connection);
		$row = mysql_fetch_row($result);
		return $row[0];
	}

	function whisperfiSwitch($Context,$Switch,$UserID,$Target) {
		mysql_query("UPDATE `".$Context->Configuration['DATABASE_TABLE_PREFIX']."User` SET $Target = $Switch WHERE UserID = $UserID",$Context->Database->Connection);
	}
?>