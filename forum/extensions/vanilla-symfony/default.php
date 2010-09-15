<?php
/*
Extension Name: Vanilla symfony integration
Extension Url: http://github.com/contructions-incongrues/musiques-incongrues.net
Description: Makes it possible to use symfony with a Lussumo Vanilla instance
Version: 0.1
Author: Tristan Rivoallan <tristan@rivoallan.net>
Author Url: http://github.com/trivoallan
*/

if(!in_array(ForceIncomingString("PostBackAction", ""), array('Symfony')))
{
    require dirname(__FILE__).'/../../s/frontend_dev.php';
}