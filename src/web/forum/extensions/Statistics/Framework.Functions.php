<?php

function GetBasicCheckRadio($Name, $Value = 1, $Checked, $Attributes = '') {
	return '<input type="radio" name="'.$Name.'" value="'.$Value.'"'.(($Checked == 1)?' checked="checked"':'').$Attributes.' />';
}

function GetDynamicRadio($Name, $Value = 0, $Checked, $OnClick, $Text, $Attributes = '', $RadioID = '') {
	if ($RadioID == '') $RadioID = $Name.'ID';
	$Attributes .= ' id="'.$RadioID.'"';
	if ($OnClick != '') $Attributes .= ' onclick="'.$OnClick.'"';
   return '<label for="'.$RadioID.'">'.GetBasicCheckRadio($Name, $Value, $Checked, $Attributes).' '.$Text.'</label>';
}

?>