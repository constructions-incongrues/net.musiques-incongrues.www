<?php if (!defined('APPLICATION')) exit();
echo $this->Form->Open();
echo $this->Form->Errors();
echo "<h1>".'DiscussionFilter '.t('Settings').'</h1>'; 
echo t('List the allowed Discussion table field names (column names)');
echo $this->Form->TextBox('Plugins.FilterDiscussion.Fieldnames', array('class'=>'NameInput','size'=>"80"));
echo t('(Use comma sepators between field names. Case Sensitive! Ensure you are accurate!)').'<br>';
echo t('List the url parameters you want ignored. This is to accommodate other plugins that read url parameters.');
echo $this->Form->TextBox('Plugins.FilterDiscussion.Ignoreparms', array('class'=>'NameInput','size'=>"80"));
echo t('Use comma sepators between ignorable parameter names').'<br>';
echo '<br>'.t('Specify saved filters').'<br>';
echo t("Specify a name and it's associated saved filter parameters. This allows use of &!filter=saved-name in lieu of a long url parameter list");
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
             <td class="Alt">
            </td>
        </tr>    
<?php 
	  echo "<tr><td>1-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName1', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td class="Alt">' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilters1', array('class'=>'LinkInput','size'=>"80"));
      echo '</td></tr>';
	  
	  echo "<tr><td>1-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName1', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td class="Alt">' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilters1', array('class'=>'LinkInput','size'=>"80"));
      echo '</td></tr>';
	  
	  echo "<tr><td>1-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName1', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td class="Alt">' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilters1', array('class'=>'LinkInput','size'=>"80"));
      echo '</td></tr>';
	  
	  echo "<tr><td>1-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName1', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td class="Alt">' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilters1', array('class'=>'LinkInput','size'=>"80"));
      echo '</td></tr>';
	  
	  echo "<tr><td>1-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName1', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td class="Alt">' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilters1', array('class'=>'LinkInput','size'=>"80"));
      echo '</td></tr>';
	  
	  echo "<tr><td>1-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName1', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td class="Alt">' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilters1', array('class'=>'LinkInput','size'=>"80"));
      echo '</td></tr>';
	  
	  echo "<tr><td>1-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName1', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td class="Alt">' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilters1', array('class'=>'LinkInput','size'=>"80"));
      echo '</td></tr>';
	  
	  echo "<tr><td>1-"; 
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedName1', array('class'=>'NameInput','size'=>"10"));
	  echo '</td><td class="Alt">' ;
      echo $this->Form->TextBox('Plugins.FilterDiscussion.SavedFilters1', array('class'=>'LinkInput','size'=>"80"));
      echo '</td></tr>';
	  
	  echo '</tbody> </table><br>'
$this->Form->Close('Save');
?>