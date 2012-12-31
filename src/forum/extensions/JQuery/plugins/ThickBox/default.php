<?php
/*
Extension Name: JQThickBox
Extension Url: http://lussumo.com/addons/?PostBackAction=AddOn&AddOnID=240
Description: THE JQUERY EXTENSION NEEDS TO BE ENABLED BEFORE THIS!!! jQuery Plugin - ThickBox 3.1.
Version: v12-3.1
Author: Luke Scammell aka [-Stash-]
Author Url: http://scammell.co.uk/
*/
 if(!isset($Head)) return;
 $Configuration["JQTHICKBOX_PATH"] = 'extensions/JQThickBox/';
 $JQTBext = 'v12-3.1';
 $JQTBjs = '3.1';

 // Set the Extension and JavaScript version numbers.
 if (!array_key_exists('JQTHICKBOX_VERSION_EXT', $Configuration) || ($Configuration['JQTHICKBOX_VERSION_EXT'] != $JQTBext)) {
  AddConfigurationSetting($Context, 'JQTHICKBOX_VERSION_EXT', $JQTBext);
 }
 if (!array_key_exists('JQTHICKBOX_VERSION_JS', $Configuration) || ($Configuration['JQTHICKBOX_VERSION_JS'] != $JQTBjs)) {
  AddConfigurationSetting($Context, 'JQTHICKBOX_VERSION_JS', $JQTBjs);
 }

 // Specify which pages to add JavaScript and CSS to Head Control and do it.
 if (in_array($Context->SelfUrl, array(
  'account.php',
  'categories.php',
  'comments.php',
  'extension.php',
  'index.php',
  'people.php',
  'post.php',
  'search.php',
  'settings.php',
  'termsofservice.php'))) {
   includeJQuery(); // call JQuery to ensure it's loaded first

  /* use ThickBox 3.1 */
   $Head->AddStylesheet($Context->Configuration['JQTHICKBOX_PATH'].'thickbox-3.1.css');
   $Head->AddScript($Context->Configuration['JQTHICKBOX_PATH'].'thickbox-3.1.min.js');

  /* use ThickBox 2.1.1 by removing the // from the beginning of the next three lines and adding // to the beginning of  the previous two lines. */
  // $Head->AddStylesheet($Context->Configuration['JQTHICKBOX_PATH'].'old/thickbox-2.1.1.css');
  // $Head->AddStylesheet($Context->Configuration['JQTHICKBOX_PATH'].'old/thickbox-2.1.1.ie.css');
  // $Head->AddScript($Context->Configuration['JQTHICKBOX_PATH'].'old/thickbox-2.1.1.min.js');
  }
?>