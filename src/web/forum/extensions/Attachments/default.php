<?php
/*
Extension Name: Attachments
Extension Url: http://vanillaforums.org/addon/407/recaptcha
Description: Allows users to attach files to their comments.
Version: 2.1
Author: Maurice (Jazzman) Krijtenberg
Author Url: http://www.krijtenberg.nl

Changes

Version 2.1 - 20.12.2006, Jazzman
- Removed version check as it isn't really necessary :)
- Fixed a security issue, where discussion attachments could be downloaded by
  any user (even if you weren't able to view the discussion itself) by just
  altering the URL and attachment number (Thanks to jaz!)
- Added new SaveAsDialog function which allows filenames with spaces

*/
$Context->Configuration['ATTACHMENTS_ALLOWED_FILETYPES'] = array (
	'image/gif'						=> array('gif', 'GIF'),
	'image/png'						=> array('png', 'PNG'),
	'image/jpeg'					=> array('jpg', 'jpeg', 'JPG', 'JPEG'),
	'image/pjpeg'					=> array('jpg', 'jpeg', 'JPG', 'JPEG'),
	'application/pdf'				=> array('pdf', 'PDF'),
	'application/x-pdf'				=> array('pdf', 'PDF'),
	'application/msword'			=> array('doc', 'DOC', 'rtf', 'RTF'),
	'application/zip'				=> array('zip', 'ZIP'),
	'application/x-zip-compressed'	=> array('zip', 'ZIP'),
	'application/octet-stream'		=> array('rar', 'RAR', 'doc', 'DOC'),
	'text/plain'					=> array('txt', 'TXT'),
	'application/x-gzip'			=> array('gz', 'GZ', 'tar.gz', 'TAR.GZ'),
	'application/download'			=> array('rar', 'RAR')
);

// Language dictionary
$Context->Dictionary['Attachments'] = 'Attachments';
$Context->Dictionary['DeleteAttachment'] = 'Delete';
$Context->Dictionary['ExtensionOptions'] = 'Extension Options';
$Context->Dictionary['AttachmentSettings'] = 'Attachments';
$Context->Dictionary['AttachmentUploadSettingsInfo'] = 'The <em>Upload Path</em> should be an absolute path. Use a path outside of your webroot for better security. Remember that the folder should be writable.<br><br>You can use the following replacement tags:<br>%day%, %month%, %year%, %userid%
';
$Context->Dictionary['UploadPath'] = 'Upload Path';
$Context->Dictionary['MaximumFilesize'] = 'Maximum Filesize <small>(bytes)</small>';
$Context->Dictionary['AttachmentFiletypes'] = 'Allowed Filetypes';
$Context->Dictionary['AttachmentFiletypesNotes'] = 'Here you can add the filetypes which are allowed for uploading attachments. Add an application-type and file extension. You can add more file extensions for one application mime type. Use commas to seperate them.';
$Context->Dictionary['ApplicationType'] = 'Application-type';
$Context->Dictionary['FiletypeExtension'] = 'File extension';
$Context->Dictionary['AddFiletype'] = 'Add another application-type/extension';
$Context->Dictionary['AttachmentImport'] = 'Import Attachments';
$Context->Dictionary['AttachmentImportNotes'] = 'Import your attachments from the older Attachment extension version 1.x';
$Context->Dictionary['AttachmentImportPath'] = 'Path to your old attachments folder';
$Context->Dictionary['RememberToSetAttachmentsPermissions'] = 'Remember to set Attachments Permissions for you and your users. You can do it at the <a href="'.GetUrl($Context->Configuration, 'settings.php', '', '', '', '', 'PostBackAction=Roles').'">Roles &amp; Permissions</a> page.';
$Context->Dictionary['ErrCreateTable'] = 'Could not create Attachments database table!';
$Context->Dictionary['ErrCreateConfig'] = 'Could not save Attachments settings to configuration file!';
$Context->Dictionary['ErrCreateAttachmentFolder'] = 'An error occured while creating a new attachment folder. Check your upload path permissions';
$Context->Dictionary['ErrAttachmentNotFound'] = 'Could not find the attachment';
$Context->Dictionary['PERMISSION_ADD_ATTACHMENTS'] = 'Add attachments';
$Context->Dictionary['PERMISSION_MANAGE_ATTACHMENTS'] = 'Manage attachments';


/****** DO NOT EDIT BELOW THIS LINE ******/

// Default permissions
$Context->Configuration['PERMISSION_ADD_ATTACHMENTS'] = '0';
$Context->Configuration['PERMISSION_MANAGE_ATTACHMENTS'] = '0';

