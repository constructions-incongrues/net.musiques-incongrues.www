<?php
$imageParts = array(
	sprintf('%s/images/parts/1/%s', dirname(__FILE__), filter_var($_GET['part1'])),
	sprintf('%s/images/parts/2/%s', dirname(__FILE__), filter_var($_GET['part2'])),
	sprintf('%s/images/parts/3/%s', dirname(__FILE__), filter_var($_GET['part3'])),
);

// Create images
$image1 = imagecreatefrompng($imageParts[0]);
$image2 = imagecreatefrompng($imageParts[1]);
$image3 = imagecreatefrompng($imageParts[2]);
$imageFull = imagecreatetruecolor(800, 600);

// Merge images
imagecopy($imageFull, $image1, 0, 0, 0, 0, 800, 200);
imagecopy($imageFull, $image2, 0, 200, 0, 0, 800, 200);
imagecopy($imageFull, $image3, 0, 400, 0, 0, 800, 200);

// Serve image
header('Content-Type: image/png');
header(sprintf('Content-Disposition: attachment; filename="identite-incongrues_%s.png"', uniqid()));
imagepng($imageFull);

imagedestroy($imageFull);
imagedestroy($image1);
imagedestroy($image2);
imagedestroy($image3);

exit(0);