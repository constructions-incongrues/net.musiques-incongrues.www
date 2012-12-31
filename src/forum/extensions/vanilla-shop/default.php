<?php
/*
Extension Name: Vanilla Shop 
Extension Url: http://vanilla-shop
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

require dirname(__FILE__).'/lib/Vanilla/CI/aExtension.php';

class ShopPage extends Vanilla_CI_aExtension
{
  function getCatalogItems()
  {
    $items = array();
    
    // Build selection query
    $sql = $this->context->ObjectFactory->NewContextObject($this->context, 'SqlBuilder');
    $sql->SetMainTable('CatalogItems','c');
    $sql->AddJoin('Discussion', 'd', 'DiscussionID', 'c', 'DiscussionID', 'INNER JOIN');
    $sql->addSelect('DiscussionID', 'c');
    $sql->addSelect('ImageUrl', 'c');
    $sql->addSelect('Name', 'd');
    $sql->addWhere('d', 'Active', '', '1', '=');
    $sql->AddOrderBy('DateCreated', 'd', 'desc');
    
    // Execute query
    $db = $this->context->Database;
    $rs = $db->Execute($sql->GetSelect(), $this, __FUNCTION__, 'Failed to fetch catalog items from database.');

    // Gather and return events
    if ($db->RowCount($rs) > 0)
    {      
      // Hydrate items
      while($db_item = $db->GetRow($rs))
      {
	$db_item['url'] = GetUrl(
		$this->context->Configuration, 
		'comments.php', 
		'', 
		'DiscussionID', 
		$db_item['DiscussionID'], 
		'', 
		'#Item_1', 
		CleanupString($db_item['Name']).'/'
	);
        $items[] = $db_item;
      }
    }
    
    return $items;
  }
  

	function render()
	{
		// Fetch and render template
		echo $this->renderTemplate('items_list', array('items' => $this->getCatalogItems()), dirname(__FILE__).'/templates');
 	}
}

if (in_array($Context->SelfUrl, array('post.php', 'index.php', 'comments.php', 'extension.php', 'categories.php', 'search.php')))
{
	if (in_array($Context->Session->UserID, array(1, 2, 47)))
	{
		main($Context, $Configuration, $Page, $Menu);
	}
}

function main(Context $context, array $configuration, Page $page, Menu $menu)
{
	// Locale setup
	setlocale(LC_ALL, 'fr_FR.UTF-8');

	$extension = new ShopPage($context);
//	$extension->setupDelegates(array('DiscussionForm' => array('DiscussionForm_PreCommentRender', 'PostSaveDiscussion')));

	// Code needed to display the "shops" page
	if(in_array(ForceIncomingString('PostBackAction', ''), array('Shop')))
	{
		$context->PageTitle = $context->GetDefinition('Shop');
		$menu->CurrentTab = 'Shop';
		$page->AddRenderControl($extension, $configuration["CONTROL_POSITION_BODY_ITEM"]);
	}
}

return;

// Add shop related form controls
if ($Context->SelfUrl == 'post.php')
{
  $Context->AddToDelegate("DiscussionForm", "DiscussionForm_PreCommentRender", 'VanillaShop');
  $Context->AddToDelegate("DiscussionForm","PostSaveDiscussion","VanillaShop_ProcessShop");
}



/**
 * Displays additional shop controls within discussion form.
 */
function VanillaShop_MetadataControls(&$DiscussionForm)
{
  $html = '
      <li>
        <label for="VanillaShop_issellable" style="display: inline;">%s</label>
        <input type="checkbox" class="check_sellable" name="VanillaShop_issellable"  onclick="jQuery(\'#VanillaShop_fieldset\').toggle();" %s %s "/>
	</li>
	<fieldset id="VanillaShop_fieldset" style="display:%s;">
        <li>
	<label for="VanillaShop_imageurl">Image</label>
	<input type="text" name="VanillaShop_imageurl" size="60" value="%s" />
        </li>
	</fieldset>
      %s
  ';
  // Today's date - formatted for display
  $fmt_today = date('d/m/Y');

  // Default form values
  $form_issellable = ForceIncomingString('VanillaShop_issellable', true) === 'on' ? 'checked' : '';
  $form_disable = '';
  $form_hidden_issellable = '';
  $fieldset_display = ForceIncomingString('VanillaShop_issellable', true) === 'on' ? 'block' : 'none';
  $image_url = '';

  if (isset($_GET['is_sellable']) && $_GET['is_sellable'] == 'true')
  {
    $form_issellable = 'checked';
  }

  // Check if an item is already related to this discussion
  if ($DiscussionForm->Discussion->DiscussionID)
  {
    // Build selection query
    $sql = $DiscussionForm->Context->ObjectFactory->NewContextObject($DiscussionForm->Context, 'SqlBuilder');
    $sql->SetMainTable('CatalogItems','c');
    $sql->addSelect('DiscussionID', 'c');
    $sql->addSelect('ImageUrl', 'i');
    $sql->addWhere('c', 'DiscussionID', '', $DiscussionForm->Discussion->DiscussionID, '=');

    // Execute query
    $db = $DiscussionForm->Context->Database;
    $rs = $db->Execute($sql->GetSelect(), $DiscussionForm, __FUNCTION__, 'Failed to fetch catalog item from database.');
    if ($db->RowCount($rs) > 0)
    {      
      $db_item = $db->GetRow($rs);
      $form_disable = 'disabled';
      $form_issellable = 'checked';
      $fieldset_display = 'block';
      $form_hidden_issellable = '<input type="hidden" name="VanillaShop_issellable" value="on" />';
      $image_url = $db_item['ImageUrl'];
    }
  }

  
  // Template population and rendering
  echo sprintf($html,
               $DiscussionForm->Context->getDefinition("C'est à vendre ?"),
               FormatStringForDisplay($form_issellable), $form_disable,
  	       $fieldset_display,
		$image_url,
               $form_hidden_issellable
	       );

}

/**
 * Triggers event creation / update if user requested it.
 */
function VanillaShop_ProcessShop(&$DiscussionForm)
{
  // Gather data
  if(ForceIncomingBool('VanillaShop_issellable', true) === true)
  {
    // Save event to backend
    VanillaShop_SaveCatalogItem($DiscussionForm, ForceIncomingString('VanillaShop_imageurl', ''));
  }
}

/**
 * Validates and save data to backend.
 */
function VanillaShop_SaveCatalogItem(&$DiscussionForm, $image_url)
{
  // Save event to backend
  // Only create / update event if discussion has been saved to database
  if ($DiscussionForm->DelegateParameters['ResultDiscussion']->DiscussionID)
  {
    // REPLACE event into database
    $sql = $DiscussionForm->Context->ObjectFactory->NewContextObject($DiscussionForm->Context, 'SqlBuilder');
    $sql->SetMainTable('CatalogItems','c');
    $sql->AddFieldNameValue('DiscussionID', mysql_real_escape_string($DiscussionForm->DelegateParameters['ResultDiscussion']->DiscussionID));
    $sql->AddFieldNameValue('ImageUrl', mysql_real_escape_string($image_url));
    $sql_query = str_replace('UPDATE', 'REPLACE', $sql->GetUpdate()); // SqlBuilder does not have a GetReplace method
    $DiscussionForm->Context->Database->Execute($sql_query, $DiscussionForm, __FUNCTION__, 'Failed to save catalog item into database.');
  }
}
