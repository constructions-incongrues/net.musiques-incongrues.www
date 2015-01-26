<?php
// Image Processor
// Based on ZenPhoto's image processor script
include('../../appg/settings.php');
include('../../appg/init_ajax.php');

// Load the core attachment extension and this one
include($Context->Configuration['EXTENSIONS_PATH'].'Attachments/default.php');
include($Context->Configuration['EXTENSIONS_PATH'].'InlineImages/default.php');

function LoadImage($ImagePath) {
	$Extension = strtolower(substr(strrchr($ImagePath, "."), 1));
	if ($Extension == "jpg" || $Extension == "jpeg") {
		return imagecreatefromjpeg($ImagePath);
	} else if ($Extension == "gif") {
		return imagecreatefromgif($ImagePath);
	} else if ($Extension == "png") {
		return imagecreatefrompng($ImagePath);
	} else {
		return false;
	}
}

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
	$MaxWidth = $Context->Configuration['INLINEIMAGES_MAX_WIDTH'];

	// Check if image width exceeds the maximum width
	list($Width, $Height, $Type, $Attr) = @getimagesize($Attachment->Path);
	if( $Width > $MaxWidth ) {

		// Our new width is the maximum width
		$NewWidth = $MaxWidth;
		$Quality  = 100;

		// Change image path to show resized image
		$ImagePath = basename($Attachment->Path);
		$ImagePath = str_replace($ImagePath, '', $Attachment->Path);
		$ImagePath = $ImagePath . 'resized'. $MaxWidth .'-'. basename($Attachment->Path) . '.jpg';

		// Check if there's already a resized image, if not create one
		if( !file_exists( $ImagePath )) {
			if ($OriginalImage  = LoadImage($Attachment->Path)) {
				$OriginalWidth  = ImageSX($OriginalImage);
				$OriginalHeight = ImageSY($OriginalImage);

				$NewHeight = Round($OriginalHeight * $NewWidth) / $OriginalWidth;
				$NewImage  = imagecreatetruecolor($NewWidth, $NewHeight);
				imagecopyresampled($NewImage, $OriginalImage, 0, 0, 0, 0, $NewWidth, $NewHeight, $OriginalWidth, $OriginalHeight);

				// Create the resize image file
				touch($ImagePath);
				imagejpeg($NewImage, $ImagePath, $Quality);
				chmod($ImagePath, 0644);
				imagedestroy($NewImage);
				imagedestroy($OriginalImage);

			} else {
				die('Error processing image!');
			}
		}
	}

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