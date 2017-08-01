<?php if (!defined('APPLICATION')) exit();
echo $this->Form->Open();
echo $this->Form->Errors();
echo "<h1>".'SidePanelLinks '.t('Settings').'</h1>'; 
echo '<b>'.t('Specify which Vanilla controllers should include the side panel'.'</b>'.'(comma separated list of valid controllers).').'<br>';
echo '<b>'.t('Examples:').'</b>'.'discussioncontroller,discussionscontroller,categoriescontroller '.'<br>';
echo $this->Form->TextBox('Plugins.SidePanelLinks.Controllers', array('class'=>'NameInput','size'=>"100"));
echo '<br>';
//
echo '<b>'.t('Optionally specify a title for the links side panel:&nbsp;').'</b>';
echo $this->Form->TextBox('Plugins.SidePanelLinks.Paneltitle', array('class'=>'NameInput','size'=>"30"));
echo '<br>';
//
echo '<b>'.t('List below the parameters for the side menu links. See the readme file for detailed explanation.').'</b><br>';
?>
<table>
    <thead>
        <tr>
            <th class="Alt"><?php	echo t('Active'); ?></th>
			<th class="Alt"><?php	echo t('Required Role Permission<br(Optional)'); ?></th>
			<th class="Alt"><?php	echo t('Text of link'); ?></th>
			<th class="Alt"><?php	echo t('Link'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
           <td >
           </td>
        </tr>    
<?php 
	  for ($x = 1; $x <= 20; $x++) {
		  echo '<tr><td class="ExtractSetting1">'.$x.'&nbsp;'; 
		  echo $this->Form->CheckBox('Plugins.SidePanelLinks.Active'.$x, array('class'=>'Checkbox','size'=>"1"));
		  echo '</td><td>';
		  echo $this->Form->TextBox('Plugins.SidePanelLinks.Permission'.$x, array('class'=>'NameInput','size'=>"40"));
		  echo '</td><td>';
		  echo $this->Form->TextBox('Plugins.SidePanelLinks.Title'.$x, array('class'=>'NameInput','size'=>"19"));
		  echo '</td><td>';
		  echo $this->Form->TextBox('Plugins.SidePanelLinks.Url'.$x, array('class'=>'NameInput','size'=>"60"));
		  echo '</td></tr>';
		  ////////////////
	  }
	  echo '</tbody></table><br>';
?>
<?php echo $this->Form->Close('Save');?>