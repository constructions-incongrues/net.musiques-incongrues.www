<?php
// Utility functions used by the Atom extension
function AuthenticateUserForRSS2(&$Context) {
   // Perform some http authentication if public browsing is not enabled.
   if ($Context->Configuration['AUTHENTICATE_USER_FOR_RSS2']) {
      $UserIsAuthenticated = false; // Assume user is not authenticated
      $PHP_AUTH_USER = ForceString(@$_SERVER['PHP_AUTH_USER'], '');
      $PHP_AUTH_PW = ForceString(@$_SERVER['PHP_AUTH_PW'], '');
      
      if ($PHP_AUTH_USER != '' && $PHP_AUTH_PW != '') {
         // Validate the inputs
         $s = $Context->ObjectFactory->NewContextObject($Context, 'SqlBuilder');
         $s->SetMainTable('User', 'u');
         $s->AddSelect('UserID', 'u');
         $s->AddWhere('u', 'Name', '', FormatStringForDatabaseInput($PHP_AUTH_USER), '=');
         $s->StartWhereGroup();
         $s->AddWhere('u', 'Password', '', FormatStringForDatabaseInput($PHP_AUTH_PW), '=', 'and', 'md5');
         $s->AddWhere('u', 'Password', '', FormatStringForDatabaseInput($PHP_AUTH_PW), '=', 'or');
         $s->EndWhereGroup();
         
         $ValidationData = $Context->Database->Select($s, 'Feed', 'ValidateCredentials', 'An error occurred while validating user credentials.');
         if ($Context->Database->RowCount($ValidationData) > 0) $UserIsAuthenticated = true;
      }         
      
      if (!$UserIsAuthenticated) {
         header('WWW-Authenticate: Basic realm="Private"');
         header('HTTP/1.0 401 Unauthorized');

        	echo('<h1>'.$Context->GetDefinition('FailedFeedAuthenticationTitle').'</h1>
			<h2>'.$Context->GetDefinition('FailedFeedAuthenticationText').'</h2>');
			$Context->Unload();
			die();
      }
   }
}

// Attach to the DiscussionManager's GetDiscussionBuilder method to ensure that the
// first comment is returned as well.
function DiscussionManager_GetFirstCommentForRSS2($DiscussionManager) {
	// SqlBuilder is passed by reference.
	$SqlBuilder = &$DiscussionManager->DelegateParameters['SqlBuilder'];
	$SqlBuilder->AddJoin('Comment', 'fc', 'CommentID', 't', 'FirstCommentID', 'left join');
	$SqlBuilder->AddSelect(array('FormatType', 'Body'), 'fc');
}
   
function FixDateForRSS2($Date = '') {
   // Dates need to be in RFC 2822 format
   $DateFormat = 'r';
   if ($Date == '') {
      return date($DateFormat, mktime());
   } else {
      return date($DateFormat, UnixTimestamp($Date));
   }
}

function FormatStringForRSS2Summary($String) {
   $sReturn = strip_tags($String);
   $sReturn = htmlspecialchars($sReturn);
   $sReturn = SliceString($sReturn, 200);
   return str_replace('\r\n', ' ', $sReturn);
}

function ReturnWrappedFeedForRSS2(&$Context, $FeedItems) {
	return '<rss version="2.0">
		<channel>
			<title>'.htmlspecialchars($Context->Configuration['APPLICATION_TITLE'].' - '.$Context->PageTitle).'</title>
			<lastBuildDate>'.FixDateForRSS2().'</lastBuildDate>
			<link>'.$Context->Configuration['BASE_URL'].'</link>
			<description></description>
			<generator>Lussumo Vanilla '.APPLICATION_VERSION.'</generator>
			'.$FeedItems.'
		</channel>
	</rss>';
}

function ReturnFeedItemForRSS2($Properties) {
	return '<item>
		<title>'.$Properties['Title'].'</title>
		<link>'.$Properties['Link'].'</link>
		<guid isPermaLink="false">'.$Properties['Link'].'</guid>
		<pubDate>'.$Properties['Published'].'</pubDate>
		<author>'.$Properties['AuthorName'].'</author>
		<description>
			<![CDATA['.$Properties['Content'].']]>
		</description>
	</item>
	';
}

?>