<?php
/*
Extension Name: Inline Images
Extension Url: http://vanillaforums.org/addon/154/inline-images
Description: Requires the Attachments extension. Displays attached images instead of linking to them.
Version: 1.4
Author: Maurice (Jazzman) Krijtenberg
Author Url: http://www.krijtenberg.nl

Changes

Version 1.1 - 12.10.2006, Jazzman
- Added caching for inline images

Version 1.2 - 04.12.2006, Jazzman
- Added security patch for Vanilla 1.0.3

Version 1.3 - 11.01.2006, Jazzman
- Removed security patch again, as it was not neccesary and resulted in errors
- Added ThickBox support using the JQuery and JQThickBox extension from Stash

Version 1.4 - 27.04.2011, SubJunk
- Added support for the HtmlFormatter 2.5+ dynamic image resizing feature
- Increased default maximum width
*/

$Context->Dictionary['ExtensionOptions'] = 'Extension Options';
$Context->Dictionary['InlineImages'] = 'Inline Images';
$Context->Dictionary['InlineImagesNotes'] = 'With Inline Images you can display image attachments directly into your comments by using [Image_%AttachmentID%].<br>If an image width exceeds the maximum width it will be automatically resized.';
$Context->Dictionary['InlineImagesMaxWidth'] = 'Maximum Width';
$Context->Dictionary['UseThickBox'] = 'Use ThickBox (Requires JQuery and JQThickBox extensions!)';

// Maximum width of inline images
if( !array_key_exists('INLINEIMAGES_MAX_WIDTH', $Configuration)) {
	AddConfigurationSetting($Context, 'INLINEIMAGES_MAX_WIDTH', '5120');
}
// ThickBox Support
if( !array_key_exists('INLINEIMAGES_USE_THICKBOX', $Configuration)) {
	AddConfigurationSetting($Context, 'INLINEIMAGES_USE_THICKBOX', '0');
}

if (in_array($Context->SelfUrl, array('comments.php', 'post.php')) ) {

	function InlineImages_RenderAttachment(&$AttachmentManager) {
		$Attachment     = &$AttachmentManager->DelegateParameters['Attachment'];
		$AttachmentBody = &$AttachmentManager->DelegateParameters['AttachmentBody'];
		$Comment        = &$AttachmentManager->DelegateParameters['Comment'];

		// Locate the image and check if it's an image file
		if( file_exists( $Attachment->Path ) && in_array($Attachment->Extension, array('jpg', 'jpeg', 'gif', 'png', 'bmp')) ) {

			// Init variables
			$WebRoot   = $AttachmentManager->Context->Configuration['WEB_ROOT'];
			$ImageTag  = '[Image_'.$Attachment->AttachmentID .']';
			global $ImageBody;
			$ImageBody = '<a href="'.$WebRoot.'extensions/InlineImages/image.jpg.php?AttachmentID='.$Attachment->AttachmentID.'">';
			$ImageBody .= '<img src="'.$WebRoot.'extensions/InlineImages/image.php?AttachmentID='.$Attachment->AttachmentID.'" alt="'.$Attachment->Name.'" class="InlineImage" />';
			$ImageBody .= '</a><br />';

			// ThickBox
			if ($AttachmentManager->Context->Configuration['INLINEIMAGES_USE_THICKBOX'] == '1') {
				$ImageBody = '<a href="'.$WebRoot.'extensions/InlineImages/image.jpg.php?AttachmentID='.$Attachment->AttachmentID.'" class="thickbox" rel="Comment_'.$Attachment->CommentID.'">'. $ImageBody .'</a>';
			}

			// Check if it's used in the comment body
			if( strpos($Comment->Body, $ImageTag) > 0 ) {
				$Comment->Body = str_replace($ImageTag, $ImageBody, $Comment->Body);
			} else {
				$AttachmentBody .= $ImageBody;
			}

			// We don't want this attachment to be processed
			// by other renderers, so we clear the attachment object
			$AttachmentManager->DelegateParameters['Attachment'] = false;
		}
	}

	function InlineImages_AttachmentsListItem(&$AttachmentManager) {
		$Attachment = $AttachmentManager->DelegateParameters['Attachment'];
		$AttachmentList = &$AttachmentManager->DelegateParameters['AttachmentList'];

		$AttachmentList .= '<li id="Attachment_'.$Attachment->AttachmentID.'">['. $Attachment->AttachmentID .'] '. $Attachment->Name .' ';
		if( $Attachment->UserID == $AttachmentManager->Context->Session->UserID || $AttachmentManager->Context->Session->User->Permission('PERMISSION_MANAGE_ATTACHMENTS') ) {
			$AttachmentList .= '<a href="./" onclick="DeleteAttachment(\''. $AttachmentManager->Context->Configuration['WEB_ROOT'] . "extensions/Attachments/ajax.php" .'\', \''. $Attachment->AttachmentID .'\'); return false;">'. $AttachmentManager->Context->GetDefinition('DeleteAttachment') .'</a>';
		}
		$AttachmentList .= '</li>';

		// We don't want this attachment to be processed
		// by other renderers, so we clear the attachment object
		$AttachmentManager->DelegateParameters['Attachment'] = false;
	}


	$Context->AddToDelegate('AttachmentManager',
		'PreRender_Attachment',
		'InlineImages_RenderAttachment');

	$Context->AddToDelegate('AttachmentManager',
		'AttachmentsListItem',
		'InlineImages_AttachmentsListItem');


}

