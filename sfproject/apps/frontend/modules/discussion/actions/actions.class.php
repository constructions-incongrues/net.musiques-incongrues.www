<?php

/**
 * discussion actions.
 *
 * @package    musiques-incongrues
 * @subpackage discussion
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class discussionActions extends sfActions
{
 /**
  * Displays discussions list.
  *
  * @param sfWebRequest $request A request object
  */
  public function executeList(sfWebRequest $request)
  {
      // Fetch latest discussions
      $discussions = LUM_DiscussionTable::getInstance()->getLatest();

      // Pass data to view
      $this->discussions = $discussions;

      // Select template
      return sfView::SUCCESS;
  }
}
