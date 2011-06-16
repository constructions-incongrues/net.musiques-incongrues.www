<?php
// List emoticons categories and images
$sourceDir = dirname(__FILE__).'/smilies';
$categoriesPaths = glob($sourceDir.'/*');
$categories = array();
foreach ($categoriesPaths as $categoryPath) {
	$smiliesPaths = glob($categoryPath.'/*.*');
	$smilies = array();
	foreach ($smiliesPaths as $smiliePath) {
		$smilies[] = array('name' => basename($smiliePath), 'path' => $smiliePath);
	}
	
	$categories[] = array('name' => basename($categoryPath), 'path' => $categoryPath, 'smilies' => $smilies);
	
}

// Rebuild files
// -- js
$tplJs = "arrSmilies[\"%s\"][%s] = '<span onclick=\"insertSmilie(\'%s\');\" class=\"VanillaconsLink\"><img src=\"@PATHS.BASEURI@extensions/Vanillacons/smilies/%s/%s\" /></span>';";
$tplPhp = '$Smilies["%s"] = "@PATHS.BASEURI@extensions/Vanillacons/smilies/%s/%s";';
$php = array('<?php');
$js = array('var arrSmilies = [];');
foreach ($categories as $category) {
	$i = 0;
	$js[] = sprintf('arrSmilies["%s"] = [];', $category['name']);
	foreach ($category['smilies'] as $smiley) {
		$js[] = sprintf($tplJs, $category['name'], $i, $smiley['name'], $category['name'], basename($smiley['path']));
		$php[] = sprintf($tplPhp, $smiley['name'], $category['name'], basename($smiley['path']));
		$i++;
	}
}
$php[] = sprintf('$Configuration["SMILIES_CATEGORIES"] = %d;', count($category));
file_put_contents(dirname(__FILE__).'/smilies.js-dist', implode("\n", $js));
file_put_contents(dirname(__FILE__).'/smilies.php-dist', implode("\n", $php));