if ($Context->SelfUrl == "settings.php" && $Context->Session->User->Permission('PERMISSION_MANAGE_ATTACHMENTS')) {

	class InlineImagesForm extends PostBackControl {
		var $ConfigurationManager;

		function InlineImagesForm(&$Context) {
			$this->Name = 'InlineImagesForm';
			$this->ValidActions = array('InlineImages', 'ProcessInlineImages');
			$this->Constructor($Context);
			if (!$this->Context->Session->User->Permission('PERMISSION_MANAGE_ATTACHMENTS')) {
				$this->IsPostBack = 0;
			} elseif( $this->IsPostBack ) {
				$SettingsFile = $this->Context->Configuration['APPLICATION_PATH'].'conf/settings.php';
				$this->ConfigurationManager = $this->Context->ObjectFactory->NewContextObject($this->Context, 'ConfigurationManager');
				if ($this->PostBackAction == 'ProcessInlineImages') {
					$this->ConfigurationManager->GetSettingsFromForm($SettingsFile);
					$this->ConfigurationManager->DefineSetting('INLINEIMAGES_USE_THICKBOX', ForceIncomingBool('INLINEIMAGES_USE_THICKBOX', 0), 0);
					if ($this->ConfigurationManager->SaveSettingsToFile($SettingsFile)) {
						header('Location: '.GetUrl($this->Context->Configuration, 'settings.php', '', '', '', '', 'PostBackAction=InlineImages&Success=1'));
					} else {
						$this->PostBackAction = 'InlineImages';
					}
				}
			}
			$this->CallDelegate('Constructor');
		}

		function Render() {
			if ($this->IsPostBack) {
				$this->CallDelegate('PreRender');
				$this->PostBackParams->Clear();
				if ($this->PostBackAction == 'InlineImages') {
					$this->PostBackParams->Set('PostBackAction', 'ProcessInlineImages');
					echo '
					<div id="Form" class="Account InlineImagesSettings">';
					if (ForceIncomingInt('Success', 0)) echo '<div id="Success">'.$this->Context->GetDefinition('ChangesSaved').'</div>';
					echo '
						<fieldset>
							<legend>'.$this->Context->GetDefinition("InlineImages").'</legend>
							'.$this->Get_Warnings().'
							'.$this->Get_PostBackForm('frmInlineImages').'
							<p>'.$this->Context->GetDefinition("InlineImagesNotes").'</p>
							<ul>
								<li>
									<label for="txtMaxWidth">'.$this->Context->GetDefinition("InlineImagesMaxWidth").'</label>
									<input type="text" name="INLINEIMAGES_MAX_WIDTH" id="txtMaxWidth"  value="'.$this->ConfigurationManager->GetSetting('INLINEIMAGES_MAX_WIDTH').'" maxlength="10" class="SmallInput" />
								</li>
								<li>
									<p><span>'.GetDynamicCheckBox('INLINEIMAGES_USE_THICKBOX', 1, $this->ConfigurationManager->GetSetting('INLINEIMAGES_USE_THICKBOX'), '', $this->Context->GetDefinition('UseThickBox')).'</span></p>
								</li>
							</ul>
							<div class="Submit">
								<input type="submit" name="btnSave" value="'.$this->Context->GetDefinition('Save').'" class="Button SubmitButton" />
								<a href="'.GetUrl($this->Context->Configuration, $this->Context->SelfUrl).'" class="CancelButton">'.$this->Context->GetDefinition('Cancel').'</a>
							</div>
							</form>
						</fieldset>
					</div>
					';
				}
				$this->CallDelegate('PostRender');
			}
		}
	}

	$InlineImagesForm = $Context->ObjectFactory->NewContextObject($Context, 'InlineImagesForm');
	$Page->AddRenderControl($InlineImagesForm, $Configuration["CONTROL_POSITION_BODY_ITEM"] + 1);

	$ExtensionOptions = $Context->GetDefinition('ExtensionOptions');
	$Panel->AddList($ExtensionOptions, 20);
	$Panel->AddListItem($ExtensionOptions, $Context->GetDefinition('InlineImages'), GetUrl($Context->Configuration, 'settings.php', '', '', '', '', 'PostBackAction=InlineImages'));
}
?>
