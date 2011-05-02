<?php
// Configure
$response = array('top' => null, 'middle' => null, 'bottom' => null);
$store = sprintf('%s/images', dirname(__FILE__));

// Get images list
$imagesList = array(
	'top'    => glob(sprintf('%s/parts/1/*.png', $store)),
	'middle' => glob(sprintf('%s/parts/2/*.png', $store)),
	'bottom' => glob(sprintf('%s/parts/3/*.png', $store))
);

// Select random images
$response = array(
	'top'    => basename($imagesList['top'][array_rand($imagesList['top'])]),
	'middle' => basename($imagesList['middle'][array_rand($imagesList['middle'])]),
	'bottom' => basename($imagesList['bottom'][array_rand($imagesList['bottom'])])
);

// Build response body
$responseJson = json_encode($response);

// Output response headers
header('Content-Type: application/json');
header(sprintf('Content-Length: %d', strlen($responseJson)));

// Output response body
echo $responseJson;

// Exit successfully
exit(0);