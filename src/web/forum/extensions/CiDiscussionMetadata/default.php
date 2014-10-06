<?php
/*
Extension Name: CiDiscussionMetadata
Extension Url: http://www.github.com/constructions-incongrues/TODO
Description: Strongly typed discussions
Version: 0.1
Author: Constructions Incongrues
Author Url: http://www.constructions-incongrues.net
*/

// ORM
require(__DIR__.'/database.php');

// Configuration
$Configuration['CiDiscussionMetadata']['Types'] = array();

// Dependencies
if (!class_exists('\Discussion')) {
    require($Configuration['LIBRARY_PATH'].'/Vanilla/Vanilla.Class.Discussion.php');
}
if (!class_exists('\DiscussionManager')) {
    require($Configuration['LIBRARY_PATH'].'/Vanilla/Vanilla.Class.DiscussionManager.php');
}

// Override Dicussion classes
require(__DIR__.'/src/ConstructionsIncongrues/Vanilla/DiscussionManager.php');
require(__DIR__.'/src/ConstructionsIncongrues/Vanilla/Discussion.php');
$Context->ObjectFactory->SetReference('DiscussionManager', '\ConstructionsIncongrues\Vanilla\DiscussionManager');
$Context->ObjectFactory->SetReference('Discussion', '\ConstructionsIncongrues\Vanilla\Discussion');

// Additional discussion form controls
function CiDiscussionMetadata_DiscussionForm_PreCommentRender(DiscussionForm $discussionForm)
{
    foreach ($discussionForm->Context->Configuration['CiDiscussionMetadata']['Types'] as $type) {
        $function = sprintf('CiDiscussionMetadata_%s_PreRender', ucfirst($type));
        $data = array();
        if (function_exists($function)) {
            // Execute controller
            $data = call_user_func($function, $discussionForm, $_REQUEST);
        }
        CiTwigRender(sprintf('form_%s.php', $type), $discussionForm, $data);
    }
}

// Calls specific type controllers dans saves data to database
function CiDiscussionMetadata_DiscussionForm_PostSaveDiscussion(DiscussionForm $discussionForm)
{
    if (is_object($discussionForm->DelegateParameters['ResultDiscussion']) &&
        isset($_REQUEST['CiDiscussionMetadata_Type']) &&
        is_array($_REQUEST['CiDiscussionMetadata_Type'])) {
        foreach ($_REQUEST['CiDiscussionMetadata_Type'] as $type) {
            // Create main database record
            $sql = $discussionForm->Context->ObjectFactory->NewContextObject($discussionForm->Context, 'SqlBuilder');
            $sql->SetMainTable('DiscussionMetadata', 'dm');
            $sql->AddFieldNameValue(
                'DiscussionID',
                $discussionForm->DelegateParameters['ResultDiscussion']->DiscussionID
            );
            $sql->AddFieldNameValue('Type', mysql_real_escape_string($type));
            $sqlQuery = str_replace('UPDATE', 'REPLACE', $sql->GetUpdate()); // SqlBuilder does not have a GetReplace method
            $discussionForm->Context->Database->Execute(
                $sqlQuery,
                $discussionForm,
                __FUNCTION__,
                'Failed to save discussion metadata into database.'
            );

            // Execute metadata controller for each selected type
            $function = sprintf('CiDiscussionMetadata_%s_PreSave', ucfirst($type));
            if (function_exists($function)) {
                // Execute controller
                $metadata = call_user_func($function, $discussionForm, $_REQUEST);
                $metadata['DiscussionID'] = $discussionForm->DelegateParameters['ResultDiscussion']->DiscussionID;

                // Make sure we have all required fields
                $columns = array_values(
                    $discussionForm->Context->DatabaseColumns[sprintf('DiscussionMetadata_%s', ucfirst($type))]
                );
                $missing = array_diff($columns, array_keys($metadata));
                if (count($missing)) {
                    throw new \InvalidArgumentException(sprintf('Missing fields : %s', implode(', ', $missing)));
                }

                // Save data to database
                $sql = $discussionForm->Context->ObjectFactory->NewContextObject($discussionForm->Context, 'SqlBuilder');
                $sql->SetMainTable(sprintf('DiscussionMetadata_%s', ucfirst($type)), 't');
                foreach ($columns as $column) {
                    $sql->AddFieldNameValue($column, mysql_real_escape_string($metadata[$column]));
                }
                $sqlQuery = str_replace('UPDATE', 'REPLACE', $sql->GetUpdate()); // SqlBuilder does not have a GetReplace method
                $discussionForm->Context->Database->Execute(
                    $sqlQuery,
                    $discussionForm,
                    __FUNCTION__,
                    'Failed to save discussion metadata into database.'
                );
            }
        }
    }
}

// Delegation
if ($Context->SelfUrl == 'post.php') {
    $Context->AddToDelegate(
        'DiscussionForm',
        'DiscussionForm_PreCommentRender',
        'CiDiscussionMetadata_DiscussionForm_PreCommentRender'
    );
    $Context->AddToDelegate(
        'DiscussionForm',
        'PostSaveDiscussion',
        'CiDiscussionMetadata_DiscussionForm_PostSaveDiscussion'
    );
}