if (!array_key_exists('ATTACHMENTS_VERSION', $Configuration)) {
	$Errors = 0;
	$TableDrop = "drop table if exists `".$Configuration['DATABASE_TABLE_PREFIX']."Attachment`";
	if (!mysql_query($TableDrop, $Context->Database->Connection)) $Errors = 1;
	$TableCreate = "
		CREATE TABLE `".$Configuration['DATABASE_TABLE_PREFIX']."Attachment` (
		  `AttachmentID` int(11) NOT NULL auto_increment,
		  `UserID` int(11) NOT NULL default '0',
		  `DiscussionID` int(11) NOT NULL default '0',
		  `CommentID` int(11) NOT NULL default '0',
		  `Title` varchar(200) NOT NULL default '',
		  `Description` text NOT NULL,
		  `Name` varchar(200) NOT NULL default '',
		  `Path` text NOT NULL,
		  `Size` int(11) NOT NULL default '0',
		  `MimeType` varchar(200) NOT NULL default '',
		  `DateCreated` datetime NOT NULL default '0000-00-00 00:00:00',
		  `DateModified` datetime NOT NULL default '0000-00-00 00:00:00',
		  PRIMARY KEY  (`AttachmentID`)
		)";
	if (!mysql_query($TableCreate, $Context->Database->Connection)) $Errors = 1;
	if ($Errors == 0) {
		// Add the db structure to the database configuration file
		$Structure = "// Attachments Table Structure
\$DatabaseTables['Attachment'] = 'Attachment';
\$DatabaseColumns['Attachment']['AttachmentID'] = 'AttachmentID';
\$DatabaseColumns['Attachment']['UserID'] = 'UserID';
\$DatabaseColumns['Attachment']['DiscussionID'] = 'DiscussionID';
\$DatabaseColumns['Attachment']['CommentID'] = 'CommentID';
\$DatabaseColumns['Attachment']['Title'] = 'Title';
\$DatabaseColumns['Attachment']['Description'] = 'Description';
\$DatabaseColumns['Attachment']['Name'] = 'Name';
\$DatabaseColumns['Attachment']['Path'] = 'Path';
\$DatabaseColumns['Attachment']['Size'] = 'Size';
\$DatabaseColumns['Attachment']['MimeType'] = 'MimeType';
\$DatabaseColumns['Attachment']['DateCreated'] = 'DateCreated';
\$DatabaseColumns['Attachment']['DateModified'] = 'DateModified';
";
		if (!AppendToConfigurationFile($Configuration['APPLICATION_PATH'].'conf/database.php', $Structure)) $Errors = 1;
		if ($Errors == 0) {
			AddConfigurationSetting($Context, 'ATTACHMENTS_UPLOAD_PATH', $Configuration['APPLICATION_PATH'] . 'uploads/%year%/%month%/');
			AddConfigurationSetting($Context, 'ATTACHMENTS_MAXIMUM_FILESIZE', '2048000');
			AddConfigurationSetting($Context, 'ATTACHMENTS_VERSION', '2.1');
		} else {
			// Could not save configuration
			$NoticeCollector->AddNotice($Context->GetDefinition('ErrCreateConfig'));
		}
	} else {
		// Could not create database
		$NoticeCollector->AddNotice($Context->GetDefinition('ErrCreateTable'));
	}
} else {

	class Attachment {
		var $AttachmentID;
		var $UserID;
		var $DiscussionID;
		var $CommentID;
		var $Title;
		var $Description;
		var $Name;
		var $Path;
		var $Size;
		var $MimeType;
		var $Extension;
		var $DateCreated;
		var $DateModified;
		var $Url;

		// Constructor
		function Attachment() {
			$this->Clear();
		}

		function Clear() {
			$this->AttachmentID = 0;
			$this->UserID = 0;
			$this->DiscussionID = 0;
			$this->CommentID = 0;
			$this->Title = "";
			$this->Description = "";
			$this->Name = "";
			$this->Path = "";
			$this->Size = 0;
			$this->Extension = "";
			$this->MineType = "";
			$this->DateCreated = "";
			$this->DateModified = "";
			$this->Url = "";
		}

		function GetPropertiesFromDataSet(&$Configuration, $DataSet) {
			$this->AttachmentID = @$DataSet['AttachmentID'];
			$this->UserID = @$DataSet['UserID'];
			$this->DiscussionID = @$DataSet['DiscussionID'];
			$this->CommentID = @$DataSet['CommentID'];
			$this->Title = @$DataSet['Title'];
			$this->Description = @$DataSet['Description'];
			$this->Name = @$DataSet['Name'];
			$this->Path = @$DataSet['Path'];
			$this->Size = @$DataSet['Size'];
			$this->MineType = @$DataSet['MineType'];
			$this->DateCreated = @$DataSet['DateCreated'];
			$this->DateModified = @$DataSet['DateModified'];
			$this->Url = GetUrl($Configuration, './', '', '', '', '', 'PostBackAction=Download&AttachmentID='. $this->AttachmentID);
			$this->Extension = strtolower(end(explode('.', $this->Name)));
		}

		function FormatPropertiesForDatabaseInput() {
			$this->Name = FormatStringForDatabaseInput($this->Name);
			$this->Title = FormatStringForDatabaseInput($this->Title);
			$this->Description = FormatStringForDatabaseInput($this->Description);
			$this->Path = FormatStringForDatabaseInput($this->Path);
		}

		function FormatPropertiesForDisplay() {
			$this->Name = FormatStringForDisplay($this->Name);
			$this->Title = FormatStringForDisplay($this->Title);
			$this->Description = FormatStringForDisplay($this->Description);
			$this->Path = FormatStringForDisplay($this->Path);
		}
	}

	class AttachmentManager extends Delegation {
		var $FormName;
		var $Attachments;
		var $DiscussionID;
		var $Discussion;
		var $CommentID;
		var $Comment;
		var $FileIdentifier;

		// Constructor
		function AttachmentManager(&$Context) {
			$this->Name	= 'AttachmentManager';
			$this->Delegation($Context);
			$this->FormName = 'frmPostComment';
			$this->Attachments = array();
			$this->DiscussionID = ForceIncomingInt('DiscussionID', 0);
			$this->FileIdentifier = 'file';
			$this->CallDelegate('Constructor');
		}

		function GetAttachmentBuilder() {
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, 'SqlBuilder');
			$s->SetMainTable('Attachment', 'a');
			$s->AddSelect(array('AttachmentID', 'UserID', 'DiscussionID', 'CommentID', 'Title', 'Description', 'Name', 'Path', 'Size', 'MimeType', 'DateCreated', 'DateModified'), 'a');
			$s->AddOrderBy('AttachmentID', 'a', 'DESC');
			return $s;
		}

		function GetAttachmentById($AttachmentID) {
			$Attachment = $this->Context->ObjectFactory->NewObject($this->Context, 'Attachment');
			$s = $this->GetAttachmentBuilder();
			$s->AddWhere('a', 'AttachmentID', '', $AttachmentID, '=');
			$ResultSet = $this->Context->Database->Select($s, $this->Name, 'RetrieveAttachments', 'An error occurred while retrieving the attachment.');
			if ($this->Context->Database->RowCount($ResultSet) == 0)
				$this->Context->WarningCollector->Add($this->Context->GetDefinition('ErrAttachmentNotFound'));
			while ($rows = $this->Context->Database->GetRow($ResultSet)) {		
				$Attachment->GetPropertiesFromDataSet($this->Context->Configuration, $rows);
			}
			return $this->Context->WarningCollector->Iif($Attachment, false);
		}

		function RemoveAttachment($AttachmentID) {
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, 'SqlBuilder');
			$s->SetMainTable('Attachment', 'a');
			$s->AddWhere('a', 'AttachmentID', '', $AttachmentID, '=');
			$this->Context->Database->Delete($s, $this->Name, 'RemoveAttachment', 'An error occurred while removing attachment.');
		}

		function RetrieveAttachments() {
			$this->CallDelegate('PreRetrieveAttachments');
			if( $this->DiscussionID > 0 ) {
				$s = $this->GetAttachmentBuilder();
				$s->AddWhere('a', 'DiscussionID', '', $this->DiscussionID, '=');
				$ResultSet = $this->Context->Database->Select($s, $this->Name, 'RetrieveAttachments', 'An error occurred while retrieving attachments.');
				while ($rows = $this->Context->Database->GetRow($ResultSet)) {
					$Attachment = $this->Context->ObjectFactory->NewObject($this->Context, 'Attachment');
					$Attachment->GetPropertiesFromDataSet($this->Context->Configuration, $rows);
					$this->Attachments[$Attachment->CommentID][] = $Attachment;
				}
			}
			$this->CallDelegate('PostRetrieveAttachments');
		}

		function UploadAttachments() {
			$this->CallDelegate('PreUploadAttachments');
			if( $this->GetFilesFound() > 0 ) {
				$this->Attachments = array();
				$Uploader = $this->Context->ObjectFactory->NewContextObject($this->Context, 'Uploader');
				$Uploader->MaximumFileSize = $this->Context->Configuration['ATTACHMENTS_MAXIMUM_FILESIZE'];
				$Uploader->AllowedFileTypes = $this->Context->Configuration['ATTACHMENTS_ALLOWED_FILETYPES'];
				foreach( $_FILES as $Key => $File ) {
					if( substr($Key, 0, strlen($this->FileIdentifier)) == $this->FileIdentifier && basename($File['name']) !== "" ) {
						$UploadPath = $this->CreateAttachmentFolder();
						if( $UploadPath ) {
							$NewFileName = $Uploader->Upload($Key, $UploadPath, $File['name']);
							if( $this->Context->WarningCollector->Count() == 0 ) {
								$FilePath = $UploadPath.$NewFileName;
								// Change file permissions
								@chmod($FilePath, 0777);
								// Remember uploaded attachment
								$this->Attachments[] = array('Path' => $FilePath, 'Size' => $File['size'], 'Type' => $File['type']);
							}
						} else {
							$this->Context->WarningCollector->Add($this->Context->GetDefinition('ErrCreateAttachmentFolder'));
						}
					}
				}
			}
			$this->CallDelegate('PostUploadAttachments');
			return $this->Context->WarningCollector->Count();
		}

		function SaveAttachments() {
			$this->CallDelegate('PreSaveAttachments');

			// If there are warning messages, delete the attachments
			// because the comment hasn't been created
			if( $this->Context->WarningCollector->Count() > 0 ) {
				foreach( $this->Attachments as $File ) {
					unlink( $File['Path'] );
				}
			} else {
				$Attachment = $this->Context->ObjectFactory->NewObject($this->Context, 'Attachment');			
				foreach( $this->Attachments as $File ) {
					$Attachment->UserID = $this->Context->Session->UserID;
					$Attachment->DiscussionID = $this->DiscussionID;
					$Attachment->CommentID = $this->Comment->CommentID;
					$Attachment->Title = basename($File['Path']);
					$Attachment->Name = basename($File['Path']);
					$Attachment->Path = $File['Path'];
					$Attachment->Size = $File['Size'];
					$Attachment->MimeType = $File['Type'];
					$Attachment->DateCreated = MySqlDateTime();
					$Attachment->DateModified = MySqlDateTime();

					$this->DelegateParameters['SaveAttachment'] = &$Attachment;
					$this->CallDelegate('PreSaveAttachment');
					
					// Save the attachment to database
					$this->SaveAttachment($Attachment);

					$this->DelegateParameters['ResultAttachment'] = &$Attachment;
					$this->CallDelegate('PostSaveAttachment');
				}
			}
			$this->CallDelegate('PostSaveAttachments');
		}

		function SaveAttachment(&$Attachment) {
			$Attachment->FormatPropertiesForDatabaseInput();
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, 'SqlBuilder');
			$s->SetMainTable('Attachment', 'a');
			$s->AddFieldNameValue('UserID', $Attachment->UserID);
			$s->AddFieldNameValue('DiscussionID', $Attachment->DiscussionID);
			$s->AddFieldNameValue('CommentID', $Attachment->CommentID);
			$s->AddFieldNameValue('Title', $Attachment->Title);
			$s->AddFieldNameValue('Description', $Attachment->Description);
			$s->AddFieldNameValue('Name', $Attachment->Name);
			$s->AddFieldNameValue('Path', $Attachment->Path);
			$s->AddFieldNameValue('Size', $Attachment->Size);
			$s->AddFieldNameValue('MimeType', $Attachment->MimeType);
			$s->AddFieldNameValue('DateCreated', $Attachment->DateCreated);
			$s->AddFieldNameValue('DateModified', $Attachment->DateModified);
			$Attachment->AttachmentID = $this->Context->Database->Insert($s, $this->Name, 'SaveAttachments', 'An error occurred while saving an attachment');
		}

		function DownloadAttachment($AttachmentID) {
			$this->CallDelegate('PreDownloadAttachment');
			$Attachment = $this->Context->ObjectFactory->NewObject($this->Context, 'Attachment');
			$s = $this->GetAttachmentBuilder();
			$s->AddWhere('a', 'AttachmentID', '', $AttachmentID, '=');
			$ResultSet = $this->Context->Database->Select($s, $this->Name, 'RetrieveAttachments', 'An error occurred while retrieving attachments.');
			while ($rows = $this->Context->Database->GetRow($ResultSet)) {
				$Attachment->GetPropertiesFromDataSet($this->Context->Configuration, $rows);
			}
			if( $Attachment->AttachmentID > 0 ) {
				// If this attachment belongs to a discussion, check if we can view this discussion, else 
				// we should not be able to download the attachment file either! (Thanks to jaz)
				if ($Attachment->DiscussionID > 0) {
					$DiscussionManager = $this->Context->ObjectFactory->NewContextObject($this->Context, 'DiscussionManager');
					$DiscussionData = $DiscussionManager->GetDiscussionById($Attachment->DiscussionID);
					if (!$DiscussionData) die();
				}
				$this->DelegateParameters['DownloadAttachment'] = &$Attachment;
				$this->CallDelegate('DownloadAttachment');
				$Path = str_replace(basename($Attachment->Path), '', $Attachment->Path);
				$this->SaveAsDialogue($Path, $Attachment->Name);
			} else {
				die();
			}
		}

		function SaveAsDialogue($FolderPath, $FileName, $DeleteFile = '0') {
			$DeleteFile = ForceBool($DeleteFile, 0);
			if ($FolderPath !== '') {
				if (substr($FolderPath, strlen($FolderPath)-1) != '/') $FolderPath = $FolderPath.'/';
			}
			$FolderPath = $FolderPath.$FileName;
			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0'); 
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="'.$FileName.'"');
			header('Content-Transfer-Encoding: binary');
			readfile($FolderPath);
			if ($DeleteFile) unlink($FolderPath);
			die();
		}

		function Render_Attachments() {
			$this->DelegateParameters['Comment'] = &$this->Comment;
			$this->CallDelegate('PreRender_Attachments');
			if( $this->Comment ) {
				if( isset( $this->Attachments[$this->Comment->CommentID] )) {
					$AttachmentBody = "";
					foreach( $this->Attachments[$this->Comment->CommentID] as $Attachment) {
						$Attachment->FormatPropertiesForDisplay();
						$this->DelegateParameters['Attachment'] = &$Attachment;
						$this->DelegateParameters['AttachmentBody'] = &$AttachmentBody;
						$this->CallDelegate('PreRender_Attachment');
						if( $Attachment ) {
							$AttachmentBody .= '
								<li class="Attachment '.$Attachment->Extension.'"><a href="'. $Attachment->Url .'">'. $Attachment->Title .'</a></li>';
						}				
						$this->CallDelegate('PostRender_Attachment');
					}
					if( $AttachmentBody !== "" ) {
						$AttachmentBody = '<div class="Attachments" id="Attachments_'.$this->Comment->CommentID.'"><ul>' . $AttachmentBody;
						$AttachmentBody .= '</ul></div>';
					}
					$this->Comment->Body .= $AttachmentBody;
				}
			}
			$this->CallDelegate('PostRender_Attachments');
		}

		function Render_AttachmentForm() {
			$AttachmentForm = '
				<ul><li><label for="Attachments">'.$this->Context->GetDefinition("Attachments").'</label>
				'.$this->GetAttachmentsList($this->CommentID).'
				<input id="AttachmentFile" type="file" name="file" class="AttachmentInput" /></li></ul>
				<script type="text/javascript" language="javascript">
					var f = document.getElementById(\''. $this->FormName .'\');
					f.encoding = \'multipart/form-data\';
				</script>
			';
			$this->DelegateParameters['AttachmentForm'] = &$AttachmentForm;
			$this->CallDelegate('PreRender_AttachmentForm');
			echo $AttachmentForm;
			$this->CallDelegate('PostRender_AttachmentForm');
		}

		function GetAttachmentsList($CommentID) {
			if( $CommentID > 0 ) {
				$AttachmentList = '<ul class="AttachmentList">';

				$this->DelegateParameters['AttachmentList'] = &$AttachmentList;
				$this->CallDelegate('PreGetAttachmentsList');
				
				$s = $this->GetAttachmentBuilder();
				$s->AddWhere('a', 'CommentID', '', $CommentID, '=');
				$Attachment = false;
				$ResultSet = $this->Context->Database->Select($s, $this->Name, 'GetAttachmentsList', 'An error occurred while retrieving attachments.');
				while ($rows = $this->Context->Database->GetRow($ResultSet)) {
					if( !$Attachment ) $Attachment = $this->Context->ObjectFactory->NewObject($this->Context, 'Attachment');
					$Attachment->Clear();
					$Attachment->GetPropertiesFromDataSet($this->Context->Configuration, $rows);
					$Attachment->FormatPropertiesForDisplay();

					$this->DelegateParameters['Attachment'] = &$Attachment;
					$this->DelegateParameters['AttachmentList'] = &$AttachmentList;
					$this->CallDelegate('AttachmentsListItem');

					if( $Attachment ) {
						$AttachmentList .= '<li id="Attachment_'.$Attachment->AttachmentID.'">'. $Attachment->Name .' ';
						if( $Attachment->UserID == $this->Context->Session->UserID || $this->Context->Session->User->Permission('PERMISSION_MANAGE_ATTACHMENTS') ) {
							$AttachmentList .= '<a href="./" onclick="DeleteAttachment(\''. $this->Context->Configuration['WEB_ROOT'] . "extensions/Attachments/ajax.php" .'\', \''. $Attachment->AttachmentID .'\'); return false;">'. $this->Context->GetDefinition('DeleteAttachment') .'</a>';
						}
						$AttachmentList .= '</li>';
					}
				}
				$AttachmentList .= '</ul>';
				
				$this->CallDelegate('PostGetAttachmentsList');
				return $AttachmentList;
			}
		}

		function GetFilesFound() {
			$FilesFound = 0;
			foreach( $_FILES as $Key => $File ) {
				if( substr($Key, 0, strlen($this->FileIdentifier)) == $this->FileIdentifier && strlen(basename($File['name'])) !== 0 )
					$FilesFound++;
			}
			return $FilesFound;
		}

		function mkdir_recursive($path, $mode = 0777) {
			if (!file_exists($path)) {
				$this->mkdir_recursive(dirname($path), $mode);
				if( @mkdir($path, $mode) ) @chmod($path, $mode);
			}
		}

		function CreateAttachmentFolder() {
			$TargetPath = str_replace(
				array('%day%', '%month%', '%year%', '%userid%'),
				array(date("d"), date("m"), date("Y"), $this->Context->Session->UserID),
				$this->Context->Configuration['ATTACHMENTS_UPLOAD_PATH']
			);
			$this->mkdir_recursive($TargetPath);
			return (is_dir($TargetPath) && is_readable($TargetPath)) ? $TargetPath : "";
		}
	}

	if ($Context->SelfUrl == "settings.php" && $Context->Session->User->Permission('PERMISSION_MANAGE_ATTACHMENTS')) {

		class AttachmentsForm extends PostBackControl {
			var $ConfigurationManager;

			function AttachmentsForm(&$Context) {
				$this->Name = 'AttachmentsForm';
				$this->ValidActions = array('Attachments', 'ProcessAttachments', 'ImportAttachments');
				$this->Constructor($Context);
				if (!$this->Context->Session->User->Permission('PERMISSION_MANAGE_ATTACHMENTS')) {
					$this->IsPostBack = 0;
				} elseif( $this->IsPostBack ) {
					$SettingsFile = $this->Context->Configuration['APPLICATION_PATH'].'conf/settings.php';
					$this->ConfigurationManager = $this->Context->ObjectFactory->NewContextObject($this->Context, 'ConfigurationManager');
					if ($this->PostBackAction == 'ProcessAttachments') {
						$this->ConfigurationManager->GetSettingsFromForm($SettingsFile);
						// Checkboxes aren't posted back if unchecked, so make sure that they are saved properly
						$this->DelegateParameters['ConfigurationManager'] = &$this->ConfigurationManager;
						$this->CallDelegate('DefineCheckboxes');

						// And save everything
						if ($this->ConfigurationManager->SaveSettingsToFile($SettingsFile)) {
							header('location: '.GetUrl($this->Context->Configuration, 'settings.php', '', '', '', '', 'PostBackAction=Attachments&Success=1'));
						} else {
							$this->PostBackAction = 'Attachments';
						}

					} elseif( $this->PostBackAction == 'ImportAttachments' ) {
						$s = $this->Context->ObjectFactory->NewContextObject($this->Context, 'SqlBuilder');
						$s->SetMainTable('Attachment', 'a');
						$s->AddWhere('a', 'MimeType', '', 'import', '=');
						$this->Context->Database->Delete($s, $this->Name, 'ImportAttachments', 'An error occured while deleting imported attachments.');

						$AttachmentManager = $this->Context->ObjectFactory->NewContextObject($this->Context, 'AttachmentManager');
						$Attachment = $this->Context->ObjectFactory->NewObject($this->Context, 'Attachment');
						$ImportPath = ForceIncomingString('AttachmentImportPath', '');
						foreach( glob($ImportPath.'*.*') as $FilePath ) {
							$FileName = basename($FilePath);
							$CommentID = ForceInt(substr($FileName, 0, strpos($FileName, '.')), 0);
							if( $CommentID > 0 ) {
								$Attachment->Clear();
								$Attachment->CommentID = $CommentID;
								$s->Clear();
								$s->SetMainTable('Comment', 'c');
								$s->AddSelect(array('DiscussionID', 'AuthUserID'), 'c');
								$s->AddWhere('c', 'CommentID', '', $CommentID, '=');
								$ResultSet = $this->Context->Database->Select($s, $this->Name, 'ImportAttachments', 'An error occurred while attempting to retrieve the requested comment.');
								while ($Row = $this->Context->Database->GetRow($ResultSet)) {
									$Attachment->DiscussionID = ForceInt(@$Row['DiscussionID'], 0);
									$Attachment->UserID = ForceInt(@$Row['AuthUserID'], 0);
								}
								$Attachment->Title = $FileName;
								$Attachment->Name = $FileName;
								$Attachment->Path = $FilePath;
								$Attachment->Size = filesize($FilePath);
								$Attachment->MimeType = 'import';
								$Attachment->DateCreated = MySqlDateTime();
								$Attachment->DateModified = MySqlDateTime();

								$AttachmentManager->SaveAttachment($Attachment);
							}
						}

						// Display normal settings form
						$this->PostBackAction = 'Attachments';
					}
				}
				$this->CallDelegate('Constructor');
			}

			function Render() {
				if ($this->IsPostBack) {
					$this->CallDelegate('PreRender');
					$this->PostBackParams->Clear();
					if ($this->PostBackAction == "Attachments") {
						$FileTypes = $this->Context->Configuration['ATTACHMENTS_ALLOWED_FILETYPES'];
						$this->PostBackParams->Set('PostBackAction', 'ProcessAttachments');
						$this->PostBackParams->Set('LabelValuePairCount', (count($FileTypes) > 0 ? count($FileTypes) : 1), 1, 'LabelValuePairCount');
						echo '
						<div id="Form" class="Account AttachmentSettings">';
						if (ForceIncomingInt('Success', 0)) echo '<div id="Success">'.$this->Context->GetDefinition('ChangesSaved').'</div>';
						echo '
						<fieldset>
							<legend>'.$this->Context->GetDefinition("AttachmentSettings").'</legend>
							'.$this->Get_Warnings().'
							'.$this->Get_PostBackForm('frmAttachments').'
							<p>'.$this->Context->GetDefinition("AttachmentUploadSettingsInfo").'</p>
							<ul>
								<li>
									<label for="txtUploadPath">'.$this->Context->GetDefinition("UploadPath").'</label>
									<input type="text" name="ATTACHMENTS_UPLOAD_PATH" id="txtUploadPath"  value="'.$this->ConfigurationManager->GetSetting('ATTACHMENTS_UPLOAD_PATH').'" maxlength="200" class="SmallInput" style="width: 100%;" />
								</li>
								<li>
									<label for="txtMaximumFilesize">'.$this->Context->GetDefinition("MaximumFilesize").'</label>
									<input type="text" name="ATTACHMENTS_MAXIMUM_FILESIZE" id="txtMaximumFilesize"  value="'.$this->ConfigurationManager->GetSetting('ATTACHMENTS_MAXIMUM_FILESIZE').'" maxlength="200" class="SmallInput" />
								</li>
							</ul>
							';
						$this->CallDelegate('PreButtonsRender');
						echo '
							<div class="Submit">
								<input type="submit" name="btnSave" value="'.$this->Context->GetDefinition('Save').'" class="Button SubmitButton" />
								<a href="'.GetUrl($this->Context->Configuration, $this->Context->SelfUrl).'" class="CancelButton">'.$this->Context->GetDefinition('Cancel').'</a>
							</div>
						';
						$this->CallDelegate('PostButtonsRender');
						echo '
							</form>
						</fieldset>
						</div>';

						
						$this->PostBackParams->Clear();
						$this->PostBackParams->Set('PostBackAction', 'ImportAttachments');
						$ImportPath = ForceIncomingString('AttachmentImportPath', '');
						if( $ImportPath == '' && array_key_exists('ATTACHMENT_UPLOAD_PATH', $this->Context->Configuration)) {
							$ImportPath = $this->Context->Configuration['ATTACHMENT_UPLOAD_PATH'];
						}
						echo '
						<div id="Form" class="Account AttachmentSettings">
						<fieldset>
							<legend>'.$this->Context->GetDefinition("AttachmentImport").'</legend>
							'.$this->Get_PostBackForm('frmImportAttachments').'
							<p>'.$this->Context->GetDefinition("AttachmentImportNotes").'</p>
							<ul>
								<li>
									<label for="txtImportPath">'.$this->Context->GetDefinition("AttachmentImportPath").'</label>
									<input type="text" name="AttachmentImportPath" id="txtImportPath"  value="'.$ImportPath.'" maxlength="200" class="SmallInput" style="width: 100%;" />
								</li>
							</ul>
							<div class="Submit">
								<input type="submit" name="btnSave" value="'.$this->Context->GetDefinition('AttachmentImport').'" class="Button SubmitButton" />
								<a href="'.GetUrl($this->Context->Configuration, $this->Context->SelfUrl).'" class="CancelButton">'.$this->Context->GetDefinition('Cancel').'</a>
							</div>
							</form>
						</fieldset>
						</div>
						';
					}
				}
				$this->CallDelegate('PostRender');
			}
		}

		$AttachmentsForm = $Context->ObjectFactory->NewContextObject($Context, 'AttachmentsForm');
		$Page->AddRenderControl($AttachmentsForm, $Configuration["CONTROL_POSITION_BODY_ITEM"] + 1);

		$ExtensionOptions = $Context->GetDefinition("ExtensionOptions");
		$Panel->AddList($ExtensionOptions, 20);
		$Panel->AddListItem($ExtensionOptions, $Context->GetDefinition("AttachmentSettings"), GetUrl($Context->Configuration, 'settings.php', '', '', '', '', 'PostBackAction=Attachments'));

	}

	if (in_array($Context->SelfUrl, array('comments.php', 'post.php')) ) {

		$Head->AddStyleSheet('extensions/Attachments/style.css');
		$Head->AddScript('extensions/Attachments/functions.js');

		// Init AttachmentManager for the discussion form
		function DiscussionForm_InitAttachmentManager(&$DiscussionForm) {
			$AttachmentManager = $DiscussionForm->Context->ObjectFactory->NewContextObject($DiscussionForm->Context, 'AttachmentManager');
			$DiscussionForm->DelegateParameters['AttachmentManager'] = &$AttachmentManager;
		}

		// Init AttachmentManager for the comment grid
		function CommentGrid_InitAttachmentManager(&$Control) {
			$AttachmentManager = $Control->Context->ObjectFactory->NewContextObject($Control->Context, 'AttachmentManager');
			$Control->DelegateParameters['AttachmentManager'] = &$AttachmentManager;
			$AttachmentManager->RetrieveAttachments();
		}

		// Add attachment control to the comment form
		function CommentForm_AddAttachmentForm(&$CommentForm) {
			$AttachmentManager = &$CommentForm->DelegateParameters['AttachmentManager'];
			$AttachmentManager->FormName = 'frmPostComment';
			$AttachmentManager->CommentID = &$CommentForm->Comment->CommentID;
			$AttachmentManager->Render_Attachments();
			$AttachmentManager->Render_AttachmentForm();
		}

		// Add attachment control to the discussion form
		function DiscussionForm_AddAttachmentForm(&$DiscussionForm) {
			$AttachmentManager = &$DiscussionForm->DelegateParameters['AttachmentManager'];
			$AttachmentManager->FormName = 'frmPostDiscussion';
			$AttachmentManager->CommentID = &$DiscussionForm->Comment->CommentID;
			$AttachmentManager->Render_Attachments();
			$AttachmentManager->Render_AttachmentForm();
		}

		// Render Attachments
		function CommentGrid_RenderAttachments(&$CommentGrid) {
			$AttachmentManager = &$CommentGrid->DelegateParameters['AttachmentManager'];
			$AttachmentManager->Comment = &$CommentGrid->DelegateParameters['Comment'];
			$AttachmentManager->Render_Attachments();
		}

		// Upload Attachments
		function DiscussionForm_UploadAttachments(&$DiscussionForm) {
			$AttachmentManager = &$DiscussionForm->DelegateParameters['AttachmentManager'];
			$AttachmentManager->UploadAttachments();
		}

		// Save Attachments
		function DiscussionForm_SaveCommentAttachments(&$DiscussionForm) {
			$Comment = &$DiscussionForm->DelegateParameters['ResultComment'];
			$AttachmentManager = &$DiscussionForm->DelegateParameters['AttachmentManager'];
			$AttachmentManager->Comment = &$Comment;
			$AttachmentManager->SaveAttachments();
		}

		// Save Attachments
		function DiscussionForm_SaveDiscussionAttachments(&$DiscussionForm) {
			$Discussion = &$DiscussionForm->DelegateParameters['ResultDiscussion'];
			$AttachmentManager = &$DiscussionForm->DelegateParameters['AttachmentManager'];
			$AttachmentManager->DiscussionID = $Discussion->DiscussionID;
			$AttachmentManager->Comment = &$Discussion->Comment;
			$AttachmentManager->SaveAttachments();
		}

		// Init AttachmentManager for discussion form
		$Context->AddToDelegate("DiscussionForm",
								"PreLoadData",
								"DiscussionForm_InitAttachmentManager");

		// Init AttachmentManager for comment grid
		$Context->AddToDelegate("CommentGrid",
								"Constructor",
								"CommentGrid_InitAttachmentManager");

		// Display attachments at comments	
		$Context->AddToDelegate("CommentGrid",
								"PreCommentOptionsRender",
								"CommentGrid_RenderAttachments");

		if( $Context->Session->User->Permission('PERMISSION_ADD_ATTACHMENTS') || 
			$Context->Session->User->Permission('PERMISSION_MANAGE_ATTACHMENTS') ) {

			// Add control to discussion form
			$Context->AddToDelegate("DiscussionForm",
									"DiscussionForm_PreButtonsRender",
									"DiscussionForm_AddAttachmentForm");

			// Add control to comment form
			$Context->AddToDelegate("DiscussionForm",
									"CommentForm_PreButtonsRender",
									"CommentForm_AddAttachmentForm");

			// Upload files on PreSaveComment
			$Context->AddToDelegate("DiscussionForm",
									"PreSaveComment",
									"DiscussionForm_UploadAttachments");

			// Upload files on PreSaveDiscussion
			$Context->AddToDelegate("DiscussionForm",
									"PreSaveDiscussion",
									"DiscussionForm_UploadAttachments");

			// Save files on PostSaveComment
			$Context->AddToDelegate("DiscussionForm",
									"PostSaveComment",
									"DiscussionForm_SaveCommentAttachments");

			// Save files on PostSaveDiscussion
			$Context->AddToDelegate("DiscussionForm",
									"PostSaveDiscussion",
									"DiscussionForm_SaveDiscussionAttachments");
		}
	}

	// Handle downloads
	if( ForceIncomingString('PostBackAction', '') == 'Download' ) {
		$AttachmentID = ForceIncomingInt('AttachmentID', 0);
		$AttachmentManager = $Context->ObjectFactory->NewContextObject($Context, 'AttachmentManager');
		$AttachmentManager->DownloadAttachment($AttachmentID);
	}

	// Remind user to set permissions
	if ($Context->SelfUrl == 'index.php' && !array_key_exists('ATTACHMENTS_NOTICE', $Configuration)) {
		if ($Context->Session->User && $Context->Session->User->Permission('PERMISSION_MANAGE_EXTENSIONS')) {
			$HideNotice = ForceIncomingBool('TurnOffAttachmentsNotice', 0);
			if ($HideNotice) {
				AddConfigurationSetting($Context, 'ATTACHMENTS_NOTICE', '1');
			} else {
				$NoticeCollector->AddNotice('<span><a href="'.GetUrl($Configuration, 'index.php', '', '', '', '', 'TurnOffAttachmentsNotice=1').'">'.$Context->GetDefinition('RemoveThisNotice').'</a></span>
					'.$Context->GetDefinition('RememberToSetAttachmentsPermissions'));
			}
		}
	}

}
?>
