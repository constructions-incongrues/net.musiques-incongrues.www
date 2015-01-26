<?php
/*
 Extension Name: MiRandomDiscussion
 Extension Url: https://github.com/contructions-incongrues
 Description: Redirects to a random discussion.
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */

// Configure "Shows" page rendering
$postBackAction = ForceIncomingString("PostBackAction", "");
if ($postBackAction == 'RandomDiscussion') {

	// Build selection query
	$sql = $Context->ObjectFactory->NewContextObject($Context, 'SqlBuilder');
	$sql->SetMainTable('Discussion','d');
	$sql->AddSelect('DiscussionID', 'd');
	$sql->AddSelect('Name', 'd');
	$sql->AddWhere('d', 'Active', '', 1, '=');
	$sql->AddOrderBy(array(), '', '', 'RAND');

	// Execute query
	$db = $Context->Database;
	$rs = $db->Execute($sql->GetSelect(), $Context, __FUNCTION__, 'Failed to fetch from database.');

	// Retrieve data
	$discussion = $db->GetRow($rs);
	$urlDiscussion = GetUrl($Context->Configuration, 'comments.php', '', 'DiscussionID', $discussion['DiscussionID'], '', '#Header', CleanupString($discussion['Name']).'/');
	
	// Redirect to discussion
	header('Location:'.$urlDiscussion);
}