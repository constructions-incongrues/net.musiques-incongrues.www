<?php
// Note: This file is included from the library/Framework/Framework.Control.NoticeCollector.php class.

$notice_class = 'Notice';
$NoticeCount = count($this->Notices);
if ($NoticeCount > 0) {
   echo '<div id="NoticeCollector" class="'.$this->CssClass.'">';
   for ($i = 0; $i < $NoticeCount; $i++)
   {
      if (strpos($this->Notices[$i], "<!-- dhr:alaune -->") !== false) { $notice_class = 'alaune'; }
      else { $notice_class = 'Notice'; }
      echo sprintf('<div class="%s">', $notice_class)
         .$this->Notices[$i];
      echo '</div>';
   }
   echo '</div>';
}
?>
