<?php require(dirname(__FILE__).'/lib/radio.controller.php'); ?>
<!DOCTYPE html>
<html xmlns:og="http://ogp.me/ns#">

	<head>
		<title>Radio Substantifique Moëlle (playlist <?php echo $playlistType ?> - Musiques Incongrues</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<!-- Opengraph (see http://ogp.me/) -->
		<meta property="og:site_name" content="Musiques Incongrues" />
		<meta property="og:title" content="Radio Substantifique Moëlle Incongrue - Playlist <?php echo $playlistType ?>" />
		<meta property="og:description" content="Cette radio extrait la substantifique moëlle sonore du forum des Musiques Incongrues" />

		<!-- Favicon -->
		<link rel="shortcut icon" type="image/png" href="<?php echo $Configuration['WEB_ROOT'] ?>themes/vanilla/styles/scene/favicon.png" />
		
		<!-- Alternate links -->
		<link rel="alternate" type="application/xspf+xml" title="<?php echo $formatsAvailable['xspf']['title'] ?>" href="<?php echo $formatsAvailable['xspf']['url'] ?>" />
		<link rel="alternate" type="text/html" title="<?php echo $formatsAvailable['xspf']['title'] ?>" href="<?php echo $formatsAvailable['raw']['url'] ?>" />
		
		<!-- Stylesheets -->
		<link rel="stylesheet" href="<?php echo $Configuration['WEB_ROOT'] ?>radio/css/mi-gabarit.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?php echo $Configuration['WEB_ROOT'] ?>radio/css/mi-radio.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?php echo $Configuration['WEB_ROOT'] ?>radio/css/mi-logo1.css" type="text/css" media="screen" />
		
		<!-- Web fonts -->
		<link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Copse' type='text/css' />

		<!-- Vendor JS -->
		<script	src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.4/mootools-yui-compressed.js" type="text/javascript"></script>
		<script src="js/Flower_v1.0/compressed/flower_core.js" type="text/javascript"></script>
		<script src="js/Flower_v1.0/compressed/flower_init.js" type="text/javascript"></script>
		<script src="js/Flower_v1.0/uncompressed/soundplayer/flower_soundplayer.js"	type="text/javascript"></script>
		<script src="js/pretty.js" type="text/javascript"></script>
		
		<!-- Custom JS -->
		<script src="js/behaviors.js" type="text/javascript"></script>
	</head>

	<body>

		<div id="Header">
			<ul id="navbar-1">
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>page/about">À propos</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>page/contact">Contact</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>page/dons">Dons</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>page/faq">Faq</a></li>
				<li class="session-nav"><a href="<?php echo $Configuration['WEB_ROOT'] ?>account">Gérer son compte</a></li>
				<li class="session-nav"><a href="<?php echo $Configuration['WEB_ROOT'] ?>people.php?PostBackAction=SignOutNow">Se déconnecter</a></li>
			</ul><!-- /ul#navbar-1 -->

			<h1 class="logo">
				<a href="<?php echo $Configuration['WEB_ROOT'] ?>">Musiques Incongrues</a>
			</h1><!-- /h1.logo -->

			<ul id="navbar-2">
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>events/">Agenda</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>discussions/">Discussions</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>shows/">Émissions</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>labels/">Labels</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>oeil/">Œil</a></li>
				<li class="navbar-link-actived"><a href="<?php echo $Configuration['WEB_ROOT'] ?>radio/">Radio</a></li>
				<li><a href="<?php echo $Configuration['WEB_ROOT'] ?>releases/">Releases</a></li>
			</ul><!-- /ul#navbar-2 -->
		</div><!-- /div#Header -->

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
					<a href="<?php echo $showDescription['url'] ?>" title="Écouter la playlist de l'émission"><?php echo $showDescription['title'] ?></a>
					<span class="panel-link-counter">♫<?php echo $showDescription['num_found'] ?></span>
				</li>
				<?php endforeach; ?>
			</ul>
	
			<h2>AUTRES FORMATS</h2>
			<ul>
			<?php foreach ($formatsAvailable as $formatName => $formatDescription): ?>
				<li>
					<a href="<?php echo $formatDescription['url'] ?>" title="<?php echo $formatDescription['title'] ?>">
						<?php echo $formatName ?>
					</a>
				</li>
			<?php endforeach; ?>
			</ul>
		</div><!-- /div#Panel -->

		<div id="content">
<?php if (count($links)): ?>
			<p id="loader">Chargement du lecteur en cours. C'est le moment de tapoter des doigts sur le bureau.</p>
			<div id="player"></div>
	
			<div id="radio-banner">
				<p class="about-radio">
					Cette radio extrait la substantifique moëlle sonore du forum des Musiques Incongrues.<br />
					Vous écoutez actuellement la playlist de <em><?php echo $playlistType ?></em>. <br />
					<a href="readme.html" target="_blank" title="Consulter le mode d'emploi de la radio">En savoir plus sur le fonctionnement de cette radio</a>.
				</p>
				
				<p class="listing-topic-radio">
					<a href="<?php echo $playlistRandom['url'] ?>">DÉCOUVRIR</a><br />
					<span class="discover-radio">
						<a href="<?php echo $playlistRandom['url'] ?>">Une playlist au hasard !</a>
					</span>
				</p>
			</div><!-- div#radio-banner -->
			
	<?php foreach ($playlist as $link): ?>
			<div class="flower_soundplaylist">
				<p>
					<span class="tracks-title">
						<a href="<?php echo $link['url'] ?>" title="<?php echo $link['title'] ?>" class="x-playable">
							<?php echo truncate_text($link['title'], 80) ?>
						</a>
					</span>
				</p>
				<!-- 
				<span class="tracks-donwload"><a href="<?php echo $link['url'] ?>"
					title="<?php echo $link['title'] ?>">TÉLECHARGER</a></span> </span></p>
				-->
	 
				<p class="tracks-date" title="<?php echo $link['contributed_at'] ?>"><?php echo $link['contributed_at'] ?></p>
	
				<p class="tracks-who">
					Posté par <a href="<?php echo $link['contributor_url'] ?>" title="Voir le profil de l'auteur sur Musiques Incongrues"> <?php echo $link['contributor_name']?></a>
					<a href="<?php echo $link['query_contributor'] ?>" class="<?php echo $link['playlists']['user']['class'] ?>"	title="Écouter la playlist de l'utilisateur. <?php echo $link['playlists']['user']['title'] ?>">♫<?php echo $link['playlists']['user']['num_found'] ?></a>
					dans la discussion <a href="<?php echo $link['discussion_url'] ?>" title="Lire la discussion <?php echo $link['discussion_name'] ?>"> <?php echo truncate_text($link['discussion_name'], 30) ?></a>
					<a href="<?php echo $link['query_discussion'] ?>" title="Écouter la playlist de la discussion <?php echo $link['discussion_name'] ?>. <?php echo $link['playlists']['discussion']['title'] ?>"	class="<?php echo $link['playlists']['discussion']['class'] ?>">♫<?php echo $link['playlists']['discussion']['num_found'] ?></a>
					&bull; Hébergé par <a href="<?php echo $link['domain_fqdn'] ?>"><?php echo $link['domain_fqdn'] ?></a>
					<a href="<?php echo $link['query_domain'] ?>" class="<?php echo $link['playlists']['host']['class'] ?>" title="Écouter la playlist du domaine. <?php echo truncate_text($link['playlists']['host']['title'], 10) ?>">♫<?php echo $link['playlists']['host']['num_found'] ?></a>
				</p>
			</div><!-- /div.flower_soundplaylist -->
	<?php endforeach; ?>
<?php else: ?>
			<h2>Aucun résultat. MERDRE ALORS ! | <a href="<?php echo $_SERVER['PHP_SELF'] ?>" title="Réinitialiser les filtres">reset</a></h2>
<?php endif; ?>
		</div><!-- /div#content -->
	</body>
</html>