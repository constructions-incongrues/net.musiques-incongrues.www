<?php if (!defined('APPLICATION')) exit();
$PluginInfo['DiscussionExtender'] = array(
    'Name' => 'Discussion Extender',
    'Description' => 'provides an interface to add arbitrary fields to discussions via the dashboard.',
    'Version' => '1.0',
    'RequiredApplications' => array('Vanilla' => '2.1'),
    'HasLocale' => TRUE,
    'MobileFriendly' => TRUE,
    'SettingsUrl' => '/dashboard/settings/discussionextender',
    'SettingsPermission' => 'Garden.Settings.Manage',
    'Author' => 'Zachary Doll',
    'AuthorEmail' => 'hgtonight@daklutz.com',
    'AuthorUrl' => 'http://daklutz.com',
    'License' => 'GPLv2'
);

class DiscussionExtender extends Gdn_Plugin {

  /**
   * The available form types for extending the discussion model.
   */
  public $FieldTypes = array();

  /**
   * The available display positions on the add/edit discussion form.
   */
  public $FieldPositions = array();

  /**
   * List of data we need to save to the config for each field.
   * @var type
   */
  public $FieldProperties = array('Name', 'Type', 'Position', 'Label', 'Options', 'Display', 'Required', 'Column');

  /**
   * List of default discussion fields. These are off limits to prevent accidental or malicious overwrite of data.
   */
  public $ReservedNames = array('DiscussionID', 'Type', 'ForeignID', 'CategoryID', 'InsertUserID', 'UpdateUserID', 'FirstCommentID', 'LastCommentID', 'Name', 'Body', 'Format', 'Tags', 'CountComments', 'CountBookmarks', 'CountViews', 'Closed', 'Announce', 'Sink', 'DateInserted', 'DateUpdated', 'InsertIPAddress', 'UpdateIPAddress', 'DateLastComment', 'LastCommentUserID', 'Score', 'Attributes', 'RegardingID');

  /**
   * Set up translated field types on construct
   */
  function __construct() {
    parent::__construct();
    $this->DefineConstantTranslations();
  }

  /**
   * Use translated names for the public properties
   */
  private function DefineConstantTranslations() {
    $this->FieldTypes = array(
        'TextBox' => T('TextBox'),
        'Dropdown' => T('Dropdown'),
        'CheckBox' => T('Checkbox'),
    );

    $this->FieldPositions = array(
        'cat' => T('Before Category Dropdown'),
        'body' => T('Before Discussion Body'),
        'mid' => T('After Discussion Body'),
        'last' => T('Before Buttons')
    );
  }

  /**
   * Add the Dashboard menu item.
   */
  public function Base_GetAppSettingsMenuItems_Handler($Sender) {
    $Menu = &$Sender->EventArguments['SideMenu'];
    $Menu->AddLink('Forum', T('Discussion Fields'), 'settings/discussionextender', 'Garden.Settings.Manage');
  }

  /**
   * Settings mini-controller
   */
  public function SettingsController_DiscussionExtender_Create($Sender) {
    $Sender->Permission('Garden.Settings.Manage');
    $Sender->AddSideMenu();

    $Sender->AddJsFile($this->GetResource('js/admin.discussionextender.js', FALSE, FALSE));
    if (!C('DiscussionExtender.Fields')) {
      $this->Setup();
    }

    $this->Dispatch($Sender);
  }

  /**
   * Settings/list of fields
   * @param type $Sender
   */
  public function Controller_Index($Sender) {
    $Sender->SetData('Positions', $this->FieldPositions);
    $Sender->SetData('Fields', $this->GetDiscussionFields());
    $Sender->Render('settings', '', 'plugins/DiscussionExtender');
  }

