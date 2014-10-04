<?php
/*
Extension Name: Unanswered Discussions
Extension Url: http://lussumo.com/addons
Description: Provides a filter or a tab to view discussions without comments.
Version: 0.1
Author: Based on SirNot's Participated Threads, reworked by WallPhone then Grahack
Author Url: http://wallphone.com/
*/


/* SETTINGS */

// add 'Unanswered Discussions' tab
define('UD_ADD_TAB', false);

//true = add discussion filter
define('UD_ADD_FILTER', true);

$Context->SetDefinition('UnansweredDiscussions', 'Unanswered Discussions');

/* END OF SETTINGS */


// let's provide the links (filter or tab) for Unanswered Discussions
if ( isset( $Menu ) && isset( $Panel ) )
{
	$Url = GetUrl(
		$Configuration,
		'index.php', '', '', '', '',
		'View=UnansweredDiscussions');
		
	if ( UD_ADD_TAB )
	{
		$Menu->AddTab(
			$Context->GetDefinition('UnansweredDiscussions'),
			'UnansweredDiscussions',
			$Url, '', 100);
	}
	
	$PagesWithFilterLink =
		array( 'index.php', 'categories.php', 'comments.php', 'post.php' );
	if ( UD_ADD_FILTER && in_array( $Context->SelfUrl, $PagesWithFilterLink ))
	{
		if( ! isset( $Context->Dictionary['DiscussionFilters'] ))
		{
			$DiscussionFilters = 'Discussion Filters';
			$Panel->AddList($DiscussionFilters, 10);
		}
		else $DiscussionFilters = $Context->GetDefinition('DiscussionFilters');
		
		$Panel->AddListItem(
			$DiscussionFilters,
			$Context->GetDefinition('UnansweredDiscussions'),
			$Url );
	}
}

// let's process the search for Unanswered Discussions
if(
	$Context->SelfUrl == 'index.php'
	&& ForceIncomingString( 'View', '' ) == 'UnansweredDiscussions' )
{
		$Context->AddToDelegate(
			'DiscussionManager',
			'PostGetDiscussionBuilder',
			'UnansweredDiscussionsBuilder' );
		$Context->AddToDelegate(
			'DiscussionManager',
			'PreGetDiscussionCount',
			'UnansweredDiscussionsCounter' );
		$Context->AddToDelegate(
			'DiscussionGrid',
			'Constructor',
			'UnansweredDiscussionsConstructor' );
	
	function UnansweredDiscussionsBuilder( &$D )
	{
		$s = &$D->DelegateParameters['SqlBuilder'];
		
		$s->Fields = 'distinct '.$s->Fields;
		$s->AddWhere( 't', 'CountComments', '', '1', '=' );
	}
	
	function UnansweredDiscussionsCounter( &$D )
	{
		$s = &$D->DelegateParameters['SqlBuilder'];
		
		$s->Fields =
			'count(distinct t.'
			.$GLOBALS['DatabaseColumns']['Discussion']['DiscussionID'].') as Count';
		$s->AddWhere( 't', 'CountComments', '', '1', '=' );
	}
	
	function UnansweredDiscussionsConstructor( &$F )
	{
		$F->Context->PageTitle .=
			' : '.$F->Context->GetDefinition('UnansweredDiscussions');
	}
	
	// let's set the current tab to Unanswered Discussions
	if( UD_ADD_TAB )
	{
		function UnansweredDiscussionsSetTab( &$F )
		{
			$F->CurrentTab = 'UnansweredDiscussions';
		}
		$Context->AddToDelegate(
			'Menu',
			'PreRender',
			'UnansweredDiscussionsSetTab');
	}
}