<?php
/*
Extension Name: CiDiscussionMetadata_Sellable
Extension Url: http://www.github.com/constructions-incongrues/TODO
Description: Strongly typed discussions : Sellable
Version: 0.1
Author: Constructions Incongrues
Author Url: http://www.constructions-incongrues.net
*/

// Database fields
// TODO : migrations
$DatabaseTables['DiscussionMetadata_Sellable'] = 'DiscussionMetadata_Sellable';
$DatabaseColumns['DiscussionMetadata_Sellable']['DiscussionID'] = 'DiscussionID';
$DatabaseColumns['DiscussionMetadata_Sellable']['Price'] = 'Price';

// Declare Twig templates path
$Context->Twig->getLoader()->addPath(__DIR__.'/templates/twig');

// Declare type availability
$Configuration['CiDiscussionMetadata']['Types'][] = 'sellable';

// Computes data before it is saved to database
function CiDiscussionMetadata_Sellable_PreSave(DiscussionForm $discussionForm, array $parameters)
{
    $metadata = array(
        'Price' => filter_var($parameters['CiDiscussionMetadata_Sellable_Price']),
    );

    return $metadata;
}

// Process data before it is passed to template
function CiDiscussionMetadata_Sellable_PreRender(DiscussionForm $discussionForm, array $request)
{
    $data = array();
    return $data;
}
