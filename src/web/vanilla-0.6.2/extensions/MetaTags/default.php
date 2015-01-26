<?php
/*
Extension Name: Meta Tags
Extension Url: http://lussumo.com/docs/
Description: Adds a meta tags to the header
Version: 1.0
Author: Chris Vincent
Author Url: http://ourpaintbox.com

*/

$Context->Dictionary["MetaTags"] = "MetaTags";

class MetaTags extends Control {
  
  var $Name;
  var $Context;
  var $MetaData;
  
  function MetaTags(&$Context) {
    $this->Name = 'MetaTags';
    $this->Context = $Context;  
	$this->MetaData = Array();   
	$this->MaxChar = 150;
	$this->MaxWords = 50;
  }
  
  function Render() {
    if($this->PostBackAction == "SaveMetaTags") {
	  
	}   

    echo 'ok... this part hasnt bee finished yet...';

    // Get tags and then show
  }
 
  function AddData($Name = '', $Content = '') {
    if($Name == '' || $Content == '') return false;
    $this->MetaData[] = Array(0 => $Name, 1 => $Content);
    return true;
  }
  
  function GetData() {
    $MetaTags = Array();
    while (list($a, $b) = each($this->MetaData)) {
      $b = $this->FormatTag($b);
	  $MetaTags[] = "\n      <meta name='".$b[0]."' content='".$b[1]."' />";
	}
    return $MetaTags;  
  }
  
  function FormatTag($Stuff) {
    
	switch(strtolower($Stuff[0])) {
	  
	  case 'keywords':
	    $Stuff[1] = substr($Stuff[1], 0, $this->MaxChar);
	    break;
	    
	  case 'description':
        $string = str_replace(Array("\r", "\n", "'", '"'), Array('', " ", '', ''), explode(' ', $Stuff[1]));
        $Stuff[1] = implode(' ', array_splice($string, 0, $this->MaxWords));
	    break; 
	}
	return $Stuff;  
  }
  
}

$MetaTags = $Context->ObjectFactory->NewContextObject($Context, 'MetaTags');

// From here one you can add whatever you want to the meta tags untill the template closes
// Start Meta Template //

$MetaTags->AddData('keywords', 'musiques incongrues, da ! heard it records, the brain, egotwister');

if($Context->SelfUrl == 'comments.php') {
  
  $query = mysql_fetch_assoc(
    $Context->Database->Execute('SELECT LUM_Comment.Body
				 FROM LUM_Comment
				 WHERE LUM_Comment.DiscussionID = '.ForceIncomingInt("DiscussionID", 0).';', 
				 '', '', '', ''));

  // This Finds the Discussion ID  
  //$CommentManager = &$Context->ObjectFactory->NewContextObject($Context, 'CommentManager');
  //$FirstComment = &$CommentManager->GetCommentById(ForceIncomingInt("DiscussionID", 0), $Context->Session->User->UserID);
  //$Description = &$FirstComment->Body;

  $MetaTags->AddData('description', strip_tags($query['Body']));
} else {
  $MetaTags->AddData('description', 'Labels, Mixes, Releases, Events, Radio, Forum & More !');
}

// End Meta Template //

$Data = $MetaTags->GetData();
while (list($a, $b) = each($Data)) {
  $Head->AddString($b);
}
$Head->AddString("\n");

if($Context->SelfUrl == 'settings.php') {
  $Page->AddRenderControl($MetaTags, $Configuration['CONTROL_POSITION_BODY_ITEM'] + 80);
  $Panel->AddListItem($Context->GetDefinition('AdministrativeOptions'), 'MetaTags', GetUrl($Context->Configuration, $Context->SelfUrl, '', '', '', '', 'PostBackAction=MetaTags'), '', '', 91);
}

?>
