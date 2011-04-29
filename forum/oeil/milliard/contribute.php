<?php
$feedback = null;
$store = dirname(__FILE__).'/images'; 
if (isset($_FILES['file'])) {
	try {
	// Sanity checks
	// -- File type
	if ($_FILES['file']['type'] != 'image/png') {
		throw new InvalidArgumentException('Image must be of type "image/png"', 400);
	}
	// -- File size
	$imageSize = getimagesize($_FILES['file']['tmp_name']);
	if ($imageSize[0] !== 800 || $imageSize[1] !== 600) {
		throw new InvalidArgumentException(sprintf('Image dimensions must be 800x600 (uploaded image dimensions : %s)', $imageSize[3]), 400);
	}
	
	// Build image name
	$imageName = uniqid('millemilliard_');
	
	// Cut image
	$image = imagecreatefrompng($_FILES['file']['tmp_name']);
	$imageTop = imagecreatetruecolor(800, 200);
	$imageMiddle = imagecreatetruecolor(800, 200);
	$imageBottom = imagecreatetruecolor(800, 200);
    imagecopyresampled($imageTop, $image, 0, 0, 0, 0, 800, 200, 800, 200);
    imagecopyresampled($imageMiddle, $image, 0, 0, 0, 200, 800, 200, 800, 200);
    imagecopyresampled($imageBottom, $image, 0, 0, 0, 400, 800, 200, 800, 200);
	
	// Store files
	move_uploaded_file($_FILES['file']['tmp_name'], sprintf('%s/full/%s.png', $store, $imageName));
	imagepng($imageTop, sprintf('%s/parts/1/%s_part_1.png', $store, $imageName));
	imagepng($imageMiddle, sprintf('%s/parts/2/%s_part_2.png', $store, $imageName));
	imagepng($imageBottom, sprintf('%s/parts/3/%s_part_3.png', $store, $imageName));
	
	// Free resources
	imagedestroy($image);
	imagedestroy($imageTop);
	imagedestroy($imageMiddle);
	imagedestroy($imageBottom);
	
	// User feedback
	$feedback = 'Image successfully uploaded and sliced. Thanks !';
	} catch (Exception $e) {
		$feedback = $e->getMessage();
	}
}
?>
<!DOCTYPE html>
<html>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Mille Milliard de Hasard - Contribution - Musiques Incongrues</title>
	</head>
	
	<body>
		<p>L'image doit être au format PNG et avoir pour dimensions 800x600 pixels.</p>
<?php if($feedback): ?>
		<p><?php echo $feedback ?></p>
<?php endif; ?>
		<form method="post" enctype="multipart/form-data">
			<input type="file" name="file" size="30" />
			<input type="submit" name="upload" value="Contribuer" />
		</form>
	</body>

</html>