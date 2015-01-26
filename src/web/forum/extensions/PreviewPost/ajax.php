<?php
include('../../appg/settings.php');
include('../../appg/init_ajax.php');

//$Context->SelfUrl = 'comments.php';

/* begin */
include($Configuration['LIBRARY_PATH'].'Vanilla/Vanilla.Functions.php');

// Set the current page to the comments.php page.
$Context->SelfUrl = 'comments.php';

// Define these controls so that other extensions don't mess up and throw errors
// when trying to access page objects which should be present on the comments.php page.
$Page = $Context->ObjectFactory->NewContextObject($Context, 'Page', $Configuration['PAGE_EVENTS']);
$Head = $Context->ObjectFactory->CreateControl($Context, 'Head');
$Menu = $Context->ObjectFactory->CreateControl($Context, 'Menu');
$Panel = $Context->ObjectFactory->CreateControl($Context, 'Panel');
$NoticeCollector = $Context->ObjectFactory->CreateControl($Context, 'NoticeCollector');
$Foot = $Context->ObjectFactory->CreateControl($Context, 'Filler', 'foot.php');
$PageEnd = $Context->ObjectFactory->CreateControl($Context, 'PageEnd');
$Context->PassThruVars['SetBookmarkOnClick'] = '';

include('../../conf/extensions.php');
/* end */

$Text = ForceIncomingString('Data', '');
$Type = ForceIncomingString('Type', '');
if($Text != '' && $Type != '')
{
   if(MAGIC_QUOTES_ON) $Text = stripslashes($Text);
   
   $Context->Session->User->AuthUserID = $Context->Session->User->UserID;
   $Context->Session->User->AuthUsername = $Context->Session->User->Name;
   $Text = $Context->FormatString($Text, $Context->Session->User, $Type, FORMAT_STRING_FOR_DISPLAY);
   
   if(in_array($Type, array('Text', 'BBCode'))) $Text = str_replace(array("\r\n", "\r", "\n"), '<br />', $Text);
   
   header('Content-Type: text/html; charset=utf-8');
   echo($Text);
}
?>
