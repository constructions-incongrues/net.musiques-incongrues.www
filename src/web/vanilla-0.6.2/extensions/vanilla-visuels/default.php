<?php
/*
Extension Name: Vanilla Visuels 
Extension Url: http://vanilla-visuels
Description: 100% codé avec les pieds !
Version: 0.1
Author: Tristan Rivoallan <tristan@rivoallan.net>
Author Url: http://blogmarks.net/user/mbertier/marks
*/


error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL);

if (!($Context->SelfUrl == 'post.php' || $Context->SelfUrl == 'index.php' || $Context->SelfUrl == 'comments.php' || $Context->SelfUrl == 'extension.php' || $Context->SelfUrl == 'categories.php' || $Context->SelfUrl == 'search.php'))
{
  return;  
}

/*
// Limit access to thoses uids
$uid = $Context->Session->UserID;
if (!($uid == 1 || $uid == 2 || $uid == 47))
{
  return;
}
*/

// Add "events" tab
$Menu->addTab($Context->getDefinition('Œil'),
              $Context->getDefinition('Œil'),
	      $Configuration['BASE_URL'] . 'oeil/',
	      'class="Eyes"');

?>
