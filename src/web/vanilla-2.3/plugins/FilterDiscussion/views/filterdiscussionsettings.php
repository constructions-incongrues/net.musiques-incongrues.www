<?php if (!defined('APPLICATION')) exit();
echo $this->Form->Open();
echo $this->Form->Errors();
echo "<h1>".'DiscussionFilter '.t('Settings').'</h1><br>'; 
echo '<b>'.t('List the allowed Discussion table field names (column names):').'</b><br>';
echo $this->Form->TextBox('Plugins.FilterDiscussion.Fieldnames', array('class'=>'NameInput','size'=>"120"));
echo '<br>'.t('(Use comma sepators between field names. Case Sensitive! Ensure you are accurate!)').'<br>';
echo '<b>'.t('List the url parameters you want ignored (This is to accommodate other plugins that read url parameters):').'</b><br>';
echo $this->Form->TextBox('Plugins.FilterDiscussion.Ignoreparms', array('class'=>'NameInput','size'=>"120"));
echo '<br>'.t('Use comma sepators between ignorable parameter names').'<br>';
echo '<br><b>'.t('Specify saved filters').'</b><br>';
echo t("Specify a name and it's associated saved filter parameters. This allows use of &!filter=saved-name in lieu of long url parameter lists").'<br>';
?>
<table>
    <thead>
        <tr>
            <th><?php echo Gdn::Translate('Saved Filter Name'); ?></th>
            <th class="Alt"><?php echo Gdn::Translate('Filter Parameters'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
             <td >
            </td>
        </tr>    
<?php 
	  echo "<tr><td>1-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName1', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td >' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilter1', array('class'=>'NameInput','size'=>"120"));
      echo '</td></tr>';
	  
	  echo "<tr><td>2-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName2', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td class="Alt">' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilter2', array('class'=>'NameInput','size'=>"120"));
      echo '</td></tr>';
	  
	  echo "<tr><td>3-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName3', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td class="Alt">' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilter3', array('class'=>'NameInput','size'=>"120"));
      echo '</td></tr>';
	  
	  echo "<tr><td>4-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName4', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td class="Alt">' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilter4', array('class'=>'NameInput','size'=>"120"));
      echo '</td></tr>';
	  
	  echo "<tr><td>5-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName5', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td class="Alt">' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilter5', array('class'=>'NameInput','size'=>"120"));
      echo '</td></tr>';
	  
	  echo "<tr><td>6-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName6', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td class="Alt">' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilter6', array('class'=>'NameInput','size'=>"120"));
      echo '</td></tr>';
	  
	  echo "<tr><td>7-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName7', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td class="Alt">' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilter7', array('class'=>'NameInput','size'=>"120"));
      echo '</td></tr>';
	  
	  echo "<tr><td>8-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName8', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td class="Alt">' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilter8', array('class'=>'NameInput','size'=>"120"));
      echo '</td></tr>';
	  
	  echo '</tbody> ';
?>
</table><br>'
<?php echo $this->Form->Close('Save');?>