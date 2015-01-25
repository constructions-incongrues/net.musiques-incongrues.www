<?php

if (!defined('IN_VANILLA')) {
	exit();
}

echo '<li>
	<label for="ddMaxBookmarksInPanel">'.$GlobalsForm->Context->GetDefinition('MaxBookmarksInPanel').'</label>
	';
$Selector = $GlobalsForm->Context->ObjectFactory->NewObject($GlobalsForm->Context, 'Select');
$Selector->CssClass = 'SmallSelect';
$Selector->Attributes = ' id="ddMaxBookmarksInPanel"';
$Selector->Name = 'PANEL_BOOKMARK_COUNT';
for ($i = 3; $i < 11; $i++) {
	$Selector->AddOption($i, $i);
}
for ($i = 15; $i < 51; $i++) {
	$Selector->AddOption($i, $i);
	$i += 4;
}
$Selector->SelectedValue = $GlobalsForm->ConfigurationManager->GetSetting('PANEL_BOOKMARK_COUNT');
echo $Selector->Get().'
 </li>
 <li>
	<label for="ddMaxPrivateInPanel">'.$GlobalsForm->Context->GetDefinition('MaxPrivateInPanel').'</label>
	';
$Selector->Name = 'PANEL_PRIVATE_COUNT';
$Selector->Attributes = ' id="ddMaxPrivateInPanel"';
$Selector->SelectedValue = $GlobalsForm->ConfigurationManager->GetSetting('PANEL_PRIVATE_COUNT');
echo $Selector->Get().'
 </li>
 <li>
	<label for="ddMaxBrowsingHistoryInPanel">'.$GlobalsForm->Context->GetDefinition('MaxBrowsingHistoryInPanel').'</label>
	';
$Selector->Name = 'PANEL_HISTORY_COUNT';
$Selector->Attributes = ' id="ddMaxBrowsingHistoryInPanel"';
$Selector->SelectedValue = $GlobalsForm->ConfigurationManager->GetSetting('PANEL_HISTORY_COUNT');
echo $Selector->Get().'
 </li>
 <li>
	<label for="ddMaxDiscussionsInPanel">'.$GlobalsForm->Context->GetDefinition('MaxDiscussionsInPanel').'</label>
	';
$Selector->Name = 'PANEL_USER_DISCUSSIONS_COUNT';
$Selector->Attributes = ' id="ddMaxDiscussionsInPanel"';
$Selector->SelectedValue = $GlobalsForm->ConfigurationManager->GetSetting('PANEL_USER_DISCUSSIONS_COUNT');
echo $Selector->Get().'
 </li>';
