<?php
/*
Extension Name: JQuery
Extension Url: http://lussumo.com/addons/?PostBackAction=AddOn&AddOnID=231
Description: jQuery JavaScript Library version 1.2.6.  THIS NEEDS TO BE ENABLED BEFORE ALL OTHER JQ EXTENSIONS and AFTER LightBox!!!
Version: v15-1.2.6
Author: Luke Scammell aka [-Stash-]
Author Url: http://scammell.co.uk/
*/
 if(!isset($Head)) return;

 // a little security
 if (!defined('IN_VANILLA')) exit();
 define('JQUERY_EXTENSION', true);
 $JQPPath = 'extensions/JQuery/plugins/';
 // Please don't mess with these unless you know what you're doing, otherwise you can break update version checking
 $JQext = 'v15-1.2.6';
 $JQjs = '1.2.6';

 // Set the Extension and JavaScript version numbers.
 if (!array_key_exists('JQUERY_VERSION_EXT', $Configuration) || ($Configuration['JQUERY_VERSION_EXT'] != $JQext)) {
  AddConfigurationSetting($Context, 'JQUERY_VERSION_EXT', $JQext);
 }
 if (!array_key_exists('JQUERY_VERSION_JS', $Configuration) || ($Configuration['JQUERY_VERSION_JS'] != $JQjs)) {
  AddConfigurationSetting($Context, 'JQUERY_VERSION_JS', $JQjs);
 }
 // Enable inline images to be clickable for larger versions
 if(!array_key_exists('JQUERY_FILE_TYPE', $Configuration)) {
  AddConfigurationSetting($Context, 'JQUERY_FILE_TYPE', '0');
 }
 // Plugins
 if(!array_key_exists('JQUERY_PLUGIN_THICKBOX', $Configuration)) {
  AddConfigurationSetting($Context, 'JQUERY_PLUGIN_THICKBOX', '1');}
 if(!array_key_exists('JQUERY_PLUGIN_LIGHTBOX', $Configuration)) {
  AddConfigurationSetting($Context, 'JQUERY_PLUGIN_LIGHTBOX', '0');}
 if(!array_key_exists('JQUERY_PLUGIN_SHADOWBOX', $Configuration)) {
  AddConfigurationSetting($Context, 'JQUERY_PLUGIN_SHADOWBOX', '0');}
 if(!array_key_exists('JQUERY_PLUGIN_HIDESPOILER', $Configuration)) {
  AddConfigurationSetting($Context, 'JQUERY_PLUGIN_HIDESPOILER', '0');}
 if(!array_key_exists('JQUERY_PLUGIN_SMOOTHPAGESCROLL', $Configuration)) {
  AddConfigurationSetting($Context, 'JQUERY_PLUGIN_SMOOTHPAGESCROLL', '0');}
 function includeJQuery() {
  if(defined('JQUERY_INCLUDED')) return;
  global $Head, $Configuration, $Context;
   /* Use jQuery 1.2.6 */
   // Default - Minified
   if ($Context->Configuration['JQUERY_FILE_TYPE'] == '0') {$Head->AddScript('extensions/JQuery/jquery-1.2.6.min.js');}
   // Compressed - Packed - only use this if you can't get GZ on your server
   elseif ($Context->Configuration['JQUERY_FILE_TYPE'] == '1') {$Head->AddScript('extensions/JQuery/jquery-1.2.6.pack.js');}
   // Development Mode - Original
   elseif ($Context->Configuration['JQUERY_FILE_TYPE'] == '2') {$Head->AddScript('extensions/JQuery/jquery-1.2.6.js');}
   // Old Version - 1.1.4 minified
   elseif ($Context->Configuration['JQUERY_FILE_TYPE'] == '3') {$Head->AddScript('extensions/JQuery/old/jquery-1.1.4.min.js');}
   define('JQUERY_INCLUDED', true);
 }

 // ThickBox
