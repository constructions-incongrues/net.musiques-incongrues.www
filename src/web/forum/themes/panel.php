<?php
// Note: This file is included from the library/Framework/Framework.Control.Panel.php class.

echo '<div id="Panel">';

// Add the start button to the panel
if (true || $this->Context->Session->UserID > 0 && $this->Context->Session->User->Permission('PERMISSION_START_DISCUSSION')) {
   $CategoryID = ForceIncomingInt('CategoryID', 0);
	if ($CategoryID == 0)
	{
	    $CategoryID = '';
	}
	$tpl_button = '<h1><a href="%s" title="">%s</a></h1>';
	echo sprintf(
	    $tpl_button,
	    GetUrl($this->Context->Configuration, 'post.php', 'category/', 'CategoryID', $CategoryID),
	    'Commencer une discussion');
	echo sprintf(
	    $tpl_button,
	    GetUrl($this->Context->Configuration, 'post.php').'?is_event=true&CategoryID=5',
	    'Annoncer un événement');
	echo sprintf(
	    $tpl_button,
	    GetUrl($this->Context->Configuration, 'post.php').'?is_release=true',
	    'Proposer une release');
	echo sprintf(
	    $tpl_button,
	    'http://musiquesincongrues.uservoice.com',
	    'Suggérer une idée');
	    
}

$this->CallDelegate('PostStartButtonRender');

while (list($Key, $PanelElement) = each($this->PanelElements)) {
   $Type = $PanelElement['Type'];
   $Key = $PanelElement['Key'];
   if ($Type == 'List') {
      $sReturn = '';
      $Links = $this->Lists[$Key];
      if (count($Links) > 0) {
         ksort($Links);
         $sReturn .= '<ul>
            <li>
               <h2>'.$Key.'</h2>
               <ul>';
               while (list($LinkKey, $Link) = each($Links)) {
                  $sReturn .= '<li>
                     <a '.($Link['Link'] != '' ? 'href="'.$Link['Link'].'"' : '').' '.$Link['LinkAttributes'].'>'
                        .$Link['Item'];
                        if ($Link['Suffix'] != '') $sReturn .= ' <span>'.$this->Context->GetDefinition($Link['Suffix']).'</span>';
                     $sReturn .= '</a>';
                  $sReturn .= '</li>';
               }
               $sReturn .= '</ul>
            </li>
         </ul>';
      }
      echo $sReturn;
   } elseif ($Type == 'String') {
      echo $this->Strings[$Key];
   }
}

$this->CallDelegate('PostElementsRender');

echo '</div>
<div id="Content">';
?>