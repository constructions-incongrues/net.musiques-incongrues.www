<?php

/**
 * admin actions.
 *
 * @package    musiques-incongrues
 * @subpackage admin
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class adminActions extends sfActions
{
 /**
  * Show admin dashboard
  *
  * @param sfRequest $request A request object
  */
  public function executeDashboard(sfWebRequest $request)
  {
    print_r($_COOKIE);
    print_r($_SESSION);
    var_dump($_SESSION['LussumoUserID']);
  }
}
