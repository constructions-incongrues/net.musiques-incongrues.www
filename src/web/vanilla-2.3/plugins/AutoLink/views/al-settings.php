<?php if (!defined('APPLICATION')) exit();
echo $this->Form->Open();
echo $this->Form->Errors();
?>


<h1><?php echo Gdn::Translate('Auto Link | by: Peregrine - see donation button below'); ?></h1>

<div class="Info"><?php echo Gdn::Translate('AutoLink Options.'); ?></div>

<table class="AltRows">
    <thead>
        <tr>
            <th><?php echo Gdn::Translate('Option'); ?></th>
            <th class="Alt"><?php echo Gdn::Translate('Description'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <?php
                  echo $this->Form->CheckBox(
                    'Plugins.AutoLink.Precedence', 'Precedence',
                    array('value' => '1', 'selected' => 'selected')
                );
                ?>
            </td>
            <td class="Alt">
               <?php echo Gdn::Translate('Links override Tags if both tag and designated link occur.'); ?>
            </td>
        </tr>
        
       
        <tr>
            <td>
           
             <?php
             echo $this->Form->CheckBox(
                    'Plugins.AutoLink.Links', 'Links',
                    array('value' => '1', 'selected' => 'selected')
                );
                ?>
            </td>
            <td class="Alt">
               <?php echo Gdn::Translate('Enable autolinks of specified words'); ?>
            </td>
        </tr>
       
       
      <tr>
            <td>
                <?php
                echo $this->Form->CheckBox(
                    'Plugins.AutoLink.Tags', 'Tags',
                    array('value' => '1', 'selected' => 'selected')
                );
                ?>
            </td>
            <td class="Alt">
               <?php echo Gdn::Translate('Enable autotags from tag plugin (If you check this option - you must enable the tagging plugin)'); ?>
            </td>
        </tr>
  </tbody>
</table>

<?php echo $this->Form->Close('Save');?>
