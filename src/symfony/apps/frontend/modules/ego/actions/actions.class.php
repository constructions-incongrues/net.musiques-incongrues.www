<?php

/**
 * ego actions.
 *
 * @package    musiques-incongrues
 * @subpackage ego
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class egoActions extends sfActions
{
 /**
  * @param sfRequest $request A request object
  */
  public function executeList(sfWebRequest $request)
  {
    // Fetch all members
    $q = Doctrine_Query::create()
        ->select('Name, CountComments, Picture, UserID')
        ->from('LUM_User u')
        ->orderBy('datelastactive desc');
     $people = $q->execute(null, Doctrine_Core::HYDRATE_ARRAY);
     $q->free();

     // Pass data to view
     $this->people = $people;
  }
}
