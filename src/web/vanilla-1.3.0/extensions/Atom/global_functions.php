<?php
function GetFeedUriForAtom(&$Configuration, $Parameters) {
   if ($Configuration['URL_BUILDING_METHOD'] == 'mod_rewrite') $Parameters->Remove('DiscussionID');
   $Uri = FormatStringForDisplay($_SERVER['REQUEST_URI']);
   $Uri = explode('?', $Uri);
   $Uri = $Uri[0];
   return $Uri.'?'.$Parameters->GetQueryString();   
}

?>