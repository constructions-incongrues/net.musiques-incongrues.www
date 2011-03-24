<?php
require('Zend/Loader/Autoloader.php');
require(dirname(__FILE__).'/lib/radio.lib.php');

// Get configuration from Vanilla
$Configuration = array();
require(dirname(__FILE__).'/../conf/settings.php');

// Setup Zend Framework autoloading
Zend_Loader_Autoloader::getInstance();

// Instanciate and configure cache handler
$cache = Zend_Cache::factory('Function', 'File');

// TODO : refactor using http_build_query

// Define default values
// TODO : also define sanity checks and mandatory values
$parameters = array(
	'start'          => 0,
	'limit'          => 250,
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

		foreach ($link['playlists'] as $playlistType => $playlistDescription)  {
			$response = $cache->call('callService', array($playlistDescription['filterFieldName'], $playlistDescription['filterFieldValue']));
			$link['playlists'][$playlistType]['num_found'] = $response['num_found'];
			if (isset($response[0])) {
				$link['playlists'][$playlistType]['title'] = sprintf('Preview : %s', guessTitle($response[0]));
			} else {
				$link['playlists'][$playlistType]['title'] = '';
			}
		}

		// Add link to playlist
		$playlist[] = $link;
	}

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
		$playlistType = truncate_text($playlist[0]['discussion_name'], 20);
	}
	if (isset($parameters['contributor_name'])) {
		$playlistType = truncate_text($playlist[0]['contributor_name'], 20);
	}
	if (isset($parameters['show'])) {
		$playlistType = truncate_text($showsAvailable[$parameters['show']]['title'], 20);
	} else if (isset($parameters['domain_fqdn'])) {
		$playlistType = truncate_text($parameters['domain_fqdn'], 20);
	}

	?>
<!DOCTYPE html>
<html>

<head>
<title>Radio Substantifique Moëlle - Musiques Incongrues</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="shortcut icon" type="image/png"
	href="<?php echo $Configuration['WEB_ROOT'] ?>themes/vanilla/styles/scene/favicon.png" />
<link rel="playlist" type="application/xspf+xml"
	title="<?php echo $formatsAvailable['xspf']['title'] ?>"
	href="<?php echo $formatsAvailable['XSPF']['url'] ?>" />
<link rel="raw" type="text/html"
	title="<?php echo $formatsAvailable['XSPF']['title'] ?>"
	href="<?php echo $formatsAvailable['raw']['url'] ?>" />
<link rel="stylesheet"
	href="<?php echo $Configuration['WEB_ROOT'] ?>radio/css/style.radio.css"
	type="text/css" media="screen" />
<link rel='stylesheet'
	href='http://fonts.googleapis.com/css?family=Vibur' type='text/css'>
<link rel='stylesheet'
	href='http://fonts.googleapis.com/css?family=Just+Another+Hand'
	type='text/css'>
<link rel='stylesheet'
	href='http://fonts.googleapis.com/css?family=Copse' type='text/css'>

<script
	src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.4/mootools-yui-compressed.js"
	type="text/javascript"></script>
<script src="js/Flower_v1.0/compressed/flower_core.js"
	type="text/javascript"></script>
<script src="js/Flower_v1.0/compressed/flower_init.js"
	type="text/javascript"></script>
<script
	src="js/Flower_v1.0/uncompressed/soundplayer/flower_soundplayer.js"
	type="text/javascript"></script>
<script src="js/pretty.js" type="text/javascript"></script>
<script type="text/javascript">
window.addEvent('domready', function() {
            var player = new FlowerSoundPlayer({
            	swfLocation:'assets/scripts/SoundPlayer.swf',
            	controlImages:{previous:'assets/images/previous.png',next:'assets/images/next.png',play:'assets/images/play.png',pause:'assets/images/pause.png'},
            	seekbarSpcStyle: {'position':'relative','background-color':'#000','height':'3px','width':'100%','margin-top':'4px','overflow':'hidden'},
            	seekbarStyle: {'position':'absolute','background-color':'#c00','height':'3px','width':'0%','cursor':'pointer','z-index':'10'},
            	positionStyle: {'position':'absolute','left':'0%','width':'3px','height':'3px','background-color':'#fc0','z-index':'15'},
            });

            player.addEvent('ready', function() {
                this.createPagePlayer('player');
                $$('#loader').each(function(el){el.setStyle('display','none');});
                $$('.flower_soundplayer_next').each(function(el){el.setStyle('display','none');});
                $$('.flower_soundplayer_play').each(function(el){el.setStyle('display','none');});
                $$('.flower_soundplayer_prev').each(function(el){el.setStyle('display','none');});
                $$('.flower_soundplayer_time').each(function(el){el.setStyle('display','none');});
            });

            $$('p.tracks-date').each(function(el) {
				var date = prettyDate(el.title);
				if (date != undefined) {
                	el.innerHTML = date;
				}
            });
});
		</script>
</head>

<body>
<div id="Session">TODO : <a
	href="<?php echo $Configuration['WEB_ROOT'] ?>account">gérer son compte</a>
- <a
	href="<?php echo $Configuration['WEB_ROOT'] ?>people.php?PostBackAction=SignOutNow">se
déconnecter</a> - <a
	href="<?php echo $Configuration['WEB_ROOT'] ?>page/contact">nous
contacter</a></div>

<h1 class="logo"><a href=""<?php echo $Configuration['WEB_ROOT'] ?>"> <span
	class="first">Musiques</span> <span class="last">Incongrues</span> </a>
</h1>

