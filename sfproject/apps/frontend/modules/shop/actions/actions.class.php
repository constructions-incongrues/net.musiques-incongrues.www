<?php

/**
 * shop actions.
 *
 * @package    musiques-incongrues
 * @subpackage shop
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class shopActions extends sfActions
{
 /**
  * Displays list of buyable items.
  *
  * @param sfWebRequest $request A request object
  */
  public function executeList(sfWebRequest $request)
  {
    // Fetch list of sellable items
    $items = LUM_SellableTable::getInstance()->findAll();

    // Pass data to view
    $this->items = $items;
  }
}
