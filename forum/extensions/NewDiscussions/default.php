<?php
/*
Extension Name: New Discussions
Extension Url: http://lussumo.com/addons
Description: A discussion filter which displays only unread discussions.
Version: 1.0
Author: SirNot
Author Url: N/A
*/

//boolean values
define('ND_ADD_TAB',		0); //true = add tab
define('ND_ADD_FILTER', 	1); //true = add as discussion filter

$Context->Dictionary['NewDiscussions'] = 'New Discussions';

if(isset($Menu) && isset($Panel) && $Context->Session->User->UserID)
{
	if(ND_ADD_TAB)
		$Menu->AddTab($Context->GetDefinition('NewDiscussions'), 'new_discussions', 
			$Context->Configuration['WEB_ROOT'].'?View=NewDiscussions', '', 101);
	if(ND_ADD_FILTER && in_array($Context->SelfUrl, array('index.php', 'categories.php', 'comments.php')))
	{
		if(!isset($Context->Dictionary['DiscussionFilters']))
		{
			$Context->Dictionary['DiscussionFilters'] = $DiscussionFilters = 'Discussion Filters';
			$Panel->AddList($DiscussionFilters, 10);
		}
		else $DiscussionFilters = $Context->GetDefinition('DiscussionFilters');
		
		$Panel->AddListItem($DiscussionFilters, $Context->GetDefinition('NewDiscussions'), 
			$Context->Configuration['WEB_ROOT'].'?View=NewDiscussions');
	}
}

if($Context->SelfUrl == 'index.php' && ForceIncomingString('View', '') == 'NewDiscussions' && $Context->Session->User->UserID)
{
	$Context->AddToDelegate('DiscussionManager', 'PostGetDiscussionBuilder', 'NewDiscussionsBuilder');
	$Context->AddToDelegate('DiscussionManager', 'PreGetDiscussionCount', 'NewDiscussionsCounter');
	$Context->AddToDelegate('DiscussionGrid', 'Constructor', 'NewDiscussionsConstructor');
	if(ND_ADD_TAB) $Context->AddToDelegate('Menu', 'PreRender', 'NewDiscussionsSetTab');
	
	function NewDiscussionsBuilder(&$D)
	{
		$s = &$D->DelegateParameters['SqlBuilder'];
		
		$s->AddWhere('t', 'CountComments', 'utw', 'CountComments', '>', 'and', '', 0, 1);
		$s->AddWhere('', '', 'utw', 'CountComments', '', 'or', 'ISNULL', 0, 0);
		$s->EndWhereGroup();
	}
	
	function NewDiscussionsCounter(&$D)
	{
		$D->DelegateParameters['SqlBuilder']->AddJoin(
			'UserDiscussionWatch', 'utw', 'DiscussionID', 't', 'DiscussionID', 'left join', 
			' and utw.'.$D->Context->DatabaseColumns['UserDiscussionWatch']['UserID'].' = '.
			$D->Context->Session->UserID
		);
		
		NewDiscussionsBuilder($D);
	}
	
	function NewDiscussionsConstructor(&$F)
	{
		$F->Context->PageTitle .= ' : '.$F->Context->GetDefinition('NewDiscussions');
	}
	
	function NewDiscussionsSetTab(&$F)
	{
		$F->CurrentTab = 'new_discussions';
	}
}