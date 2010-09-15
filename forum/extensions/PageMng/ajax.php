<?php

include('../../appg/settings.php');
include('../../appg/init_ajax.php');

define('PAGEMNG_ISAJAX', 1);
include('default.php');

if($Context->Session->User->Permission('PERMISSION_CHANGE_APPLICATION_SETTINGS'))
	$PageMng->ReorganizeOrder();

?>