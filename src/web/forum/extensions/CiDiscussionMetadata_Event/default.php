<?php
/*
Extension Name: CiDiscussionMetadata_Event
Extension Url: http://www.github.com/constructions-incongrues/TODO
Description: Strongly typed discussions : Events
Version: 0.1
Author: Constructions Incongrues
Author Url: http://www.constructions-incongrues.net
*/

// Database fields
// TODO : migrations
$DatabaseTables['DiscussionMetadata_Event'] = 'DiscussionMetadata_Event';
$DatabaseColumns['DiscussionMetadata_Event']['DiscussionID'] = 'DiscussionID';
$DatabaseColumns['DiscussionMetadata_Event']['StartsOn'] = 'StartsOn';
$DatabaseColumns['DiscussionMetadata_Event']['EndsOn'] = 'EndsOn';
$DatabaseColumns['DiscussionMetadata_Event']['City'] = 'City';
$DatabaseColumns['DiscussionMetadata_Event']['Country'] = 'Country';

// Declare Twig templates path
$Context->Twig->getLoader()->addPath(__DIR__.'/templates/twig');

// Declare type availability
$Configuration['CiDiscussionMetadata']['Types'][] = 'event';

// Computes data before it is saved to database
function CiDiscussionMetadata_Event_PreSave(DiscussionForm $discussionForm, array $parameters)
{
    $metadata = array(
        'StartsOn' => filter_var($parameters['CiDiscussionMetadata_Event_StartsOn']),
        'EndsOn'   => filter_var($parameters['CiDiscussionMetadata_Event_EndsOn']),
        'City'     => filter_var($parameters['CiDiscussionMetadata_Event_City']),
        'Country'  => filter_var($parameters['CiDiscussionMetadata_Event_Country'])
    );

    return $metadata;
}

// Process data before it is passed to template
function CiDiscussionMetadata_Event_PreRender(DiscussionForm $discussionForm, array $request)
{
    $data = array();
    return $data;
}
