<?php
require('Zend/Loader/Autoloader.php');
require(dirname(__FILE__).'/radio.lib.php');

// Get configuration from Vanilla
$Configuration = array();
require(dirname(__FILE__).'/../../conf/settings.php');

// Setup Zend Framework autoloading
Zend_Loader_Autoloader::getInstance();

// Instanciate and configure cache handler
$cache = Zend_Cache::factory('Function', 'File');

// TODO : refactor using http_build_query

// Define default values
// TODO : also define sanity checks and mandatory values
$parameters = array(
	'start'          => 0,
	'limit'          => 100,
	'sort'           => 'newest',
	'sort_field'     => 'contributed_at',
	'sort_direction' => 'desc',
	'format'         => 'json',
	'show'           => null
);
$parameters = array_merge($parameters, $_GET);

// Shows
$showParameters = $parameters;
unset($showParameters['discussion_id'], $showParameters['domain_fqdn'], $showParameters['contributor_name']);
$showsAvailable = array(
	'gbbg'          => array('filterFieldName' => 'domain_fqdn', 'filterFieldValue' => 'www.grandbazaarbernardgrancher.com', 'title' => 'GBBG', 'url' => '?'.http_build_query(array_merge($showParameters, array('show' => 'gbbg')))),
	'istotassaca'   => array('filterFieldName' => 'domain_fqdn', 'filterFieldValue' => 'bereznicka.deal.pl', 'title' => 'Istota Ssaca', 'url' => '?'.http_build_query(array_merge($showParameters, array('show' => 'istotassaca')))),
	'lelaboratoire' => array('filterFieldName' => 'domain_fqdn', 'filterFieldValue' => 'www.lelaboratoire.be', 'title' => 'Le Laboratoire', 'url' => '?'.http_build_query(array_merge($showParameters, array('show' => 'lelaboratoire')))),
	'ouiedire'      => array('filterFieldName' => 'domain_fqdn', 'filterFieldValue' => 'www.ouiedire.net', 'title' => 'Ouïedire', 'url' => '?'.http_build_query(array_merge($showParameters, array('show' => 'ouiedire')))),
	'radioclash'    => array('filterFieldName' => 'domain_fqdn', 'filterFieldValue' => 'www.thisisradioclash.org', 'title' => 'This is Radioclash', 'url' => '?'.http_build_query(array_merge($showParameters, array('show' => 'radioclash')))),
	'thebrain'      => array('filterFieldName' => 'domain_fqdn', 'filterFieldValue' => 'thebrain.lautre.net', 'title' => 'The Brain', 'url' => '?'.http_build_query(array_merge($showParameters, array('show' => 'thebrain')))),
);
foreach ($showsAvailable as $showName => $showDescription) {
	$response = $cache->call('callService', array($showDescription['filterFieldName'], $showDescription['filterFieldValue']));
	$showsAvailable[$showName]['num_found'] = $response['num_found'];
}

// Sorting
$sortsAvailable = array(
	'newest' => array('text' => 'Les plus récents', 'sort_field' => 'contributed_at', 'sort_direction' => 'desc'),
	'oldest' => array('text' => 'Les plus anciens', 'sort_field' => 'contributed_at', 'sort_direction' => 'asc'),
	'random' => array('text' => 'Aléatoire',        'sort_field' => 'random',         'sort_direction' => 'asc'),
);
 
foreach ($sortsAvailable as $sortName => $sortDescription) {
	$sortsAvailable[$sortName]['url'] = '?'.http_build_query(array_merge($parameters, array('sort' => $sortName)));
}

// Current sorting mode
$sortCurrent = $sortsAvailable[$parameters['sort']];
$parameters['sort_field'] = $sortCurrent['sort_field'];
$parameters['sort_direction'] = $sortCurrent['sort_direction'];

// Restrict playlist to a show
if (isset($_GET['show']) && isset($showsAvailable[$_GET['show']])) {
	$showCurrent = $showsAvailable[$_GET['show']];
	$parameters[$showCurrent['filterFieldName']] = $showCurrent['filterFieldValue'];
}

// Build service request URL
$url = sprintf('http://data.musiques-incongrues.net/collections/links/segments/mp3/get?%s', http_build_query($parameters));

// Call service
$links = $cache->call('callServiceUrl', array($url));

// Other playlist formats
$playlistFormats = array();
$formatsAvailable = array(
	'xspf' => array('text' => 'XSPF', 'format' => 'xspf', 'title' => 'Télécharger la playlist au format de playlist XSPF'), 
	'raw' => array('text' => 'RAW', 'format' => 'html', 'title' => 'Explorer la playlist sur data.musiques-incongrues.net')
);
foreach ($formatsAvailable as $formatName => $formatDescription) {
	$formatsAvailable[$formatName]['url'] = 'http://data.musiques-incongrues.net/collections/links/segments/mp3/get?'.http_build_query(array_merge($parameters, array('limit' => '-1', 'start' => 0, 'format' => $formatName)));
}

// Get number of results
$linksCount = array_pop($links);

