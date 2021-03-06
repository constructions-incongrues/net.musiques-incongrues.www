<?php
/*
Extension Name: constructions-incongrues.net/vanilla-ext-fields
Extension Url: https://github.com/constructions-incongrues/net.musiques-incongrues.www/tree/master/src/web/forum/extensions/constructions-incongrues/vanilla-ext-fields
Description:
Version:
Author: Constructions Incongrues
Author Url: http://www.constructions-incongrues.net
*/

use Symfony\Component\Form\Forms;

require_once(__DIR__.'/vendor/autoload.php');

$Context->AddToDelegate('Discussion', 'PostDiscussionPrefix', 'VanillaExtFields_PostDiscussionPrefix');

function VanillaExtFields_PostDiscussionPrefix(&$Discussion)
{
    $types = [
        'agenda' => '<a href="/forum/events" title="Consulter l\'agenda du forum">agenda</a>',
        'musique' => '<a href="/forum/releases" title="Parcourir la discothèque du forum">musique</a>',
        'misc'    => 'misc'
    ];
    $prefix = 'misc';
    foreach (array_keys($types) as $type) {
        $function = sprintf('_is%s', ucfirst($type));
        if (is_callable($function) && call_user_func($function, $Discussion)) {
            $prefix = $type;
            break;
        }
    }
    if (!empty($prefix)) {
        $Discussion->DelegateParameters['Prefix'] = $types[$prefix] . ' ⋅ ';
    }
}

function _isAgenda($Discussion)
{
    if ($Discussion->DiscussionID) {
        // Build selection query
        $sql = $Discussion->Context->ObjectFactory->NewContextObject($Discussion->Context, 'SqlBuilder');
        $sql->SetMainTable('Event','e');
        $sql->addSelect('DiscussionID', 'e');
        $sql->addWhere('e', 'DiscussionID', '', $Discussion->DiscussionID, '=');

        // Execute query
        $db = $Discussion->Context->Database;
        $rs = $db->Execute($sql->GetSelect(), $Discussion, __FUNCTION__, 'Failed to fetch event from database.');
        return $db->RowCount($rs) > 0;
    }
}

function _isMusique($Discussion)
{
    if ($Discussion->DiscussionID) {
        // Build selection query
        $sql = $Discussion->Context->ObjectFactory->NewContextObject($Discussion->Context, 'SqlBuilder');
        $sql->SetMainTable('Releases','r');
        $sql->addSelect('DiscussionID', 'r');
        $sql->addWhere('r', 'DiscussionID', '', $Discussion->DiscussionID, '=');

        // Execute query
        $db = $Discussion->Context->Database;
        $rs = $db->Execute($sql->GetSelect(), $Discussion, __FUNCTION__, 'Failed to fetch release from database.');
        return $db->RowCount($rs) > 0;
    }
}
