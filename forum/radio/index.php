<?php
// Get configuration from Vanilla
$Configuration = array();
require(dirname(__FILE__).'/../conf/settings.php');

// Helpers
function CleanupString($InString) {
	$Code = explode(',', '&lt;,&gt;,&#039;,&amp;,&quot;,À,Á,Â,Ã,Ä,&Auml;,Å,Ā,Ą,Ă,Æ,Ç,Ć,Č,Ĉ,Ċ,Ď,Đ,Ð,È,É,Ê,Ë,Ē,Ę,Ě,Ĕ,Ė,Ĝ,Ğ,Ġ,Ģ,Ĥ,Ħ,Ì,Í,Î,Ï,Ī,Ĩ,Ĭ,Į,İ,Ĳ,Ĵ,Ķ,Ł,Ľ,Ĺ,Ļ,Ŀ,Ñ,Ń,Ň,Ņ,Ŋ,Ò,Ó,Ô,Õ,Ö,&Ouml;,Ø,Ō,Ő,Ŏ,Œ,Ŕ,Ř,Ŗ,Ś,Š,Ş,Ŝ,Ș,Ť,Ţ,Ŧ,Ț,Ù,Ú,Û,Ü,Ū,&Uuml;,Ů,Ű,Ŭ,Ũ,Ų,Ŵ,Ý,Ŷ,Ÿ,Ź,Ž,Ż,Þ,Þ,à,á,â,ã,ä,&auml;,å,ā,ą,ă,æ,ç,ć,č,ĉ,ċ,ď,đ,ð,è,é,ê,ë,ē,ę,ě,ĕ,ė,ƒ,ĝ,ğ,ġ,ģ,ĥ,ħ,ì,í,î,ï,ī,ĩ,ĭ,į,ı,ĳ,ĵ,ķ,ĸ,ł,ľ,ĺ,ļ,ŀ,ñ,ń,ň,ņ,ŉ,ŋ,ò,ó,ô,õ,ö,&ouml;,ø,ō,ő,ŏ,œ,ŕ,ř,ŗ,š,ù,ú,û,ü,ū,&uuml;,ů,ű,ŭ,ũ,ų,ŵ,ý,ÿ,ŷ,ž,ż,ź,þ,ß,ſ,А,Б,В,Г,Д,Е,Ё,Ж,З,И,Й,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Ъ,Ы,Э,Ю,Я,а,б,в,г,д,е,ё,ж,з,и,й,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ъ,ы,э,ю,я');
	$Translation = explode(',', ',,,,,A,A,A,A,Ae,A,A,A,A,A,Ae,C,C,C,C,C,D,D,D,E,E,E,E,E,E,E,E,E,G,G,G,G,H,H,I,I,I,I,I,I,I,I,I,IJ,J,K,K,K,K,K,K,N,N,N,N,N,O,O,O,O,Oe,Oe,O,O,O,O,OE,R,R,R,S,S,S,S,S,T,T,T,T,U,U,U,Ue,U,Ue,U,U,U,U,U,W,Y,Y,Y,Z,Z,Z,T,T,a,a,a,a,ae,ae,a,a,a,a,ae,c,c,c,c,c,d,d,d,e,e,e,e,e,e,e,e,e,f,g,g,g,g,h,h,i,i,i,i,i,i,i,i,i,ij,j,k,k,l,l,l,l,l,n,n,n,n,n,n,o,o,o,o,oe,oe,o,o,o,o,oe,r,r,r,s,u,u,u,ue,u,ue,u,u,u,u,u,w,y,y,y,z,z,z,t,ss,ss,A,B,V,G,D,E,YO,ZH,Z,I,Y,K,L,M,N,O,P,R,S,T,U,F,H,C,CH,SH,SCH,Y,Y,E,YU,YA,a,b,v,g,d,e,yo,zh,z,i,y,k,l,m,n,o,p,r,s,t,u,f,h,c,ch,sh,sch,y,y,e,yu,ya');
	$sReturn = $InString;
	$sReturn = str_replace($Code, $Translation, $sReturn);
	$sReturn = urldecode($sReturn);
	$sReturn = preg_replace('/[^A-Za-z0-9 ]/', '', $sReturn);
	$sReturn = str_replace(' ', '-', $sReturn);
	return strtolower(str_replace('--', '-', $sReturn));
}

// TODO : refactor using http_build_query

// Define default values
// TODO : also define sanity checks and mandatory values
$parameters = array(
	'start'          => 0,
	'limit'          => 10000,
	'sort'           => 'newest',
);
$parameters = array_merge($parameters, $_GET);

// Sorting
$sortsAvailable = array(
	'random' => array('text' => 'Aléatoire', 'sort_field' => 'random', 'sort_direction' => 'asc'), 
	'newest' => array('text' => 'Les plus récents', 'sort_field' => 'contributed_at', 'sort_direction' => 'desc'),
	'oldest' => array('text' => 'Les plus anciens', 'sort_field' => 'contributed_at', 'sort_direction' => 'asc')
);

