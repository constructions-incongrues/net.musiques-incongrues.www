<?php if (!defined('APPLICATION')) exit();

$Positions = $this->Data('Positions');
$Fields = $this->Data('Fields');

echo Wrap(T('Custom Discussion Fields'), 'h1');
echo Wrap(T('DiscussionExtender.Settings.Desc'), 'p', array('class' => 'Wrap'));
echo Wrap(Anchor(T('Add Discussion Field'), '/settings/discussionextender/edit/', 'Popup SmallButton'), 'div', array('class' => 'Wrap')); ?>
<table>
   <thead>
      <tr>
         <th><?php echo T('Label'); ?></th>
         <th><?php echo T('Type'); ?></th>
         <th><?php echo T('Position'); ?></th>
         <th><?php echo T('Required'); ?></th>
         <th><?php echo T('In Meta'); ?></th>
         <th><?php echo T('Column'); ?></th>
         <th><?php echo T('Options'); ?></th>
      </tr>
   </thead>
   <tbody>

<?php
  foreach ($Fields as $Name => $Field) {
    $String  = Wrap($Field['Label'], 'td');
    $String .= Wrap($Field['Type'], 'td');
    $String .= Wrap($Positions[$Field['Position']], 'td');
    $String .= Wrap(($Field['Required']) ? T('Yes') : T('No'), 'td');
    $String .= Wrap(($Field['Display']) ? T('Yes') : T('No'), 'td');
    $String .= Wrap(($Field['Column']) ? T('Yes') : T('No'), 'td');
    $String .= Wrap(Anchor(T('Edit'), '/settings/discussionextender/edit/'.$Name, 'Popup SmallButton') . ' ' . Anchor(T('Delete'), '/settings/discussionextender/delete/'.$Name, 'Popup SmallButton'),'td');
    echo Wrap($String, 'tr');
  }
?>
   </tbody>
</table>
<br />
<br />
<?php
echo Wrap(T('Sample New Discussion'), 'h3');
echo Wrap(T('Sample new discussion preview.'),'div', array('class' => 'Info', 'id' => 'NewDiscussionPreview'));