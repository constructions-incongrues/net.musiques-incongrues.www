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
function ogCallService($segment, $filterFieldName, $filterFieldValue, $sortField = 'contributed_at', $sortDirection = 'desc') {
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
  	$discussion = mysql_fetch_assoc($Context->Database->Execute('SELECT d.Name, d.DiscussionID, c.Body FROM LUM_Discussion d INNER JOIN LUM_Comment c on c.DiscussionID = d.DiscussionID WHERE d.DiscussionID = '.ForceIncomingInt("DiscussionID", 0).';', '', '', '', ''));
	
	// Update metadata according to current discussion
	$ogMetaTags['title'] = $discussion['Name'];
	$ogMetaTags['description'] = substr($discussion['Body'], 0, 300) . '...';
	
	// Look for an image
	$ogMetaTags['image'] = $Context->ObjectFactory->NewContextObject($Context, 'DiscussionManager')->getDiscussionByID($discussion['DiscussionID'])->getFirstImage();
	
	// If it is a release, check if discussion holds any link to an MP3 file
	$release = mysql_fetch_assoc($Context->Database->Execute('Select LabelName, DownloadLink from LUM_Releases where DiscussionID = ' . $discussion['DiscussionID'], '', '', '', ''));
	if ($release) {
		$mp3sDiscussion = ogCallService('mp3', 'discussion_id', ForceIncomingInt('DiscussionID', 0), 'contributed_at', 'asc');
		if (is_array($mp3sDiscussion) && $mp3sDiscussion['num_found'] > 0) {
			$ogMetaTags['type'] = 'song';
			$ogMetaTags['audio'] = $mp3sDiscussion[0]['url'];
			$ogMetaTags['audio:title'] = $mp3sDiscussion[0]['discussion_name'];
			$ogMetaTags['audio:type'] = 'application/mp3';
			// Those seem to be required. See http://developers.facebook.com/docs/opengraph/
			if ($release['LabelName']) {
				$ogMetaTags['audio:artist'] = $release['LabelName'];
			}
			$ogMetaTags['audio:album'] = 'Unknown album';
		}
	}
	
	// If discussion relates to an event, add location metadata
	$event = mysql_fetch_assoc($Context->Database->Execute('SELECT * FROM LUM_Event WHERE LUM_Event.DiscussionID = '.ForceIncomingInt("DiscussionID", 0).';', '', '', '', ''));
	if ($event) {
		$ogMetaTags['locality'] = $event['City'];
		$ogMetaTags['country-name'] = $event['Country'];
	}	
} else if ($Context->SelfUrl == 'extension.php' && ForceIncomingString('PostBackAction', null) == 'Events') {
	$ogMetaTags['title'] = 'Musiques Incongrues - Agenda';
	if (ForceIncomingString('city', null)) {
		$ogMetaTags['title'] .= ' - ' . trim(ucfirst(ForceIncomingString('city', null)), '/');
	}
	
	$ogMetaTags['description'] = "Ce soir on sort : l'agenda du forum des Musiques Incongrues";
}

// Add meta tags to header
// TODO : enable delegation of OG tags settings in any extension
if (!array_intersect(explode('/', $_SERVER['REQUEST_URI']), array('shows', 'labels'))) {
	foreach ($ogMetaTags as $name => $value) {
		$Head->AddString(sprintf('<meta property="og:%s" content="%s" />'."\n", $name, addcslashes($value, '"')));
	}
}

