<?php
/*
Extension Name: Multi File Upload
Extension Url: http://vanillaforums.org/addon/239/multi-file-upload
Description: Requires the Attachments extension. Allows users to add multiple attachments to each comment.
Version: 1.1
Author: Maurice (Jazzman) Krijtenberg
Author Url: http://www.krijtenberg.nl

Changes

Version 1.1 - 08.03.2007, Jazzman
- Made attachments expandible so the file upload is hidden at first
- When MULTI_FILE_UPLOADS is set to 1, it will not show the multi file list
*/

$Context->Configuration['MULTI_FILE_UPLOADS'] = '6';


if (in_array($Context->SelfUrl, array('comments.php', 'post.php')) && $Context->Session->UserID > 0 ) {

	if (ForceInt($Context->Configuration['MULTI_FILE_UPLOADS'], 0) > 1) {
		$Head->AddScript('extensions/MultiFileUpload/multifile.js');
	}

	function MultiFileUpload_AttachmentForm(&$AttachmentManager) {
		$AttachmentForm = &$AttachmentManager->DelegateParameters['AttachmentForm'];
		$AttachmentForm = '
			<ul><li><label for="Attachments"><span onclick="showMultiFileUpload(); return false;" style="cursor: pointer;" id="AttachmentsLabel">[+] '. $AttachmentManager->Context->GetDefinition("Attachments").'</span></label>
			'.$AttachmentManager->GetAttachmentsList($AttachmentManager->CommentID).'
			<div id="MultiFileUpload" style="display: none;">
				<div id="AttachmentsList"></div>
				<input id="AttachmentFile" type="file" name="file" class="AttachmentInput" />
			</div>
			</li></ul>
			<script type="text/javascript" language="javascript">
				function showMultiFileUpload()
				{
					var txtAttachments = "'.$AttachmentManager->Context->GetDefinition("Attachments").'";
					var objAttachmentsLabel = document.getElementById("AttachmentsLabel");
					var objMultiFileUpload  = document.getElementById("MultiFileUpload");
					if (objAttachmentsLabel.innerHTML == "[-] " + txtAttachments)
					{
						objAttachmentsLabel.innerHTML = "[+] " + txtAttachments;
						objMultiFileUpload.style.display = "none";
					}
					else
					{
						objAttachmentsLabel.innerHTML = "[-] " + txtAttachments;
						objMultiFileUpload.style.display = "block";
					}
				}
				var f = document.getElementById(\''. $AttachmentManager->FormName .'\');
				f.encoding = \'multipart/form-data\';
			';
			if (ForceInt($AttachmentManager->Context->Configuration['MULTI_FILE_UPLOADS'], 0) > 1) {
				$AttachmentForm .= '
				var multi_selector = new MultiSelector(document.getElementById("AttachmentsList"), '. $AttachmentManager->Context->Configuration['MULTI_FILE_UPLOADS'] .');
				multi_selector.addElement(document.getElementById("AttachmentFile"));
				';
			}
			$AttachmentForm .= '
			</script>
		';
	}

	$Context->AddToDelegate('AttachmentManager', 'PreRender_AttachmentForm', 'MultiFileUpload_AttachmentForm');
}
?>