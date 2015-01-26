<?php
/*
Extension Name: Preview Post
Extension Url: http://vanillaforums.org/addon/84/preview-post
Description: Allows users to view a preview of what their post will look like.
Version: 2.5.3
Author: SirNotAppearingOnThisForum
Author Url: N/A
 *
 * Copyright 2006 Sirnot
 * Copyright 2010 Damien Lebrun <dinoboff@gmail.com>
 *
 * TODO: Convert to jquery.
 * TODO: Let the server side build the comment, not just the message.
 * 
 */


if (!defined('IN_VANILLA')) {
	exit();
}


$Context->SetDefinition('PostPreview', 'Preview');
$Context->SetDefinition('PreviewPost','Preview Post');

if(!in_array($Context->SelfUrl, array('post.php', 'comments.php'))
	|| !isset($Head)
) {
	return;
}


$Head->AddScript('extensions/PreviewPost/preview.js', '~', 390);
$Head->AddStyleSheet('extensions/PreviewPost/preview.css', 'screen');

$Context->AddToDelegate(
	'DiscussionForm',
	'DiscussionForm_PostSubmitRender',
	'PreviewPostButton');
$Context->AddToDelegate(
	'DiscussionForm',
	'CommentForm_PostSubmitRender',
	'PreviewPostButton');

function PreviewPostButton(&$Form) {
	$Context = $Form->Context;
	$User = $Context->Session->User;

	printf(
		'<input type="button" name="btnPreview" value="%s" '
			. 'class="Button SubmitButton PreviewButton" '
			. 'onclick="showpreview(\'%s\', {id: %d, name: \'%s\'});" />',
		$Context->GetDefinition('PreviewPost'),
		$Context->Configuration['BASE_URL'],
		$User->UserID,
		!empty($User->Name) ? FormatStringForDisplay($User->Name) : 'Guest'
	);
}