if (count($links)) {
	// Build soundplayer playlist
	$urlPatternDiscussion = '?discussion_id=%d';
	$urlPatternContributor = '?contributor_name=%s';
	$urlPatternDomain = '?domain_fqdn=%s';
	$playlist = array();
	foreach ($links as $link) {
		// Filters
		$link['query_discussion'] = sprintf($urlPatternDiscussion, $link['discussion_id']);
		$link['query_contributor'] = sprintf($urlPatternContributor, $link['contributor_name']);
		$link['query_domain'] = sprintf($urlPatternDomain, $link['domain_fqdn']);

		// Discussion
		$link['discussion_name'] = utf8_decode($link['discussion_name']);
		$link['discussion_url'] = sprintf('%sdiscussion/%d/%s#Item1', $Configuration['WEB_ROOT'], $link['discussion_id'], CleanupString($link['discussion_name']));

		// URL
		// Soundcloud accepts surnumerous suffix, and thus makes our lives easier :)
		if ($link['domain_fqdn'] == 'soundcloud.com') {
			$link['url'] .= '.mp3';
		}

		// Title
		$link['title'] = guessTitle($link);

		// Contributor
		$link['contributor_name'] = utf8_decode($link['contributor_name']);
		$link['contributor_url'] = sprintf('%saccount/%d/', $Configuration['WEB_ROOT'], $link['contributor_id']);

		// Define related playlists links
		$link['playlists'] = array(
			'user'       => array('filterFieldName' => 'contributor_name', 'filterFieldValue' => utf8_decode($link['contributor_name']), 'num_found' => 0),
			'discussion' => array('filterFieldName' => 'discussion_id', 'filterFieldValue' => $link['discussion_id'], 'num_found' => 0),
			'host'       => array('filterFieldName' => 'domain_fqdn', 'filterFieldValue' => $link['domain_fqdn'], 'num_found' => 0)
		);

		// For selecting a random playlist
		$playlistsInteresting = array();
		foreach ($link['playlists'] as $playlistType => $playlistDescription)  {
			$response = $cache->call('callService', array($playlistDescription['filterFieldName'], $playlistDescription['filterFieldValue']));
			$link['playlists'][$playlistType]['num_found'] = $response['num_found'];
			if (isset($response[0])) {
				$link['playlists'][$playlistType]['title'] = sprintf('Aperçu : %s', guessTitle($response[0]));
			} else {
				$link['playlists'][$playlistType]['title'] = '';
			}
		}

		// Add link to playlist
		$playlist[] = $link;
	}

	// Random playlist
	$playlistRandom = array('url' => '?'.http_build_query(array('limit' => $parameters['limit'], 'sort' => 'random')));
	
	// Pagination
	$pagination = array(
		'urlNext'     => sprintf('?start=%d&limit=%d', filter_var($parameters['start'], FILTER_SANITIZE_NUMBER_INT) + $parameters['limit'], $parameters['limit']),
		'urlPrevious' => sprintf('?start=%d&limit=%d', filter_var($parameters['start'], FILTER_SANITIZE_NUMBER_INT) - $parameters['limit'], $parameters['limit']),
	);
	if (isset($parameters['discussion_id'])) {
		$pagination['urlNext'] = sprintf($pagination['urlNext'].'&discussion_id=%d', $parameters['discussion_id']);
		$pagination['urlPrevious'] = sprintf($pagination['urlPrevious'].'&discussion_id=%d', $parameters['discussion_id']);
	}
	if (isset($parameters['contributor_name'])) {
		$pagination['urlNext'] = sprintf($pagination['urlNext'].'&contributor_name=%s', $parameters['contributor_name']);
		$pagination['urlPrevious'] = sprintf($pagination['urlPrevious'].'&contributor_name=%s', $parameters['contributor_name']);
	}

	// Fetch random discussion image
	//	if (isset($parameters['discussion_id'])) {
	//		$url = sprintf('http://data.musiques-incongrues.net/collections/links/segments/images/get?sort_field=random&limit=1&format=json&discussion_id=%d', $parameters['discussion_id']);
	//		$curl = curl_init($url);
	//		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	//		$response = json_decode(curl_exec($curl), true);
	//		$imageUrl = sprintf('%suploads/radio-big.gif', $Configuration['WEB_ROOT']);
	//		if (is_array($response) && $response['num_found'] > 0) {
	//			$imageUrl = $response[0]['url'];
	//		}
	//	}
	}

	// Playlist contents description
	// TODO : refactoring smell
	$playlistType = 'globale';
	if (isset($parameters['discussion_id'])) {
		$playlistType = sprintf('la discussion %s', $playlist[0]['discussion_name']);
	}
	if (isset($parameters['contributor_name'])) {
		$playlistType = sprintf("l'utilisateur %s", $playlist[0]['contributor_name']);
	}
	if (isset($parameters['show'])) {
		$playlistType = sprintf("l'émission %s", $showsAvailable[$parameters['show']]['title']);
	} else if (isset($parameters['domain_fqdn'])) {
		$playlistType = sprintf("de l'hébergeur %s", $parameters['domain_fqdn']);
	}