  /**
   * Add/Edit a field.
   */
  public function Controller_Edit($Sender) {
    $Sender->Permission('Garden.Settings.Manage');
    $Sender->SetData('Title', T('Add Discussion Field'));

    $Args = $Sender->RequestArgs;
    array_shift($Args);
    $Name = val(0, $Args, FALSE);

    if ($Sender->Form->AuthenticatedPostBack()) {
      $FormPostValues = $Sender->Form->FormValues();

      // Don't save the form values we aren't interested in
      foreach ($FormPostValues as $Key => $Value) {
        if (!in_array($Key, $this->FieldProperties)) {
          unset($FormPostValues[$Key]);
        }
      }

      // Make Options an array
      if ($Options = GetValue('Options', $FormPostValues)) {
        $Options = explode("\n", preg_replace('`[^\w\s-]`', '', $Options));
        if (count($Options) < 2) {
          $Sender->Form->AddError('Must have at least 2 options.', 'Options');
        }
        SetValue('Options', $FormPostValues, $Options);
      }

      // Check label
      if (!GetValue('Label', $FormPostValues)) {
        $Sender->Form->AddError('Label is required.', 'Label');
      }

      // Check form type
      if (!array_key_exists(GetValue('Type', $FormPostValues), $this->FieldTypes)) {
        $Sender->Form->AddError('Invalid form type.', 'Type');
      }

      // Merge updated data into config
      $Fields = $this->GetDiscussionFields();
      if (!$Name || $Name != GetValue('Name', $FormPostValues)) {
        // Make unique name from label for new fields
        $Name = $TestSlug = preg_replace('`[^0-9a-zA-Z]`', '', GetValue('Label', $FormPostValues));
        $i = 1;
        while (array_key_exists($Name, $Fields) || in_array($Name, $this->ReservedNames)) {
          $Name = $TestSlug . $i++;
        }
        $FormPostValues['Name'] = $Name;
      }

      // Save if no errors
      if (!$Sender->Form->ErrorCount()) {
        $Data = C('DiscussionExtender.Fields.' . $Name, array());
        $Data = array_merge((array) $Data, (array) $FormPostValues);
        SaveToConfig('DiscussionExtender.Fields.' . $Name, $Data);
        $this->Structure();
        $Sender->RedirectUrl = Url('/settings/discussionextender');
      }
    }
    else if ($Name) {
      // Editing
      $Data = $this->GetDiscussionField($Name);
      if ($Data) {
        if (isset($Data['Options']) && is_array($Data['Options'])) {
          $Data['Options'] = implode("\n", $Data['Options']);
        }
        $Sender->Form->SetData($Data);
        $Sender->Form->AddHidden('Name', $Name);
        $Sender->SetData('Title', T('Edit Discussion Field'));
      }
    }

    $Sender->SetData('FieldTypes', $this->FieldTypes);
    $Sender->SetData('FieldPositions', $this->FieldPositions);
    $Sender->Render('edit', '', 'plugins/DiscussionExtender');
  }

  /**
   * Delete a field.
   */
  public function Controller_Delete($Sender) {
    $Sender->SetData('Title', 'Delete Discussion Field');
    $Args = $Sender->RequestArgs;
    array_shift($Args);

    $Field = $this->GetDiscussionField($Args[0]);
    if ($Field) {
      if ($Sender->Form->AuthenticatedPostBack()) {
        $FormPostValues = $Sender->Form->FormValues();
        if (!$FormPostValues['Confirm']) {
          $Sender->Form->AddError('You must confirm the removal.', 'Confirm');
        } else {
          if($FormPostValues['Wipe']) {
            $this->RemoveFieldDataFromDB($Field);
          }
          RemoveFromConfig('DiscussionExtender.Fields.' . $Field['Name']);
          $Sender->RedirectUrl = Url('/settings/discussionextender');
        }
      }

      $Sender->SetData('Field', $Field);
      $Sender->Render('delete', '', 'plugins/DiscussionExtender');
    } else {
      Redirect('settings/discussionextender');
    }
  }

  /**
   * Display custom fields on Discussion form.
   */
  public function PostController_DiscussionFormOptions_Handler($Sender) {
    $this->RenderDiscussionFieldInputs($Sender->Form, 'mid');
  }

  /**
   * Display custom fields on Discussion form.
   */
  public function PostController_AfterDiscussionFormOptions_Handler($Sender) {
    $this->RenderDiscussionFieldInputs($Sender->Form, 'last');
  }

