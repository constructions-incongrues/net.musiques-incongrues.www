<?php
/*
Extension Name: Sidepanel Rotator
Extension Url: http://lussumo.com/addons/
Description: Adds a rotating image to the side panel
Version: 1.0
Author: Justin Haury
Author Url: http://mymindisgoing.com/
*/
ob_implicit_flush(false);
ob_end_clean();
ob_start();
include(dirname(__FILE__).'/rotator.php');
$introspection = ob_get_clean();

if ($Context->SelfUrl == "index.php" ) {
	$Head->AddStyleSheet('extensions/SidepanelRotator/style.css');
		$Panel->AddString('<h2>Radio</h2>');
		$Panel->AddString("<a href=\"/forum/radio-random.php\" onclick=\"window.open(this.href, 'Substantifique Mo&euml;lle Incongrue et Inodore', 'height=700, width=340, top=100, left=100, toolbar=no, menubar=no, location=no, resizable=yes, scrollbars=no, status=no'); return false;\"><br /><img src=\"/forum/uploads/radio.png\" alt=\"RADIO\" style=\"color:#666;text-align:center;\" border=\"0px \"/></a>");
	$Panel->AddString($introspection, 80);
		

/* Groscast extensions */
		
}
elseif ($Context->SelfUrl == "categories.php" ) {
	$Head->AddStyleSheet('extensions/SidepanelRotator/style.css');
	$Panel->AddString($introspection, 2);
		
		/* Groscast extensions */
        
	
		$Panel->AddString('<h2>Radio</h2>');
		$Panel->AddString("<a href=\"/forum/radio-random.php\" onclick=\"window.open(this.href, 'Substantifique Mo&euml;lle Incongrue et Inodore', 'height=620, width=340, top=100, left=100, toolbar=no, menubar=no, location=no, resizable=yes, scrollbars=no, status=no'); return false;\"><br /><img src=\"/forum/uploads/radio.png\" alt=\"RADIO\" style=\"color:#666;text-align:center;\" border=\"0px \"/></a>");
}
elseif ($Context->SelfUrl == "search.php" ) {
	$Head->AddStyleSheet('extensions/SidepanelRotator/style.css');
	$Panel->AddString($introspection, 2);
		
		/* Groscast extensions */
        
		$Panel->AddString('<h2>Radio</h2>');
		$Panel->AddString("<a href=\"/forum/radio-random.php\" onclick=\"window.open(this.href, 'Substantifique Mo&euml;lle Incongrue et Inodore', 'height=620, width=340, top=100, left=100, toolbar=no, menubar=no, location=no, resizable=yes, scrollbars=no, status=no'); return false;\"><br /><img src=\"/forum/uploads/radio.png\" alt=\"RADIO\" style=\"color:#666;text-align:center;\" border=\"0px \"/></a>");
}
elseif ($Context->SelfUrl == "account.php" ) {
	$Head->AddStyleSheet('extensions/SidepanelRotator/style.css');
	$Panel->AddString($introspection, 2);
		
		/* Groscast extensions */
        
		$Panel->AddString('<h2>Radio</h2>');
		$Panel->AddString("<a href=\"/forum/radio-random.php\" onclick=\"window.open(this.href, 'Substantifique Mo&euml;lle Incongrue et Inodore', 'height=620, width=340, top=100, left=100, toolbar=no, menubar=no, location=no, resizable=yes, scrollbars=no, status=no'); return false;\"><br /><img src=\"/forum/uploads/radio.png\" alt=\"RADIO\" style=\"color:#666;text-align:center;\" border=\"0px \"/></a>");
}
elseif ($Context->SelfUrl == "comments.php" ) {
        $Head->AddStyleSheet('extensions/SidepanelRotator/style.css');
		$Panel->AddString('<h2>Radio</h2>');
		$Panel->AddString("<a href=\"/forum/radio-random.php\" onclick=\"window.open(this.href, 'Substantifique Mo&euml;lle Incongrue et Inodore', 'height=620, width=340, top=100, left=100, toolbar=no, menubar=no, location=no, resizable=yes, scrollbars=no, status=no'); return false;\"><br /><img src=\"/forum/uploads/radio.png\" alt=\"RADIO\" style=\"color:#666;text-align:center;\" border=\"0px \"/></a>");
        $Panel->AddString($introspection, 10);
}
elseif ($Context->SelfUrl == "account.php" ) {
        $Head->AddStyleSheet('extensions/SidepanelRotator/style.css');
        $Panel->AddString($introspection, 10);
}
elseif ($Context->SelfUrl == "extension.php" ) {
if ($Context->Session->UserID == 0)
{
$Panel->AddString('<h2>Radio</h2>');
		$Panel->AddString("<a href=\"/forum/radio-random.php\" onclick=\"window.open(this.href, 'Substantifique Mo&euml;lle Incongrue et Inodore', 'height=620, width=340, top=100, left=100, toolbar=no, menubar=no, location=no, resizable=yes, scrollbars=no, status=no'); return false;\"><br /><img src=\"/forum/uploads/radio.png\" alt=\"RADIO\" style=\"color:#666;text-align:center;\" border=\"0px \"/></a>");
        $Head->AddStyleSheet('extensions/SidepanelRotator/style.css');
        $Panel->AddString($introspection, 10);

}}
