<?php
// Image Processor for ThickBox to display original image
// Based on ZenPhoto's image processor script
include('../../appg/settings.php');
include('../../appg/init_ajax.php');

// Load the core attachment extension
include($Context->Configuration['EXTENSIONS_PATH'].'Attachments/default.php');

// Require Attachment ID
$AttachmentID = ForceIncomingInt('AttachmentID', 0);
if( $AttachmentID == 0 ) {
	die('Attachment ID not specified!');
}

// Retrieve attachment
$AttachmentManager = $Context->ObjectFactory->NewContextObject($Context, 'AttachmentManager');
$Attachment = $AttachmentManager->GetAttachmentById($AttachmentID);
if( $Attachment ) {

	// Init variables
	$ImagePath = $Attachment->Path;
	$Expires = 60 * 60 * 24 * 5; // 5 days
	$ExpireDateGMT = gmdate("D, d M Y H:i:s", time() + $Expires )." GMT";

	header("Content-Type: ".$Attachment->MimeType);
	header('Content-Disposition: inline; filename="' . $Attachment->Name . '"');
	header("Expires: ".$ExpireDateGMT);
	header("Last-Modified: ".$Attachment->DateModified);
	header("Cache-Control: public, max-age=".$Expires);
	header("Pragma: !invalid");
	header("Content-Control: cache");
	header("Content-Length: ".filesize($ImagePath));
	@readfile($ImagePath);
}
?>