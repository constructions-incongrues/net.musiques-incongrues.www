<?php
namespace ConstructionsIncongrues\Vanilla;

class DiscussionManager extends \DiscussionManager
{
    public function GetDiscussionBuilder($s = 0)
    {
        $sqlBuilder = parent::GetDiscussionBuilder($s);
        $sqlBuilder->AddJoin('DiscussionMetadata', 'dmt', 'DiscussionID', 't', 'DiscussionID', 'left join');
        $sqlBuilder->AddSelect('Type', 'dmt', 'Type');
        return $sqlBuilder;
    }
}
