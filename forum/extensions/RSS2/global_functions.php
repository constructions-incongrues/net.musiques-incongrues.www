<?php
function GetFeedUriForRSS2(&$Configuration, $Parameters) {
   if ($Configuration['URL_BUILDING_METHOD'] == 'mod_rewrite') $Parameters->Remove('DiscussionID');
   $Uri = GetRequestUri();
   $Uri = explode('?', $Uri);
   $Uri = $Uri[0];
   return $Uri.'?'.$Parameters->GetQueryString();   
}

?>