<?php
/*
Extension Name: CiDiscussionMetadata_Release
Extension Url: http://www.github.com/constructions-incongrues/TODO
Description: Strongly typed discussions : Releases
Version: 0.1
Author: Constructions Incongrues
Author Url: http://www.constructions-incongrues.net
*/

// Database fields
// TODO : migrations
$DatabaseTables['DiscussionMetadata_Release'] = 'DiscussionMetadata_Release';
$DatabaseColumns['DiscussionMetadata_Release']['DiscussionID'] = 'DiscussionID';
$DatabaseColumns['DiscussionMetadata_Release']['Label'] = 'Label';

// Declare Twig templates path
$Context->Twig->getLoader()->addPath(__DIR__.'/templates/twig');

// Declare type availability
$Configuration['CiDiscussionMetadata']['Types'][] = 'release';

// Computes data before it is saved to database
function CiDiscussionMetadata_Release_PreSave(DiscussionForm $discussionForm, array $parameters)
{
    $metadata = array(
        'Label' => filter_var($parameters['CiDiscussionMetadata_Release_Label']),
    );

    return $metadata;
}

// Process data before it is passed to template
function CiDiscussionMetadata_Release_PreRender(DiscussionForm $discussionForm, array $request)
{
    $data = array();
    return $data;
}
