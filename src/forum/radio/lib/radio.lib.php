<?php
// Helpers
function guessTitle(array $link) {
	// Thanks to soundcloud url formalism, we can guess the track title
	if ($link['domain_fqdn'] == 'soundcloud.com') {
		$urlParts = array_reverse(explode('/', $link['url']));
		$link['title'] = sprintf('%s - %s', str_replace('-', ' ', ucfirst($urlParts[2])), str_replace('-', ' ', ucfirst($urlParts[1])));
	} else {
		$link['title'] = urldecode(basename($link['url'], '.mp3'));
		$link['title'] = urldecode($link['title']);
		$link['title'] = str_replace('_', ' ', $link['title']);
	}

	return $link['title'];
}

// Get informations about playlists
// TODO : move this to a dedicated extension
function callService($filterFieldName, $filterFieldValue) {
	$url = sprintf('http://data.musiques-incongrues.net/collections/links/segments/mp3/get?format=json&%s=%s&sort_field=random&limit=1', $filterFieldName, $filterFieldValue);
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$response = json_decode(curl_exec($curl), true);
	return $response;
}

function callServiceUrl($url) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($curl);
	$links = json_decode($response, true);
	return $links;
}
