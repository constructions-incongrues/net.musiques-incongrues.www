<?php
/*
Extension Name: Preview Post
Extension Url: http://lussumo.com/addons
Description: Allows users to view a preview of what their post will look like.
Version: 2.5.2
Author: SirNotAppearingOnThisForum
Author Url: N/A

You should cut & paste these language definitions into your
conf/your_language.php file (replace "your_language" with your chosen language,
of course):
*/
$Context->Dictionary['PostPreview'] = 'Preview';
$Context->Dictionary['PreviewPost'] = 'Preview Post';

if(in_array($Context->SelfUrl, array('post.php', 'comments.php')) && isset($Head))
{
	$Head->AddScript('extensions/PreviewPost/preview.js');
	$Head->AddStyleSheet('extensions/PreviewPost/preview.css');
	
	$Context->AddToDelegate('DiscussionForm', 'DiscussionForm_PostSubmitRender', 'PreviewPostButton');
	$Context->AddToDelegate('DiscussionForm', 'CommentForm_PostSubmitRender', 'PreviewPostButton');
	
	$PrevButtonHTML = 'showpreview(\''.$Context->Configuration['BASE_URL'].'\', {id : '.$Context->Session->User->UserID.
		', name : \''.(!empty($Context->Session->User->Name) ? $Context->Session->User->Name : 'Guest').'\'});';
	$PrevButtonHTML = '<input name="btnPreview" value="'.$Context->GetDefinition('PreviewPost').
		'" class="Button SubmitButton PreviewButton" type="button" onclick="'.$PrevButtonHTML.'" />';
	
	function PreviewPostButton(&$Form) {echo($GLOBALS['PrevButtonHTML']);}
}

?>