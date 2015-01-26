<?php
include("../../appg/settings.php");
include("../../conf/settings.php");
include("../../appg/init_ajax.php");

// Include Attachments extension, because we need the classes
include($Configuration['APPLICATION_PATH'].'extensions/Attachments/default.php');

$Action = ForceIncomingString('Action', '');
$AttachmentID = ForceIncomingInt('AttachmentID', 0);

if( $AttachmentID > 0 && $Action == 'RemoveAttachment' ) {
	$AttachmentManager = $Context->ObjectFactory->NewContextObject($Context, 'AttachmentManager');
	$Attachment = $AttachmentManager->GetAttachmentById($AttachmentID);
	if ($Attachment) {
		if( $Attachment->UserID == $Context->Session->UserID || $Context->Session->User->Permission('PERMISSION_MANAGE_ATTACHMENTS') ) {
			// Delete the file
			@unlink($Attachment->Path);
			// Delete from database
			$AttachmentManager->RemoveAttachment($AttachmentID);
		}
	}
}
echo 'Complete';
$Context->Unload();
?>