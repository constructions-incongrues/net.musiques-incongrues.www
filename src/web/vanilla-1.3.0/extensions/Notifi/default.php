<?php
/*
Extension Name: Notifi
Extension Url: http://vanillaforums.org/addon/409/notifi
Description: Allows users to choose to receive email notifications about new posts in either the whole forum, specific categories or specific discussions.
Version: 2.0.4
Author: Klaus Burton
Author Url: http://www.redskiesdesign.com/
*/


// General definitions
$Context->SetDefinition('EmailNotification',                      'Email Notification');
$Context->SetDefinition('SubscribeTo',                            'Subscribe to');
$Context->SetDefinition('UnsubscribeFrom',                        'Unsubscribe from');
$Context->SetDefinition('Forum',                                  'forum');
$Context->SetDefinition('SubscribeUnsubscribeForumTitle',         'Start/stop receiving an email whenever there is a new post in this forum');
$Context->SetDefinition('CategoryLC',                             'category');
$Context->SetDefinition('SubscribeUnsubscribeCategoryTitle',      'Start/stop receive an email whenever there is a new post in this category');
$Context->SetDefinition('Discussion',                             'discussion');
$Context->SetDefinition('SubscribeUnsubscribeDiscussionTitle',    'Start/stop receiving an email whenever there is a new post in this discussion');
$Context->SetDefinition('NotificationOptions',                    'Notification options');
$Context->SetDefinition('Notifi',                                 'Notifi');
$Context->SetDefinition('RememberToSetNotifiSettingsPermissions', 'Remember to customize the Notifi settings for you and your users. You can do it at the <a href="'.GetUrl($Context->Configuration, 'settings.php', '', '', '', '', 'PostBackAction=Notifi').'">Notification Settings</a> page.');

// Definitions for account page
$Context->SetDefinition('YourNotifications',                      'Your Notifications');
$Context->SetDefinition('NotificationOnOwnExplanation',           'Automatically subscribe me to discussions I start');
$Context->SetDefinition('NotificationOnCommentExplanation',       'Automatically subscribe me to discussions I comment on');
$Context->SetDefinition('KeepEmailingExplanation',                'Email me every time (instead of once between each forum visit)');
$Context->SetDefinition('NotificationForum',                      'Subscribe me to the entire forum (discludes unauthorized categories)');

// Definitions for admin page
$Context->SetDefinition('AdminAllowAll',                          'Allow users to subscribe to the entire forum');
$Context->SetDefinition('AdminAllowCategories',                   'Allow users to subscribe to categories');
$Context->SetDefinition('AdminAllowDiscussions',                  'Allow users to subscribe to discussions');
$Context->SetDefinition('AdminAllowBbcode',                       'Convert BBCode to HTML');
$Context->SetDefinition('AdminFormatPlaintext',                   'Send emails as plaintext instead of HTML');
$Context->SetDefinition('AdminAutoAll',                           'Force notification in all cases');

// Include the file that handles installations and upgrades. Separated just for the sake of organisation.
include('_includes/install.php');

