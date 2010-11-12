<?php
/*
 Extension Name: Le magasin des Musiques Incongrues
 Extension Url: http://github.com/constructions-incongrues/musiques-incongrues.net
 Description: Des métadonnées supplémentaires pour les topics.
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/constructions-incongrues
 */

// Let's get serious
error_reporting(E_ALL & ~E_NOTICE);

// Add related form controls
if ($Context->SelfUrl == 'post.php')
{
    $Context->AddToDelegate("DiscussionForm", "DiscussionForm_PreCommentRender", 'VanillaShop_MetadataControls');
    $Context->AddToDelegate("DiscussionForm","PostSaveDiscussion","VanillaShop_ProcessSellable");
}

function VanillaShop_MetadataControls($DiscussionForm)
{
    $html = '
      <li id="button-shop">
        <label for="VanillaShop_issellable" style="display: inline;">C\'est à vendre ?</label>
        <input type="checkbox" class="check_sellable" name="VanillaShop_issellable"  onclick="jQuery(\'#VanillaShop_fieldset\').toggle();" %s %s "/>
	  </li>
	  <fieldset id="VanillaShop_fieldset" style="display:%s;">
        <li>
        	<label for="VanillaShop_price">À quel prix ? (en euros !)</label>
        	<input type="text" name="VanillaShop_price" size="3" value="%s" class="shop-input-url" />
        </li>
	  </fieldset>
      %s
      %s
  ';

    // Compute form elements state
    $form_issellable = '';
    $form_disable = '';
    $fieldset_visibility = ForceIncomingString('VanillaShop_issellable', true) === 'on' ? 'block' : 'none';
    if (isset($_GET['is_sellable']) && $_GET['is_sellable'] == 'true')
    {
        $form_issellable = 'checked';
        $fieldset_visibility = 'visible';
    }

    // Check if a release is already related to this discussion
    if ($DiscussionForm->Discussion->DiscussionID)
    {
        // Build selection query
        $sql = $DiscussionForm->Context->ObjectFactory->NewContextObject($DiscussionForm->Context, 'SqlBuilder');
        $sql->SetMainTable('Sellable','r');
        $sql->addSelect('DiscussionID', 'r');
        $sql->addSelect('Price', 'r');
        $sql->addWhere('r', 'DiscussionID', '', $DiscussionForm->Discussion->DiscussionID, '=');

        // Execute query
        $db = $DiscussionForm->Context->Database;
        $rs = $db->Execute($sql->GetSelect(), $DiscussionForm, __FUNCTION__, 'Failed to fetch release from database.');
        if ($db->RowCount($rs) > 0)
        {
            $db_release = $db->GetRow($rs);
            $form_disable = 'disabled';
            $form_issellable = 'checked';
            $fieldset_visibility = 'block';
            $price = $db_release['Price'];
        }
    }


    echo sprintf($html,
    	$form_issellable, $form_disable, $fieldset_visibility, $price, '', ''
    );
}

function VanillaShop_ProcessSellable($DiscussionForm)
{
    // Gather data
    if(ForceIncomingBool('VanillaShop_issellable', true) === true)
    {
        // Save to backend
        VanillaShop_SaveSellable($DiscussionForm, ForceIncomingString('VanillaShop_price', ''));
    }

}

function VanillaShop_SaveSellable($DiscussionForm, $price)
{
    // Save to backend
    // Only create / update if discussion has been saved to database
    if ($DiscussionForm->DelegateParameters['ResultDiscussion']->DiscussionID)
    {
        // REPLACE event into database
        $sql = $DiscussionForm->Context->ObjectFactory->NewContextObject($DiscussionForm->Context, 'SqlBuilder');
        $sql->SetMainTable('Sellable','r');
        $sql->AddFieldNameValue('DiscussionID', mysql_real_escape_string($DiscussionForm->DelegateParameters['ResultDiscussion']->DiscussionID));
        $sql->AddFieldNameValue('Price', mysql_real_escape_string($price));
        $sql_query = str_replace('UPDATE', 'REPLACE', $sql->GetUpdate()); // SqlBuilder does not have a GetReplace method
        $DiscussionForm->Context->Database->Execute($sql_query, $DiscussionForm, __FUNCTION__, 'Failed to save release into database.');
    }
}