  /**
   * Display custom fields on Discussion form.
   */
  public function Gdn_Form_BeforeBodyBox_Handler($Sender) {
    if (strtolower($Sender->EventArguments['Table']) == 'discussion') {
      $this->RenderDiscussionFieldInputs($Sender, 'body');
    }
  }

  /**
   * Display custom fields on Discussion form and populate existing data.
   */
  public function PostController_BeforeFormInputs_Handler($Sender) {
    $RequestMethod = strtolower($Sender->RequestMethod);
    if ($RequestMethod == 'editdiscussion') {
      $this->AddExistingDiscussionFieldData($Sender->Form, $Sender->Data('Discussion'));
      $this->RenderDiscussionFieldInputs($Sender->Form, 'cat');
    }
    else if ($RequestMethod == 'discussion') {
      $this->RenderDiscussionFieldInputs($Sender->Form, 'cat');
    }
  }

  /**
   * Takes a Gdn_Form object and a specific position and spits out the
   * appropriate markup for the configured fields in that position
   * @param Gdn_Form $Form
   * @param string $Position
   */
  private function RenderDiscussionFieldInputs($Form, $Position) {
    $Fields = $this->GetDiscussionFields();
    foreach ($Fields as $Name => $Field) {
      if ($Field['Position'] == $Position) {
        switch ($Field['Type']) {
          case 'Dropdown':
            echo $Form->Label($Field['Label'], $Name);
            echo $Form->Dropdown($Name, array_combine($Field['Options'], $Field['Options']));
            break;
          case 'CheckBox':
            echo $Form->CheckBox($Name, $Field['Label']);
            break;
          case 'TextBox':
            echo $Form->Label($Field['Label'], $Name);
            echo $Form->TextBox($Name);
            break;
          default:
            break;
        }
      }
    }
  }
  
  /**
   * Takes a form object and discussion. It populates the form with existing
   * attributes.
   * @param Gdn_Form $Form
   * @param stdClass $Discussion
   */
  private function AddExistingDiscussionFieldData($Form, $Discussion) {
    $Fields = DiscussionModel::GetRecordAttribute($Discussion, 'ExtendedFields', array());
    $AllowedFields = $this->GetDiscussionFields();

    foreach ($Fields as $Field => $Value) {
      if(array_key_exists($Field, $AllowedFields)) {
       $Form->SetValue($Field, $Value);
      }
    }
  }

  /**
   * Display custom fields on Discussion
   */
  public function DiscussionController_BeforeDiscussionBody_Handler($Sender) {
    $Discussion = $Sender->EventArguments['Discussion'];
    $Fields = $this->GetDiscussionFields();
    $FieldAttributes = DiscussionModel::GetRecordAttribute($Discussion, 'ExtendedFields', array());
    $FieldString = '';
    foreach ($Fields as $Name => $Field) {
      $ColumnValue = val($Name, $Discussion, FALSE);
      $Value = ($ColumnValue) ? $ColumnValue : val($Name, $FieldAttributes, FALSE);
      if ($Field['Display'] && $Value) {
        $FieldString .= Wrap($Field['Label'], 'dt') . ' ';
        $FieldString .= Wrap($Value, 'dd') . ' ';
      }
    }

    echo WrapIf($FieldString, 'dl', array('class' => 'About'));
  }

  /**
   * Validate extended fields that are required.
   * @param type $Sender
   */
  public function DiscussionModel_BeforeSaveDiscussion_Handler($Sender) {
    $Fields = $this->GetDiscussionFields();
    foreach ($Fields as $Name => $Field) {
      if (GetValue('Required', $Field)) {
         $Sender->Validation->ApplyRule($Name, 'Required', $Field['Label']." is required.");
         // TODO Force validation on enum when not a column
      }
    }
  }