foreach ($sortsAvailable as $sortName => $sortDescription) {
	$sortsAvailable[$sortName]['url'] = str_replace('sort='.$parameters['sort'], 'sort='.$sortName, $_SERVER['REQUEST_URI']);
}

// Current sorting mode
$sortCurrent = $sortsAvailable[$parameters['sort']];

// Build request URL
$urlPattern = 'http://data.musiques-incongrues.net/collections/links/segments/mp3/get?start=%d&limit=%d&sort_field=%s&sort_direction=%s&format=json';
$url = sprintf($urlPattern, $parameters['start'], $parameters['limit'], $sortCurrent['sort_field'], $sortCurrent['sort_direction']);

// Restrict playlist to a discussion
if (isset($_GET['discussion_id']) && filter_var($_GET['discussion_id'], FILTER_VALIDATE_INT)) {
	$url .= sprintf('&discussion_id=%d', $_GET['discussion_id']);
}  

// Restrict playlist to an author
if (isset($_GET['contributor_name']) && filter_var($_GET['contributor_name'], FILTER_SANITIZE_STRING)) {
	$url .= sprintf('&contributor_name=%s', $_GET['contributor_name']);
}  

// Restrict playlist to a domain
if (isset($_GET['domain_fqdn']) && filter_var($_GET['domain_fqdn'], FILTER_SANITIZE_STRING)) {
	$url .= sprintf('&domain_fqdn=%s', $_GET['domain_fqdn']);
}  

// Call service
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
$links = json_decode($response, true);

// Other playlist formats
// TODO : remove "start" and "limit" parameters from url
$playlistFormats = array(
	'XSPF' => str_replace('format=json', 'format=xspf', $url),
	'raw' => str_replace('format=json', 'format=html', $url)
);

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
		// Thanks to soundcloud url formalism, we can guess the track title
		if ($link['domain_fqdn'] == 'soundcloud.com') {
			$urlParts = array_reverse(explode('/', $link['url']));
			$link['title'] = sprintf('%s - %s', str_replace('-', ' ', ucfirst($urlParts[2])), str_replace('-', ' ', ucfirst($urlParts[1])));
		} else {
			$link['title'] = urldecode(basename($link['url'], '.mp3'));
			$link['title'] = urldecode($link['title']);
			$link['title'] = str_replace('_', ' ', $link['title']);
		}
		
		// Contributor
		$link['contributor_name'] = utf8_decode($link['contributor_name']);
		$link['contributor_url'] = sprintf('%saccount/%d/', $Configuration['WEB_ROOT'], $link['contributor_id']);
		
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
	
	// Playlist contents description
	// TODO : refactoring smell
	$playlistDescription = 'globale';
	if (isset($parameters['discussion_id'])) {
		$playlistDescription = 'discussion';
	}
	if (isset($parameters['contributor_name'])) {
		$playlistDescription = 'utilisateur';
	}
	if (isset($parameters['domain_fqdn'])) {
		$playlistDescription = 'hébergeur';
	}
	
	// Fetch random discussion image
	if (isset($parameters['discussion_id'])) {
		$url = sprintf('http://data.musiques-incongrues.net/collections/links/segments/images/get?sort_field=random&limit=1&format=json&discussion_id=%d', $parameters['discussion_id']);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = json_decode(curl_exec($curl), true);
		$imageUrl = sprintf('%suploads/radio-big.gif', $Configuration['WEB_ROOT']);
		if (is_array($response) && $response['num_found'] > 0) {
			$imageUrl = $response[0]['url'];
		}
	}
}
?>
<!DOCTYPE html>
<html>

	<head>
		<title>Radio Substantifique Moëlle - Musiques Incongrues</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<link rel="shortcut icon" type="image/png" href="<?php echo $Configuration['WEB_ROOT'] ?>themes/vanilla/styles/scene/favicon.png" />
		<link rel="playlist" type="application/xspf+xml" title="Téléchargez la playlist courante au format XSPF" href="<?php echo $playlistFormats['XSPF'] ?>" />
		<link rel="raw" type="text/html" title="Voir le résultat de la requête brute sur http://data.musiques-incongrues.net" href="<?php echo $playlistFormats['raw'] ?>" />
        <link rel="stylesheet" href="<?php echo $Configuration['WEB_ROOT'] ?>radio/css/style.radio.css" type="text/css" media="screen" /> 
        <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Vibur' type='text/css'>
        <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Just+Another+Hand' type='text/css'>
        <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Copse' type='text/css'>
        
		<script src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.4/mootools-yui-compressed.js" type="text/javascript"></script>
		<script src="js/Flower_v1.0/compressed/flower_core.js" type="text/javascript"></script>
		<script src="js/Flower_v1.0/compressed/flower_init.js" type="text/javascript"></script>
		<script src="js/Flower_v1.0/uncompressed/soundplayer/flower_soundplayer.js" type="text/javascript"></script>
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
		<div id="Session">
			TODO : <a href="<?php echo $Configuration['WEB_ROOT'] ?>account">gérer son compte</a> 
			- <a href="<?php echo $Configuration['WEB_ROOT'] ?>people.php?PostBackAction=SignOutNow">se déconnecter</a>
			- <a href="<?php echo $Configuration['WEB_ROOT'] ?>page/contact">nous contacter</a>
		</div>

		<h1 class="logo">
			<a href=""<?php echo $Configuration['WEB_ROOT'] ?>">
				<span class="first">Musiques</span>
				<span class="last">Incongrues</span>
			</a>
		</h1>
		
		<div id="search">
			<form id="SearchSimple" method="get" action="<?php echo $Configuration['WEB_ROOT'] ?>search/">
				<label for="search" style="color:white">Rechercher</label>
				<input type="text" name="Keywords" class="champs">
				<input type="hidden" name="PostBackAction" value="Search">
				<input name="Submit" value="Search" class="valid" type="submit">
			</form>
		</div>
    
		<div id="Header">
		
			<ul id="navbar-1">
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>discussions/" class="Pink">Discussions</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>settings/" class="Pink">Settings</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>page/dons" class="dons">Dons</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>page/faq" class="Faq">Faq</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>page/about" class="Faq">À propos</a></li>
			</ul>
			
			<ul id="navbar-2">
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>events/" class="Eyes">Agenda</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>releases/" class="Eyes">Releases</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>labels" class="Eyes">Labels</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>shows" class="Eyes">Émissions</a></li>
				<li><a href="http://www.tele-incongrue.net/" class="Eyes">TVi</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>radio-random.php" class="Eyes">Radio</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>oeil/" class="Eyes">Œil</a></li>
			</ul>
		</div>
	
		<h2 class="radio-counter"><?php echo $linksCount ?></h2>
		<h2 class="label">MP3s DANS LA PLAYLIST <?php echo strtoupper($playlistDescription) ?></h2>
	
		<div id="Panel">
			<h2>ÉCOUTER</h2>
			<ul>
