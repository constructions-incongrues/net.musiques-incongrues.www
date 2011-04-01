<?php
/*
 Extension Name: MiOpengrah
 Extension Url: https://github.com/contructions-incongrues
 Description: Sets Opengraph metadata whenever appropriate
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */

// Helpers
function callService($segment, $filterFieldName, $filterFieldValue, $sortField = 'contributed_at', $sortDirection = 'desc') {
	$url = sprintf('http://data.musiques-incongrues.net/collections/links/segments/%s/get?format=json&%s=%s&sort_field=%s&sort_direction=%s&limit=1', $segment, $filterFieldName, $filterFieldValue, $sortField, $sortDirection);
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$response = json_decode(curl_exec($curl), true);
	return $response;
}

// Holds all opengraph meta tags for current request
$ogMetaTags = array();

// Defaults
$ogMetaTags['title'] = 'Le forum des Musiques Incongrues';
$ogMetaTags['url'] = sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']);
$ogMetaTags['description'] = "Un forum où l'on parle musiques décalées, électroniques ou pas, c'est aussi un agenda de sorties, une radio et télé incongrues. Plus largement, une base de données où vous trouverez une myriade d'images et de videos, des infos sur la culture 'undergound' et 'overground', mais aussi tout ce qui est incongru en général.";
$ogMetaTags['site_name'] = 'Musiques Incongrues';

// Discussion related meta data
if ($Context->SelfUrl == 'comments.php') {
	// Fetch current discussion
  	$discussion = mysql_fetch_assoc($Context->Database->Execute('SELECT * FROM LUM_Discussion WHERE LUM_Discussion.DiscussionID = '.ForceIncomingInt("DiscussionID", 0).';', '', '', '', ''));
	
	// Update metadata according to current discussion
	$ogMetaTags['title'] = $discussion['Name'];
	
	// Look for an image
	$imagesDiscussion = callService('images', 'discussion_id', ForceIncomingInt('DiscussionID', 0), 'contributed_at', 'asc');
	if (is_array($imagesDiscussion) && $imagesDiscussion['num_found'] > 0) {
		$ogMetaTags['image'] = $imagesDiscussion[0]['url'];
	}
	
	// Check if discussion holds any link to an MP3 file
	$mp3sDiscussion = callService('mp3', 'discussion_id', ForceIncomingInt('DiscussionID', 0), 'contributed_at', 'asc');
	if (is_array($mp3sDiscussion) && $mp3sDiscussion['num_found'] > 0) {
		$ogMetaTags['type'] = 'song';
		$ogMetaTags['audio'] = $mp3sDiscussion[0]['url'];
		$ogMetaTags['audio:title'] = $mp3sDiscussion[0]['discussion_name'];
		$ogMetaTags['audio:type'] = 'application/mp3';
		// Those seem to be required. See http://developers.facebook.com/docs/opengraph/
		$ogMetaTags['audio:artist'] = 'Unknown artist';
		$ogMetaTags['audio:album'] = 'Unknown album';
	}
	
	// If discussion relates to an event, add location metadata
	$event = mysql_fetch_assoc($Context->Database->Execute('SELECT * FROM LUM_Event WHERE LUM_Event.DiscussionID = '.ForceIncomingInt("DiscussionID", 0).';', '', '', '', ''));
	if ($event) {
		$ogMetaTags['locality'] = $event['City'];
		$ogMetaTags['country-name'] = $event['Country'];
	}	
}

// Add meta tags to header
foreach ($ogMetaTags as $name => $value) {
	$Head->AddString(sprintf('<meta property="og:%s" content="%s" />'."\n", $name, $value));
}