function CheckNotifiSyntax($Context,$Method,$SelectID) {
	switch ($Method) {
		case 'ALL':
			if ($SelectID == 0) {
				return true;
			} else {
				return false;
			}
		case 'CATEGORY':
			if ($SelectID > 0) {
				$result = mysql_query("SELECT CategoryID FROM ".$Context->Configuration['DATABASE_TABLE_PREFIX']."Category WHERE CategoryID = '$SelectID'",$Context->Database->Connection);
				$row = mysql_fetch_row($result);
				if ($row[0] == $SelectID) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		case 'DISCUSSION':
			if ($SelectID > 0) {
				$result = mysql_query("SELECT DiscussionID FROM ".$Context->Configuration['DATABASE_TABLE_PREFIX']."Discussion WHERE DiscussionID = '$SelectID'",$Context->Database->Connection);
				$row = mysql_fetch_row($result);
				if ($row[0] == $SelectID) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		default:
			return false;
	}
}

function ChangeNotifi($Context,$Method,$SelectID,$Value) {
	if ($Context->Configuration['NOTIFI_ALLOW_'.$Method] == 1 AND $Context->Configuration['NOTIFI_AUTO_ALL'] == 0) {
		if ($Value == 1) {
			if (CheckNotifiSyntax($Context,$Method,$SelectID) AND CheckNotifi($Context,$Method,$SelectID) == false) {
				if (mysql_query("INSERT INTO `".$Context->Configuration['DATABASE_TABLE_PREFIX']."Notifi` ( `NotifiID` , `UserID` , `Method` , `SelectID` ) VALUES (NULL , '".$Context->Session->UserID."', '".$Method."', '".$SelectID."')", $Context->Database->Connection)) {
					return true;
				}
			}
		} else {
			if (CheckNotifiSyntax($Context,$Method,$SelectID) AND CheckNotifi($Context,$Method,$SelectID) == true) {
				if (mysql_query("DELETE FROM `".$Context->Configuration['DATABASE_TABLE_PREFIX']."Notifi` WHERE UserID = '".$Context->Session->UserID."' AND Method = '".$Method."' AND SelectID = '".$SelectID."'",$Context->Database->Connection)) {
					return true;
				}
			}
		}
	} else {
		return false;
	}
}

function CheckNotifi($Context,$Method,$SelectID) {
	$result = mysql_query("SELECT NotifiID FROM `".$Context->Configuration['DATABASE_TABLE_PREFIX']."Notifi` WHERE UserID = '".$Context->Session->UserID."' AND Method = '".$Method."' AND SelectID = '".$SelectID."'", $Context->Database->Connection);
	$row = mysql_fetch_row($result);
	if ($row[0] > 0) {
		return true;
	} else {
		return false;
	}
}

function notifiSwitch($Context,$Switch,$UserID,$Target) {
	mysql_query("UPDATE `".$Context->Configuration['DATABASE_TABLE_PREFIX']."User` SET $Target = $Switch WHERE UserID = $UserID",$Context->Database->Connection);
}

function notifiCheck($Context,$Target) {
	$result = mysql_query("SELECT $Target FROM ".$Context->Configuration['DATABASE_TABLE_PREFIX']."User WHERE UserID = '".$Context->Session->UserID."'",$Context->Database->Connection);
	$row = mysql_fetch_row($result);
	return $row[0];
}

// Add "subscribe/unsubscribe" links to the panel
if ($Context->Session->UserID > 0 && isset($Panel) && $Context->Configuration['NOTIFI_AUTO_ALL'] == 0) {
	$Panel->AddList($Context->GetDefinition('EmailNotification'), 100);

	if ($Context->Configuration['NOTIFI_ALLOW_ALL'] == 1 && in_array($Context->SelfUrl, array('comments.php','index.php','categories.php'))) {
		$SubscribeClass = 'notifiSubscribe';
		$UnsubscribeClass = 'notifiUnSubscribe';
		if (CheckNotifi($Context,'ALL',0)) {
			$SubscribeClass .= ' notifiActive';
		} else {
			$UnsubscribeClass .= ' notifiActive';
		}
		$LinkContent = '<span class="'. $SubscribeClass.'">'
			. $Context->GetDefinition('SubscribeTo') .'</span>'
			. '<span class="notifiSep"> / </span>'
			. '<span class="'. $UnsubscribeClass .'">'
			. $Context->GetDefinition('UnsubscribeFrom') .'</span> '
			. $Context->GetDefinition('Forum');
		$Panel->AddListItem(
				$Context->GetDefinition('EmailNotification'), $LinkContent,"./#Notify_ALL","",
				'title="'.$Context->GetDefinition('SubscribeUnsubscribeForumTitle')
					. '" id="SetNotifiAll" class="notifiToggleLink"');
		unset($SubscribeClass, $UnsubscribeClass, $LinkContent);
	}
	$DiscussionID = ForceIncomingInt("DiscussionID", "0");
	if ($DiscussionID > 0) {
		$result = mysql_query("SELECT CategoryID FROM ".$Context->Configuration['DATABASE_TABLE_PREFIX']."Discussion WHERE DiscussionID = '$DiscussionID'",$Context->Database->Connection);
		$row = mysql_fetch_row($result);
		$CategoryID = $row[0];
	} else {
		$CategoryID = ForceIncomingInt('CategoryID',0);
	}
	if ($Context->Configuration['NOTIFI_ALLOW_CATEGORY'] == 1 && in_array($Context->SelfUrl, array('index.php','comments.php')) AND ($CategoryID > 0)) {
		$SubscribeClass = 'notifiSubscribe';
		$UnsubscribeClass = 'notifiUnSubscribe';
		if (CheckNotifi($Context,'CATEGORY',$CategoryID) == true) {
			$SubscribeClass .= ' notifiActive';
		} else {
			$UnsubscribeClass .= ' notifiActive';
		}
		$LinkContent = '<span class="'. $SubscribeClass.'">'
			. $Context->GetDefinition('SubscribeTo') .'</span>'
			. '<span class="notifiSep"> / </span>'
			. '<span class="'. $UnsubscribeClass .'">'
			. $Context->GetDefinition('UnsubscribeFrom') .'</span> '
			. $Context->GetDefinition('CategoryLC');
		$Panel->AddListItem(
				$Context->GetDefinition('EmailNotification'), $LinkContent,
				"./#Notifi_CATEGORY_" . $CategoryID,"",
				'title="'.$Context->GetDefinition('SubscribeUnsubscribeCategoryTitle')
					. '" id="SetNotifiCategory_'.$CategoryID.'" class="notifiToggleLink"');
		unset($SubscribeClass, $UnsubscribeClass, $LinkContent);
	}
	if ($Context->Configuration['NOTIFI_ALLOW_DISCUSSION'] == 1 && in_array($Context->SelfUrl, array('comments.php')) AND $DiscussionID > 0) {
		$SubscribeClass = 'notifiSubscribe';
		$UnsubscribeClass = 'notifiUnSubscribe';
		if (CheckNotifi($Context,'DISCUSSION',$DiscussionID) == true) {
			$SubscribeClass .= ' notifiActive';
		} else {
			$UnsubscribeClass .= ' notifiActive';
		}
		$LinkContent = '<span class="'. $SubscribeClass.'">'
			. $Context->GetDefinition('SubscribeTo') .'</span>'
			. '<span class="notifiSep"> / </span>'
			. '<span class="'. $UnsubscribeClass .'">'
			. $Context->GetDefinition('UnsubscribeFrom') .'</span> '
			. $Context->GetDefinition('Discussion');
		$Panel->AddListItem(
			$Context->GetDefinition('EmailNotification'),$LinkContent,
				"./#Notifi_DISCUSSION_".$DiscussionID,"",
				'title="'. $Context->GetDefinition('SubscribeUnsubscribeDiscussionTitle')
					. '" id=\"SetNotifiDiscussion_'.$DiscussionID.'" class="notifiToggleLink"');
	}
}

function NotifiDiscussion($DiscussionForm) {
	// Only continue if this is a new post, not an edited one
	if ($_POST['CommentID'] == "0") {
		// Default variable values for this function set here
		$SubscribedOnOwn = "no";

		if ($DiscussionForm->Context->Configuration['NOTIFI_FORMAT_PLAINTEXT'] == 0) {
			// Make Vanilla send the email/s as HTML instead of plain text
			$DiscussionForm->Context->Configuration['DEFAULT_EMAIL_MIME_TYPE'] = 'text/html';
		}

		$DiscussionID = @$DiscussionForm->DelegateParameters['ResultDiscussion']->DiscussionID;
		if ($DiscussionID > 0) {
			// Detect if Whispered
			$result = mysql_query("SELECT WhisperUserID FROM ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."Discussion WHERE DiscussionID = '$DiscussionID'");
			$row = mysql_fetch_row($result);
			if ($row[0] > 0) {
				$Whispered = 1;
			} else {
				$Whispered = 0;
			}
			$WhisperUserID = $row[0];
			if (notifiCheck($DiscussionForm->Context,'SubscribeOwn')) {
				ChangeNotifi($DiscussionForm->Context,'DISCUSSION',$DiscussionID,1);
				$SubscribedOnOwn = "yes";
			}
		} else {
			$DiscussionID = @$DiscussionForm->DelegateParameters['ResultComment']->DiscussionID;
			// Detect if Whispered
			$mTitle = @$DiscussionForm->DelegateParameters['ResultComment']->Title;
			$CommentID = @$DiscussionForm->DelegateParameters['ResultComment']->CommentID;
			$result = mysql_query("SELECT WhisperUserID FROM ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."Discussion WHERE DiscussionID = '$DiscussionID'");
			$row = mysql_fetch_row($result);
			if ($row[0] > 0) {
				$Whispered = 1;
			} else {
				$Whispered = 0;
			}
			$WhisperUserID = $row[0];
			if ($Whispered == 0) {
				$result = mysql_query("SELECT WhisperUserID FROM ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."Comment WHERE CommentID = '$CommentID'");
				$row = mysql_fetch_row($result);
				if ($row[0] > 0) {
					$Whispered = 1;
					$WhisperUserID = $row[0];
				} else {
					$Whispered = 0;
				}
			}
		}
		if ($DiscussionID > 0) {
			$Notifieusers = array();
			$SelfUser = $DiscussionForm->Context->Session->UserID;
			if ($DiscussionForm->Context->Configuration['NOTIFI_AUTO_ALL'] == 0) {
				// Add all users who have subscribed to all, aren't already notified except the posting user
				if ($DiscussionForm->Context->Configuration['NOTIFI_ALLOW_ALL'] == 1) {
					$result = mysql_query("SELECT A.UserID,Email,FirstName, LastName, RoleID FROM ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."Notifi AS A, ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."User AS B WHERE A.Method = 'ALL' AND A.UserID <> '$SelfUser' AND A.UserID = B.UserID AND (B.Notified = 0 OR B.KeepEmailing = 1)",$DiscussionForm->Context->Database->Connection);
					while ($row = mysql_fetch_row($result)) {
						if (($Whispered == 1 AND $WhisperUserID == $row[0]) OR ($Whispered == 0)) {
							array_push($Notifieusers,array($row[0],$row[1],$row[2],$row[3],$row[4]));
						}
					}
				}

				// Add all users who have subscribed to this category , aren't already notified except the posting user
				if ($DiscussionForm->Context->Configuration['NOTIFI_ALLOW_CATEGORY'] == 1) {
					$result = mysql_query("SELECT CategoryID FROM ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."Discussion WHERE DiscussionID = '$DiscussionID'",$DiscussionForm->Context->Database->Connection);
					$row = mysql_fetch_row($result);
					$result2 = mysql_query("SELECT A.UserID,Email,FirstName, LastName, RoleID FROM ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."Notifi AS A, ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."User AS B WHERE A.Method = 'CATEGORY' AND A.SelectID = '$row[0]' AND A.UserID <> '$SelfUser'  AND A.UserID = B.UserID AND (B.Notified = 0 OR B.KeepEmailing = 1)",$DiscussionForm->Context->Database->Connection);
					while ($row2 = mysql_fetch_row($result2)) {
						if (($Whispered == 1 AND $WhisperUserID == $row2[0]) OR ($Whispered == 0)) {
							array_push($Notifieusers,array($row2[0],$row2[1],$row2[2],$row2[3],$row2[4]));
						}
					}
				}

				// Add all users who have subscribed to this discussion , aren't already notified except the posting user
				if ($DiscussionForm->Context->Configuration['NOTIFI_ALLOW_DISCUSSION'] == 1) {
					$result2 = mysql_query("SELECT A.UserID,Email,FirstName, LastName, RoleID FROM ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."Notifi AS A, ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."User AS B WHERE A.Method = 'DISCUSSION' AND A.SelectID = '$DiscussionID' AND A.UserID <> '$SelfUser' AND A.UserID = B.UserID AND (B.Notified = 0 OR B.KeepEmailing = 1)",$DiscussionForm->Context->Database->Connection);
					while ($row2 = mysql_fetch_row($result2)) {
						if (($Whispered == 1 AND $WhisperUserID == $row2[0]) OR ($Whispered == 0)) {
							array_push($Notifieusers,array($row2[0],$row2[1],$row2[2],$row2[3],$row2[4]));
						}
					}
				}
			}  else {
				// Add all users
				$result = mysql_query("SELECT UserID,Email,FirstName, LastName, RoleID FROM ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."User WHERE UserID <> '$SelfUser' AND (Notified = 0 OR KeepEmailing = 1)",$DiscussionForm->Context->Database->Connection);
				while ($row = mysql_fetch_row($result)) {
					if (($Whispered == 1 AND $WhisperUserID == $row[0]) OR ($Whispered == 0)) {
						array_push($Notifieusers,array($row[0],$row[1],$row[2],$row[3],$row[4]));
					}
				}
			}

			// Get the username of the person who posted
			$mPosterName = notifiCheck($DiscussionForm->Context,'Name');

			// Remove double inserted users
			array_unique($Notifieusers);

			// Get the category ID
			$results = mysql_query("SELECT CategoryID, Name FROM ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."Discussion WHERE DiscussionID = '$DiscussionID'",$DiscussionForm->Context->Database->Connection);
			$row = mysql_fetch_row($results);
			$categoryID     = $row[0];
			$discussionName = $row[1];

			// Get the comment contents
			$result = mysql_query("SELECT Body FROM ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."Comment WHERE CommentID = '$CommentID'");
			$row = mysql_fetch_row($result);
			if (empty($row[0])) {
				$mComment = strip_tags($_POST['Body']);
			} else {
				$mComment = $row[0];
			}

			$mailsent = array();

			// Create the email object
			$e = $DiscussionForm->Context->ObjectFactory->NewContextObject($DiscussionForm->Context, 'Email');
			$e->HtmlOn = 1;

			// Build an array that contains roles that are blocked from viewing this category
			$results  = mysql_query("SELECT * FROM ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."CategoryRoleBlock WHERE CategoryID = '$categoryID' AND Blocked = '1'",$DiscussionForm->Context->Database->Connection);
			$rolesAry = array();
			while ($row = mysql_fetch_row($results)) {
				$rolesAry[] = $row;
			}

			// If the user has chosen to be subscribed to every discussion they comment on, subscribe them
			if (notifiCheck($DiscussionForm->Context,'SubscribeComment') && $SubscribedOnOwn == "no") {
				ChangeNotifi($DiscussionForm->Context,'DISCUSSION',$DiscussionID,1);
			}

			foreach($Notifieusers as $val) {
				$roleID = $val[4];
				$allowToMail = "yes";

				// Check category permissions
				for ($i=0;$i<count($rolesAry);$i++) {
					$record=$rolesAry[$i];
					$databaseRoleID = $record[1];
					if ($databaseRoleID == $roleID) {
						$allowToMail = "no";
					}
				}

				// Check if user is permitted to view this category
				if ($allowToMail == "yes") {
					$mName = '';
					if ($val[2] != '') {
						$mName = $val[2];
					}
					if ($val[1] != "" AND !in_array($val[1],$mailsent)) {
						if ($val[2] != "" AND $val[3] != "") {
							$NotifiName = '';
						} else {
							$NotifiName = $val[2].' '.$val[3];
						}

						// Begin preparing the email
						$e->Clear();
						$e->AddFrom($DiscussionForm->Context->Configuration['SUPPORT_EMAIL'], $DiscussionForm->Context->Configuration['SUPPORT_NAME']);
						$e->AddRecipient($val[1], $NotifiName);
						$e->Subject = $DiscussionForm->Context->Configuration['APPLICATION_TITLE'].' '.$DiscussionForm->Context->GetDefinition('Notification');

						// Save the ID of the user to be emailed
						$currentUserId = $val[0];

						// Get the last viewed comment from the database
						$result            = mysql_query("SELECT CountComments FROM ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."UserDiscussionWatch WHERE UserID=".$currentUserId." AND DiscussionID=".$DiscussionID,$DiscussionForm->Context->Database->Connection);
						$list              = mysql_fetch_row($result);
						$lastViewedComment = $list[0];

						// Find out which page to go to
						$commentsPerPage = $DiscussionForm->Context->Configuration['COMMENTS_PER_PAGE'];
						$pageNumber      = ceil($lastViewedComment/$commentsPerPage);
						$jumpToComment   = $lastViewedComment-($pageNumber.'0'-10);

						// Include the email templates
						include('_includes/emailtemplates.php');

						// See if the admin has chosen to send emails as plaintext
						if ($DiscussionForm->Context->Configuration['NOTIFI_FORMAT_PLAINTEXT'] == 1) {
							if (isset($CommentID)) {
								// If this is a new comment in an existing discussion
								$message = $plainTextOldDiscussion;
							} else {
								// If this is a new discussion
								$message = $plainTextNewDiscussion;
							}
						} else {
							// If BBCode support is enabled, process it
							if ($DiscussionForm->Context->Configuration['NOTIFI_ALLOW_BBCODE'] == 1) {
								$mComment = htmlentities($mComment);
								$bbSearch = array(
									'/\[b\](.*?)\[\/b\]/is',
									'/\[i\](.*?)\[\/i\]/is',
									'/\[url\=(.*?)\](.*?)\[\/url\]/is',
									'/\[url\](.*?)\[\/url\]/is',
									'/\[img\](.*?)\[\/img\]/is',
									'/\[br\]/is',
									'/\[quote\](.*?)\[\/quote\]/is',
									'/\[cite\](.*?)\[\/cite\]/is',
								);
								$bbReplace = array(
									'<strong>$1</strong>',
									'<em>$1</em>',
									'<a href="$1">$2</a>',
									'<a href="$1">$1</a>',
									'<img src="$1" />',
									'<br />',
									'<blockquote>$1</blockquote>',
									'<cite>$1</cite>',
								);
								$mComment = preg_replace($bbSearch, $bbReplace, $mComment);
							}

							// Insert line breaks in the comment
							$mComment = nl2br($mComment);

							if (isset($CommentID)) {
								// If this is a new comment in an existing discussion
								$message = $htmlOldDiscussion;
							} else {
								// If this is a new discussion
								$message = $htmlNewDiscussion;
							}
						}

						$e->Body = $message;
						$e->Send();

						array_push($mailsent,$val[1]);
						mysql_query("UPDATE ".$DiscussionForm->Context->Configuration['DATABASE_TABLE_PREFIX']."User SET Notified = 1 WHERE UserID = '".$val[0]."'");
					}
				}
			}
		}
	}
}

$Context->AddToDelegate('DiscussionForm','PostSaveDiscussion','NotifiDiscussion');
$Context->AddToDelegate('DiscussionForm','PostSaveComment','NotifiDiscussion');

if (in_array($Context->SelfUrl, array('account.php'))) {
	if (!@$UserManager) {
		unset($UserManager);
	}
	$UserManager = $Context->ObjectFactory->NewContextObject($Context, "UserManager");
	$AccountUserID = ForceIncomingInt("u", $Context->Session->UserID);
	if (!@$AccountUser) {
		$AccountUser = $UserManager->GetUserById($AccountUserID);
	}
	if ($Context->Session->User) {
		if (($AccountUser->UserID == $Context->Session->UserID OR $Context->Session->User->Permission("PERMISSION_EDIT_USERS")) AND $Context->Configuration['NOTIFI_AUTO_ALL'] == 0) {
			if (isset($_GET['u'])) {
				if ($_GET['u'] == $AccountUser->UserID) {
					include('_includes/usersettings.php');
					$Panel->AddListItem($Context->GetDefinition('AccountOptions'), $Context->GetDefinition('EmailNotification'), GetUrl($Configuration, $Context->SelfUrl, "", "", "", "", "u=".ForceIncomingInt('u',$Context->Session->UserID)."&amp;PostBackAction=Notification"), "", "", 92);
					$Page->AddRenderControl($Context->ObjectFactory->NewContextObject($Context, "NotificationControl"), $Configuration["CONTROL_POSITION_BODY_ITEM"]);
				}
			} else {
				include('_includes/usersettings.php');
				$Panel->AddListItem($Context->GetDefinition('AccountOptions'), $Context->GetDefinition('EmailNotification'), GetUrl($Configuration, $Context->SelfUrl, "", "", "", "", "u=".ForceIncomingInt('u',$Context->Session->UserID)."&amp;PostBackAction=Notification"), "", "", 92);
				$Page->AddRenderControl($Context->ObjectFactory->NewContextObject($Context, "NotificationControl"), $Configuration["CONTROL_POSITION_BODY_ITEM"]);
			}
		}
	}
}

if (in_array($Context->SelfUrl, array('comments.php','index.php','account.php','categories.php'))) {
	$Head->AddStyleSheet('extensions/Notifi/style.css', 'screen', 100);
	$Head->AddScript('extensions/Notifi/functions.js', '~', 350);
}

if ($Context->Session->UserID > 0) {
	mysql_query("UPDATE ".$Context->Configuration['DATABASE_TABLE_PREFIX']."User SET Notified = 0 WHERE UserID = '".$Context->Session->UserID."'");
}

if ($Context->SelfUrl == "settings.php" && $Context->Session->User->Permission('PERMISSION_MANAGE_EXTENSIONS')) {
	include('_includes/adminsettings.php');
}

// Remind admin/s to customize settings
if ($Context->SelfUrl == 'index.php' && !array_key_exists('NOTIFI_SETTINGS_NOTICE', $Configuration)) {
	if ($Context->Session->User && $Context->Session->User->Permission('PERMISSION_MANAGE_EXTENSIONS')) {
		$HideNotice = ForceIncomingBool('TurnOffNotifiSettingsNotice', 0);
		if ($HideNotice) {
			AddConfigurationSetting($Context, 'NOTIFI_SETTINGS_NOTICE', '1');
		} else {
			$NoticeCollector->AddNotice('<span><a href="'.GetUrl($Configuration, 'index.php', '', '', '', '', 'TurnOffNotifiSettingsNotice=1').'">'.$Context->GetDefinition('RemoveThisNotice').'</a></span>
			'.$Context->GetDefinition('RememberToSetNotifiSettingsPermissions'));
		}
	}
}
?>