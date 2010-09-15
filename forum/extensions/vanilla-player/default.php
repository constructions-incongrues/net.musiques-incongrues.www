<?php
/*
Extension Name: Vanilla Player 
Extension Url: http://vanilla-player
Description: 100% codé avec les pieds !
Version: 0.1
Author: Tristan Rivoallan <tristan@rivoallan.net>
Author Url: http://blogmarks.net/user/mbertier/marks
*/
if(!in_array(ForceIncomingString("PostBackAction", ""), array('Releases')))
{
  $Head->AddScript('extensions/vanilla-player/js/inlineplayer.js?v=1');
}
