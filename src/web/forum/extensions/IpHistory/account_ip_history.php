<?php
// Note: This file is included from the extensions/IpHistory.php file in the IpHistory control.

echo('<h2>'.$this->Context->GetDefinition('IpHistory').'</h2>
   <ul>');
   // Loop through the user's ip history
   $SharedCount = 0;
   $HistoryCount = count($this->History);
   if ($HistoryCount > 0) {
      $i = 0;
      for ($i = 0; $i < $HistoryCount; $i++) {
         $SharedCount = count($this->History[$i]['SharedWith']);
         echo('<li>
            <h3>
               '.$this->History[$i]['IP'].'
               <small>('.
               str_replace('//1',
                  $this->History[$i]['UsageCount'],
                  $this->Context->GetDefinition(FormatPlural($this->History[$i]['UsageCount'],
                     'XTime',
                     'XTimes')
                  )
               )
               .')</small>
            </h3>');
            if ($SharedCount > 0) {
               echo('<p class="Info">
                  '.$this->Context->GetDefinition('IpAlsoUsedBy').'
               </p>
               <p class="Note">');
                  for ($j = 0; $j < $SharedCount; $j++) {
                     $SharedUserName = $this->History[$i]['SharedWith'][$j]['Name'];
                     $SharedUserID = $this->History[$i]['SharedWith'][$j]['UserID'];
                     if ($j > 0) echo(', ');
                     echo('<a href="'.GetUrl($this->Context->Configuration, 'account.php', '', 'u', $SharedUserID).'">'.$SharedUserName.'</a>');
                  }
                  echo('</p>');
            } else {
               echo('<p class="Info">
                  '.$this->Context->GetDefinition('IpNotShared').'
               </p>');
            }
         echo('</li>');
      }
   } else {
      echo('<p>'.$this->Context->GetDefinition('NoIps').'</p>');
   }
echo('</ul>');
?>