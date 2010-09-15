<?php
/*
Extension Name: CategoryIcons
Extension Url: http://www.jwurster.us/gfe/
Description: Add Category icons.
Version: 1.0.3
Author: Jim Wurster (DraganBabic)
Author Url: http://www.jwurster.us/
*/
/* version 1.0.3 - added check of config version in order to add config setting and avoid unnecessary database access
*/
if (!defined('IN_VANILLA')) exit();
$Configuration["CATEGORYICONS_PATH"] = 'extensions/CategoryIcons/';
// Set the version number.
if (!array_key_exists('CATEGORYICONS_VERSION', $Configuration))
{
    AddConfigurationSetting($Context, 'CATEGORYICONS_VERSION', '1.0.3');
} else if ($Configuration['CATEGORYICONS_VERSION'] != '1.0.3')
{
    AddConfigurationSetting($Context, 'CATEGORYICONS_VERSION', '1.0.3');
}
//
if (in_array($Context->SelfUrl, array("index.php", "categories.php")) )
{
    $Context->Dictionary['CategoryIcons'] = 'Category Icons';
    $Head->AddStyleSheet($Configuration["CATEGORYICONS_PATH"].'style.css');
}
?>
