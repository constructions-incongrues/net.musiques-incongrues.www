<?php if (!defined('APPLICATION')) exit();

echo Wrap($this->Data('Title'), 'h1');

echo $this->Form->Open();
echo $this->Form->Errors();
?>
   <ul>
      <li>
         <?php
         echo $this->Form->Label('Type', 'Type');
         echo $this->Form->Dropdown('Type', $this->Data('FieldTypes'));
         ?>
      </li>
      <li>
         <?php
         echo $this->Form->Label('Position', 'Position');
         echo $this->Form->Dropdown('Position', $this->Data('FieldPositions'));
         ?>
      </li>
      <li>
         <?php
         echo $this->Form->Label('Label', 'Label');
         echo $this->Form->TextBox('Label');
         ?>
      </li>
      <li class="DE_Options">
         <?php
         echo $this->Form->Label('Options', 'Options');
         echo Wrap(T('One option per line'), 'p');
         echo $this->Form->TextBox('Options', array('MultiLine' => TRUE));
         ?>
      </li>
      <li>
         <?php echo $this->Form->CheckBox('Required', 'Required on all discussions'); ?>
      </li>
      <li>
         <?php echo $this->Form->CheckBox('Display', 'Show in discussion meta'); ?>
      </li>
      <li>
         <?php echo $this->Form->CheckBox('Column', 'Add a column to the discussion table for this?'); ?>
      </li>
   </ul>
<?php echo $this->Form->Close('Save');