<div id="search">
<form id="SearchSimple" method="get"
	action="<?php echo $Configuration['WEB_ROOT'] ?>search/"><label
	for="search" style="color: white">Rechercher</label> <input type="text"
	name="Keywords" class="champs"> <input type="hidden"
	name="PostBackAction" value="Search"> <input name="Submit"
	value="Search" class="valid" type="submit"></form>
</div>

<div id="Header">

<ul id="navbar-1">
	<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>discussions/"
		class="Pink">Discussions</a></li>
	<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>page/dons"
		class="dons">Dons</a></li>
	<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>page/faq"
		class="Faq">Faq</a></li>
	<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>page/about"
		class="Faq">À propos</a></li>
</ul>

<ul id="navbar-2">
	<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>events/"
		class="Eyes">Agenda</a></li>
	<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>releases/"
		class="Eyes">Releases</a></li>
	<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>labels"
		class="Eyes">Labels</a></li>
	<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>shows"
		class="Eyes">Émissions</a></li>
	<li><a href="http://www.tele-incongrue.net/" class="Eyes">TVi</a></li>
	<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>radio/"
		class="Eyes">Radio</a></li>
	<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>oeil/"
		class="Eyes">Œil</a></li>
</ul>
</div>

<h2 class="radio-counter"><?php echo $linksCount ?> MP3</h2>
<h2 class="label"><?php echo strtoupper($playlistType) ?></h2>

<div id="Panel">
<h2>ÉCOUTER</h2>
<ul>
<?php foreach ($sortsAvailable as $sortName => $sortDescription): ?>
	<?php if ($parameters['sort'] == $sortName): ?>
	<li class="panel-link-actived">
	<?php else: ?>
	<li>
	<?php endif; ?>
		<a href="<?php echo $sortDescription['url'] ?>"><?php echo $sortDescription['text']?></a>
	</li>
	<?php endforeach; ?>
</ul>

<h2>ÉMISSIONS</h2>
<ul>
<?php foreach ($showsAvailable as $showName => $showDescription): ?>
	<?php if ($parameters['show'] == $showName): ?>
	<li class="panel-link-actived">
	<?php else: ?>
	<li>
	<?php endif; ?>
	<a
		href="<?php echo $showDescription['url'] ?>"
		title="Écouter la playlist de l'émission"><?php echo $showDescription['title'] ?></a>
	<span class="panel-link-counter">♫<?php echo $showDescription['num_found'] ?></span></li>
	<?php endforeach; ?>
</ul>

<h2>AUTRES FORMATS</h2>
<ul>
<?php foreach ($formatsAvailable as $formatName => $formatDescription): ?>
	<li><a href="<?php echo $formatDescription['url'] ?>"
		title="<?php echo $formatDescription['title'] ?>"><?php echo $formatName ?></a></li>
		<?php endforeach; ?>
</ul>
</div>

<!-- -------------------------- -->

<div id="content"><?php if (count($links)): ?>

<p id="loader">Chargement du lecteur en cours. C'est le moment de
tapoter des doigts sur le bureau.</p>
<div id="player"></div>
<p>&nbsp;</p>
		<?php if (count($links) < $linksCount): ?>
<p>
Naviguer dans la playlist : <a
	href="<?php echo $pagination['urlPrevious'] ?>">&larr;</a> | <a
	href="<?php echo $pagination['urlNext'] ?>">&rarr;</a> | <?php echo $parameters['start'] + 1 ?>
- <?php echo $parameters['start'] + $parameters['limit'] ?> / <?php echo $linksCount ?> morceaux
</p>
<?php endif; ?>

<?php foreach ($playlist as $link): ?>
<div class="flower_soundplaylist">

<p><span style="cursor: pointer;"> <span class="tracks-title"><a
	href="<?php echo $link['url'] ?>" title="<?php echo $link['title'] ?>"><?php echo truncate_text($link['title'], 80) ?></a></span>
<span class="tracks-donwload"><a href="<?php echo $link['url'] ?>"
	title="<?php echo $link['title'] ?>">TÉLECHARGER</a></span> </span></p>

<p class="tracks-date" title="<?php echo $link['contributed_at'] ?>"><?php echo $link['contributed_at'] ?></p>

<p class="tracks-who">Posté par <a
	href="<?php echo $link['contributor_url'] ?>"
	title="Voir le profil de l'auteur sur Musiques Incongrues"> <?php echo $link['contributor_name']?></a>
<a href="<?php echo $link['query_contributor'] ?>" class="playlist-ico"
	title="Écouter la playlist de l'utilisateur. <?php echo $link['playlists']['user']['title'] ?>">♫<?php echo $link['playlists']['user']['num_found'] ?></a>
dans la discussion <a href="<?php echo $link['discussion_url'] ?>"
	title="Lire la discussion"> <?php echo $link['discussion_name']?></a> <a
	href="<?php echo $link['query_discussion'] ?>"
	title="Écouter la playlist de la discussion. <?php echo $link['playlists']['discussion']['title'] ?>"
	class="playlist-ico">♫<?php echo $link['playlists']['discussion']['num_found'] ?></a>
&bull; Hébergé par <a href="<?php echo $link['domain_fqdn'] ?>"><?php echo $link['domain_fqdn'] ?></a>
<a href="<?php echo $link['query_domain'] ?>" class="playlist-ico"
	title="Écouter la playlist de l'hébergeur. <?php echo $link['playlists']['host']['title'] ?>">♫<?php echo $link['playlists']['host']['num_found'] ?></a>
</p>

</div>
<?php endforeach; ?> <?php else: ?>

<h2>Aucun résultat. MERDRE ALORS ! | <a
	href="<?php echo $_SERVER['PHP_SELF'] ?>"
	title="Réinitialiser les filtres">reset</a></h2>

<?php endif; ?></div>
</body>

</html>