<?php foreach ($sortsAvailable as $sortDescription): ?>
				<li><a href="<?php echo $sortDescription['url'] ?>"><?php echo $sortDescription['text']?></a></li>
<?php endforeach; ?>
			</ul>
		
			<h2>ÉMISSIONS</h2>
			<ul>
				<li><a href="">GBBG</a></li>
				<li><a href="">ISTOTA SSACA</a> </li>
				<li><a href="">LE LABORATOIRE</a></li>
				<li><a href="">OUÏEDIRE</a></li>
				<li><a href="">RADIOCLASH</a></li>
				<li><a href="">THE BRAIN</a></li>
			</ul>
		</div>

	<!-- -------------------------- -->

		<div id="content">
<?php if (count($links)): ?>

			<p id="loader">Chargement du lecteur en cours. C'est le moment de tapoter des doigts sur le bureau.</p>
			<div id="player"></div>
	
	<?php if (count($links) < $linksCount): ?>
			<p>
				Naviguer dans la playlist :
				<a href="<?php echo $pagination['urlPrevious'] ?>">&larr;</a> |
				<a href="<?php echo $pagination['urlNext'] ?>">&rarr;</a> |
				<?php echo $parameters['start'] + 1 ?> - <?php echo $parameters['start'] + $parameters['limit'] ?> / <?php echo $linksCount ?> morceaux
			</p>
	<?php endif; ?>
			
	<?php foreach ($playlist as $link): ?>
			<div class="flower_soundplaylist">
				
				<p>
					<span style="cursor: pointer; ">
						<span class="tracks-title"><a href="<?php echo $link['url'] ?>" title="<?php echo $link['title'] ?>"><?php echo $link['title'] ?></a></span>
						<span class="tracks-donwload"><a href="<?php echo $link['url'] ?>" title="<?php echo $link['title'] ?>">TÉLECHARGER</a></span>
					</span>
				</p>
				
				<p class="tracks-date" title="<?php echo $link['contributed_at'] ?>"><?php echo $link['contributed_at'] ?></p>
				
				<p class="tracks-who">
					Posté par
					<a href="<?php echo $link['contributor_url'] ?>" title="Voir le profil de l'auteur sur Musiques Incongrues">
						<?php echo $link['contributor_name']?></a> 
					<a href="<?php echo $link['query_contributor'] ?>" class="playlist-ico" title="Écouter la playlist de l'utilisateur">♫</a> 
					dans la discussion 
					<a href="<?php echo $link['discussion_url'] ?>" title="Lire la discussion">
						<?php echo $link['discussion_name']?></a> 
					<a href="<?php echo $link['query_discussion'] ?>" title="Écouter la playlist de la discussion."class="playlist-ico">♫</a> &bull;
					Hébergé par <a href="<?php echo $link['domain_fqdn'] ?>"><?php echo $link['domain_fqdn'] ?></a>
					<a href="<?php echo $link['query_domain'] ?>" class="playlist-ico" title="Écouter la playlist de l'hébergeur.">♫</a>
				</p>
		
			</div>
	<?php endforeach; ?>
	
<?php else: ?>
	
			<h2>
				Aucun résultat. MERDRE ALORS ! 
				| <a href="<?php echo $_SERVER['PHP_SELF'] ?>" title="Réinitialiser les filtres">reset</a>
			</h2>
	
<?php endif; ?>

		</div>
	</body>

</html>
