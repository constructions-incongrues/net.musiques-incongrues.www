<?php
/*
Extension Name: Vanilla À la une
Extension Url: http://vanilla-alaune
Description: 100% codé avec les pieds !
Version: 0.1
Author: Tristan Rivoallan <tristan@rivoallan.net>
Author Url: http://blogmarks.net/user/mbertier/marks
*/

/*
 * TODO : warnings are not shown to user if only event controls have errors.
 * TODO : it could be more clever to subclass the Discussion / DiscussionForm classes (?)
 * TODO : cleanup events rendering code
 */

error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL);

if (!($Context->SelfUrl == 'post.php' || $Context->SelfUrl == 'index.php' || $Context->SelfUrl == 'comments.php' || $Context->SelfUrl == 'extension.php' || $Context->SelfUrl == 'categories.php' || $Context->SelfUrl == 'search.php'))
{
  return;
}

/*
// Limit access to thoses uids
$uid = $Context->Session->UserID;
if (!($uid == 1 || $uid == 2 || $uid == 47))
{
  return;
}
*/

$uid = $Context->Session->UserID;
if (in_array($Context->SelfUrl, array("index.php")) && strtolower(ForceIncomingString('Page', '')) != 'dons' && strtolower(ForceIncomingString('Page', '')) != 'faq')
{
   $sticky_discussions = DiscussionsPeer::getStickyDiscussions($Context);
   if ($sticky_discussions)
   {
     $discussion_tpl = '<li class="%s"><a href="%s">%s</a></li>';
     $discussions_strings = array();
     $i = 0;
     $modulo_class = 'odd';
     foreach ($sticky_discussions as $discussion)
     {
       $discussions_strings[] = sprintf($discussion_tpl,
         $modulo_class,
         GetUrl($Context->Configuration, 'comments.php', '', 'DiscussionID', $discussion['DiscussionID'], '', '#Item_1', CleanupString($discussion['Name'].'/')),
	 $discussion['Name']);
       $i++;
       if ($i % 2 === 0)
       {
         $modulo_class = 'odd';
       }
       else
       {
         $modulo_class = 'even';
       }
     }
     $notice = sprintf("
     <!-- dhr:alaune -->
     <h2 style='display:inline;' class='surtout'>Et surtout : </h2><br />
     <ul class='square'>
     %s
     </ul>
     ", implode('', $discussions_strings));
     $NoticeCollector->AddNotice($notice);
   }
}

class DiscussionsPeer
{
  public function getStickyDiscussions($context)
  {
    $discussions = array();

    // Build selection query
    $sql = $context->ObjectFactory->NewContextObject($context, 'SqlBuilder');
    $sql->SetMainTable('Discussion','d');
    $sql->addSelect('DiscussionID', 'd');
    $sql->addSelect('Name', 'd');
    $sql->addWhere('d', 'Sticky', '', '1', '=');
    $sql->AddOrderBy('Name', 'd', 'asc');

    // Execute query
    $db = $context->Database;
    $rs = $db->Execute($sql->GetSelect(), __CLASS__, __FUNCTION__, 'Failed to fetch events from database.');

    // Gather and return events
    if ($db->RowCount($rs) > 0)
    {
      while($db_discussion = $db->GetRow($rs))
      {
        $discussions[] = $db_discussion;
      }
    }

    return $discussions;
  }
}
?>
