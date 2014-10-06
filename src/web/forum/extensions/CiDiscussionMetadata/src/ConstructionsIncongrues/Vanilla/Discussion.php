<?php
namespace ConstructionsIncongrues\Vanilla;

class Discussion extends \Discussion
{
    public $Type;

    public function Clear()
    {
        parent::Clear();
        unset($this->Metadata);
    }

    public function GetPropertiesFromDataSet($DataSet)
    {
        // Get default properties
        parent::GetPropertiesFromDataSet($DataSet);

        // Get discussion types
        $this->Metadata = array();
        foreach ($this->Context->Configuration['CiDiscussionMetadata']['Types'] as $type) {
            $sql = $this->Context->ObjectFactory->NewContextObject($this->Context, 'SqlBuilder');
            $sql->SetMainTable('DiscussionMetadata', 'dmd');
            $sql->addSelect(array('DiscussionID', 'Type'), 'dmd');
            $sql->AddWhere('dmd', 'DiscussionID', '', $this->DiscussionID, '=');
            $result = $this->Context->Database->Select(
                $sql,
                get_class($this),
                __FUNCTION__,
                'An error occurred while retrieving discussion additional metadata.'
            );
            $types = array();
            while ($row = $this->Context->Database->GetRow($result)) {
                $this->Metadata[$row['Type']] = array();
            }
        }

        // Fetch additional metadata from dedicated database table
        foreach (array_keys($this->Metadata) as $type) {
            $tableMetadata = sprintf('DiscussionMetadata_%s', ucfirst($type));
            if (!isset($this->Context->DatabaseColumns[$tableMetadata])) {
                continue;
            }
            $sql = $this->Context->ObjectFactory->NewContextObject($this->Context, 'SqlBuilder');
            $sql->SetMainTable($tableMetadata, 'mdt');
            $sql->AddSelect(
                array_values($this->Context->DatabaseColumns[$tableMetadata]),
                'mdt'
            );
            $sql->AddWhere('mdt', 'DiscussionID', '', $this->DiscussionID, '=');
            $result = $this->Context->Database->Select(
                $sql,
                get_class($this),
                __FUNCTION__,
                'An error occurred while retrieving discussion additional metadata.'
            );

            // Populate metadata
            $metadata = $this->Context->Database->GetRow($result);
            $this->Metadata[$type] = $metadata;
        }
    }
}