/* if (in_array($Context->SelfUrl, array('account.php','categories.php','comments.php','extension.php','index.php','people.php','post.php','search.php','settings.php','termsofservice.php'))) {*/
  if ($Context->Configuration['JQUERY_PLUGIN_THICKBOX'] == '1') {
   includeJQuery(); // call JQuery to ensure it's loaded first 
   $Head->AddStylesheet($JQPPath.'ThickBox/thickbox-3.1.min.css');
   $Head->AddScript($JQPPath.'ThickBox/thickbox-3.1.min.js');
  }
/* }*/

 // lightbox
/* if (in_array($Context->SelfUrl, array('account.php','categories.php','comments.php','extension.php','index.php','people.php','post.php','search.php','settings.php','termsofservice.php'))) {*/
  if ($Context->Configuration['JQUERY_PLUGIN_LIGHTBOX'] == '1') {
   includeJQuery(); // call JQuery to ensure it's loaded first 
   $Head->AddStylesheet($JQPPath.'lightbox/lightbox.min.css');
   $Head->AddScript($JQPPath.'lightbox/lightbox.min.js');
   /*$Head->AddString('
				<script type="text/javascript" src="'.$Configuration['BASE_URL'].'extensions/JQuery/plugins/lightbox/lightbox.js'.'"></script>
');*/
  }
/* }*/

 // HideSpoiler
  /*if ($Context->Configuration['JQUERY_PLUGIN_HIDESPOILER'] == '1') {
   includeJQuery(); // call JQuery to ensure it's loaded first 
   $Head->AddStylesheet($JQPPath.'HideSpoiler/HideSpoiler.css');
   $Head->AddScript($JQPPath.'HideSpoiler/HideSpoiler.js');
  }*/
 // HideSpoiler
  if ($Context->Configuration['JQUERY_PLUGIN_HIDESPOILER'] == '1') {
	  if(in_array($Context->SelfUrl, array("post.php", "comments.php"))) {
   includeJQuery(); // call JQuery to ensure it's loaded first 
   $Head->AddStylesheet($JQPPath.'HideSpoiler/HideSpoiler.css');
   $Head->AddScript($JQPPath.'HideSpoiler/HideSpoiler.js');
   if(!defined('IN_VANILLA')) exit();
	  	class HideSpoiler extends StringFormatter {
	    function Parse ($String, $Object, $FormatPurpose) {
	        global $Configuration;
	        $CommentList = &$CommentGrid->DelegateParameters["CommentList"];
	  			$sReturn = $String;
	  			if($FormatPurpose  == FORMAT_STRING_FOR_DISPLAY) { // This is what you type in the comment box
	  				$Patterns = array("/\[hide\](.+?)\[\/hide\]/is");
	    				$Replacements = array('<span class="Hidden">$1</span>');
	  				$sReturn = preg_replace($Patterns, $Replacements, $sReturn);
	  			}
	  			return $sReturn;
	  		}
	  	}
	 	// Global StringFormatter
	 	$HideSpoiler = $Context->ObjectFactory->NewObject($Context, "HideSpoiler");
	 	$Context->StringManipulator->AddGlobalManipulator("HideSpoiler", $HideSpoiler);
	  }
  }

 // Shadowbox
/* if (in_array($Context->SelfUrl, array('account.php','categories.php','comments.php','extension.php','index.php','people.php','post.php','search.php','settings.php','termsofservice.php'))) {*/
  if ($Context->Configuration['JQUERY_PLUGIN_SHADOWBOX'] == '1') {
   includeJQuery(); // call JQuery to ensure it's loaded first 
   $Head->AddStylesheet($JQPPath.'Shadowbox/shadowbox.css');
   $Head->AddScript($JQPPath.'Shadowbox/shadowbox-jquery.js');
   $Head->AddScript($JQPPath.'Shadowbox/shadowbox.js');
  }
/* }*/

 // SmoothPageScroll
/* if (in_array($Context->SelfUrl, array('account.php','categories.php','comments.php','extension.php','index.php','people.php','post.php','search.php','settings.php','termsofservice.php'))) {*/
  if ($Context->Configuration['JQUERY_PLUGIN_SMOOTHPAGESCROLL'] == '1') {
   includeJQuery(); // call JQuery to ensure it's loaded first 
   $Head->AddScript($JQPPath.'SmoothPageScroll/SmoothPageScroll.js');
  }
/* }*/
?>
