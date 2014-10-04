<?php
// Utility functions used by the Atom extension
function AuthenticateUserForAtom(&$Context) {
   // Perform some http authentication if public browsing is not enabled.
   if ($Context->Configuration['AUTHENTICATE_USER_FOR_ATOM']) {
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
         header("WWW-Authenticate: Basic realm='Private'");
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
function DiscussionManager_GetFirstCommentForAtom($DiscussionManager) {
	// SqlBuilder is passed by reference.
	$SqlBuilder = &$DiscussionManager->DelegateParameters['SqlBuilder'];
	$SqlBuilder->AddJoin('Comment', 'fc', 'CommentID', 't', 'FirstCommentID', 'left join');
	$SqlBuilder->AddSelect(array('FormatType', 'Body'), 'fc');
}
		
function FixDateForAtom($Date = '') {
   $DateFormat = 'Y-m-d\TH:i:sO';
   if ($Date == '') {
      $NewDate = date($DateFormat, time());
   } else {
      $NewDate = date($DateFormat, UnixTimestamp($Date));
   }
   
   // Dates that look like this:
   // 2005-07-23T18:44:53-0400
   // Need to look like this:
   // 2005-07-23T18:44:53-04:00
   if (strlen($NewDate) != 24) {
      return $NewDate;
   } else {
      return substr($NewDate, 0, 22).':'.substr($NewDate, 22);
   }
}

function FormatStringForAtomSummary($String) {
   $sReturn = strip_tags($String);
   $sReturn = htmlspecialchars($sReturn);
   $sReturn = SliceString($sReturn, 200);
   return str_replace('\r\n', ' ', $sReturn);
}

function ReturnWrappedFeedForAtom(&$Context, $FeedItems) {
	$p = $Context->ObjectFactory->NewObject($Context, 'Parameters');
	$p->DefineCollection($_GET);
	$SelfLink = GetUrl($Context->Configuration, $Context->SelfUrl, '', '', '', '', $p->GetQueryString());
	$p->Remove('Feed');
	$AlternateLink = GetUrl($Context->Configuration, $Context->SelfUrl, '', '', '', '', $p->GetQueryString());
			
	return '<?xml version="1.0" encoding="utf-8"?>
		<feed xmlns="http://www.w3.org/2005/Atom">
		  <title type="text">'.htmlspecialchars($Context->Configuration['APPLICATION_TITLE'].' - '.$Context->PageTitle).'</title>
		  <updated>'.FixDateForAtom().'</updated>
		  <id>'.$Context->Configuration['BASE_URL'].'</id>
		  <link rel="alternate" type="text/html" hreflang="en" href="'.$AlternateLink.'"/>
		  <link rel="self" type="application/atom+xml" href="'.$SelfLink.'"/>
		  <generator uri="http://getvanilla.com/" version="'.APPLICATION_VERSION.'">
			 Lussumo Vanilla
		  </generator>
		  '.$FeedItems.'
		</feed>';
}

function ReturnFeedItemForAtom($Properties) {
	return '<entry>
		<title>'.$Properties['Title'].'</title>
		<link rel="alternate" href="'.$Properties['Link'].'" type="application/xhtml+xml" hreflang="en"/>
		<id>'.$Properties['Link'].'</id>
		<published>'.$Properties['Published'].'</published>
		<updated>'.$Properties['Updated'].'</updated>
		<author>
			<name>'.$Properties['AuthorName'].'</name>
			<uri>'.$Properties['AuthorUrl'].'</uri>
		</author>
		<summary type="text" xml:lang="en">
			'.$Properties['Summary'].'
		</summary>
		<content type="html">
			<![CDATA['.$Properties['Content'].']]>
		</content>
	</entry>
	';
}

?>