  /**
   * Save the attribute fields to the attributes column
   * @param DiscussionModel $Sender
   */
  public function DiscussionModel_AfterSaveDiscussion_Handler($Sender) {
    // Confirm we have submitted form values
    $FormPostValues = GetValue('FormPostValues', $Sender->EventArguments);

    if (is_array($FormPostValues)) {
       $DiscussionID = GetValue('DiscussionID', $FormPostValues);
       $AllowedFields = $this->GetDiscussionFields();
       $Columns = Gdn::SQL()->FetchColumns('Discussion');

       // Filter out columns and only save current fields.
       foreach ($FormPostValues as $Name => $Field) {
          if (!array_key_exists($Name, $AllowedFields)) {
             unset($FormPostValues[$Name]);
          }

          if (in_array($Name, $Columns)) {
             unset($FormPostValues[$Name]);
          }
       }

       // Update attributes if any made it thru
       if (count($FormPostValues)) {
          $Sender->SaveToSerializedColumn('Attributes', $DiscussionID, 'ExtendedFields', $FormPostValues);
       }
    }
  }

  /**
   * Add the "permanent" fields as columns to the discussion table.
   */
  public function Structure() {
    $Fields = $this->GetDiscussionFields();

    $Structure = Gdn::Structure();
    $Structure->Table('Discussion');
    $ColumnCount = 0;
    foreach ($Fields as $Name => $Field) {
      // Skip attribute fields
      if(!$Field['Column']) {
        continue;
      }
      
      $NullDefault = ($Field['Required']) ? FALSE : TRUE;
      switch ($Field['Type']) {
        case 'Dropdown':
          $Structure->Column($Name, $Field['Options'], $NullDefault);
          $ColumnCount++;
          break;
        case 'CheckBox':
          $Structure->Column($Name, 'tinyint(1)', '0');
          $ColumnCount++;
          break;
        case 'TextBox':
          $Structure->Column($Name, 'varchar(255)', $NullDefault);
          $ColumnCount++;
          break;
        default:
          break;
      }
    }

    if($ColumnCount) {
      $Structure->Set();
    }
  }

  /**
   * This permanently removes a field's associated information from the DB. It 
   * completely wipes the data from the DB.
   * 
   * @param array $Field
   * @return bool
   */
  private function RemoveFieldDataFromDB($Field) {
    $Database = Gdn::Database();
    $Px = $Database->DatabasePrefix;
    if($Field['Column']) {
      // Remove column from Discussion table
      $Table = $Database->QuoteExpression($Field['Name']);
      $Sql = "alter table {$Px}Discussion drop `{$Table}`";
      return $Database->Query($Sql)->Result();
    }
    else {
      // Remove data from Discussion Attributes
      $Sql = "select DiscussionID, Attributes from {$Px}Discussion where Attributes is not NULL";
      $Discussions = $Database->Query($Sql)->Result();
      $DiscussionModel = new DiscussionModel();
      foreach($Discussions as $Discussion) {
        $Attributes = unserialize($Discussion->Attributes);
        $Updated = FALSE;
        if(array_key_exists('ExtendedFields', $Attributes) && array_key_exists($Field['Name'], $Attributes['ExtendedFields'])) {
          // Remove the discussion field from the attributes
          unset($Attributes['ExtendedFields'][$Field['Name']]);
          $Updated = TRUE;
        }
        
        if($Updated) {
          if(count($Attributes['ExtendedFields']) == 0) {
            // Remove the storage array if it is empty
            unset($Attributes['ExtendedFields']);
          }
          
          // Save the new attributes to the db
          $DiscussionModel->SetField($Discussion->DiscussionID, 'Attributes', serialize($Attributes));
        }
      }
      return TRUE;
    }
  }
  
  /**
   * Get list of custom discussion fields.
   *
   * @return array
   */
  private function GetDiscussionFields() {
    $Fields = C('DiscussionExtender.Fields', array());

    if (!is_array($Fields)) {
      $Fields = array();
    }

    // Data checks
    foreach ($Fields as $Name => $Field) {
      // Require an array for each field
      if (!is_array($Field) || strlen($Name) < 1) {
        unset($Fields[$Name]);
      }
    }

    return $Fields;
  }

  /**
   * Get the data associated with a single discussion field.
   *
   * @param $Name
   * @return array
   */
  private function GetDiscussionField($Name) {
    return C('DiscussionExtender.Fields.' . $Name, FALSE);
  }

}
