<?php
/*
 Extension Name: MiSidebar
 Extension Url: https://github.com/contructions-incongrues
 Description: Handles http://www.musiques-incongrues.net sidebar
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */

// Helpers
// Current discussionID
$discussionID = null;
if (isset($_GET['DiscussionID'])) {
	$discussionID = filter_var($_GET['DiscussionID'], FILTER_VALIDATE_INT);
}

// TODO : move code to this extension
$Head->AddStyleSheet('extensions/SidepanelRotator/style.css');

// List all available blocks
// Sample structure
$blocks = array('sample' => array('html' => '', 'css' => array(''), 'js' => array(), 'userIds' => array()));

// Discussions essentielles
$blocks['understand'] = array('html' => '
<h2>Comprendre</h2>
<ul class="ailleurs-links">
	<li><a href="http://www.musiques-incongrues.net/forum/discussion/3055/ananas-ex-machina" title="Chaque semaine, le forum évolue. C\'est là qu\'on présente les progrès réalisés">Ananas Ex Machina</a></li>
	<li><a href="http://www.musiques-incongrues.net/forum/discussion/3278/lignes-topiques" title="Obsessions collaboratives">Lignes Topiques</a></li>
	<li><a href="http://www.musiques-incongrues.net/forum/discussion/816/musique-approximative" title="Pour discuter du site Musique Approximative et de ce qu\'on peut y entendre">Musique Approximative</a></li>
	<li><a href="http://www.musiques-incongrues.net/forum/discussion/1869/pardon-my-french" title="Proposez vos créations">Pardon My French</a></li>
	<li><a href="http://www.musiques-incongrues.net/forum/discussion/4148/-radio-substantifique-moelle-incongrue-episode-2-" title="Comprendre comment fonctionne notre radio automatique et étonnante">Radio Substantifique Moëlle</a></li>
	<li><a href="http://www.musiques-incongrues.net/forum/discussion/3787/this-is-radioclash" title="Don\'t hate the Radioclash, be the Radioclash !">This is Radioclash</a></li>
</ul>
');

// Ailleurs
$blocks['ailleurs'] = array('html' => '
<h2>Ailleurs</h2>
<ul class="ailleurs-links">
	<li><a href="http://www.daheardit-records.net" title="Da ! Heard It Records">Da ! Heard It Records</a></li>
	<li><a href="http://www.egotwister.com" title="Ego Twister">Ego Twister</a></li>
	<li><a href="http://www.serendip-arts.org" title="Festival Serendip">Festival Serendip</a></li>
	<li><a href="http://istotassaca.blogspot.com/" title="Istota Ssaca">Istota Ssaca</a></li>
	<li><a href="http://lelaboratoire.be/" title="Le Laboratoire">Le Laboratoire</a></li>
	<li><a href="http://www.mazemod.org" title="Mazemod">Mazemod</a></li>
	<li><a href="http://www.musiqueapproximative.net" title="Musique Approximative">Musique Approximative</a></li>
	<li><a href="http://www.ouiedire.net" title="Ouïedire">Ouïedire</a></li>
	<li><a href="http://www.pardon-my-french.fr" title="Pardon My French">Pardon My French</a></li>
	<li><a href="http://www.thisisradioclash.org" title="Radioclash">Radioclash</a></li>
	<li><a href="http://thebrain.lautre.net" title="The Brain">The Brain</a></li>
	<li><a href="http://want.benetbene.net" title="WANT">WANT</a></li>
</ul>
');

// Affiner
$filters = '
	<li><a href="'.$Configuration['WEB_ROOT'].'discussions/?View=Bookmarks">Discussions suivies</a></li> 
	<li><a href="'.$Configuration['WEB_ROOT'].'discussions/?View=YourDiscussions">Discussions auxquelles vous avez participé</a></li>
	<li><a href="'.$Configuration['WEB_ROOT'].'discussions/?View=Private">Discussions privées</a></li>
	<li><a href="'.$Configuration['WEB_ROOT'].'search/?PostBackAction=Search&amp;Keywords=whisper;&amp;Type=Comments" >Commentaires chuchotés</a></li>
';
$filters .= '
<li style="color: black;">
	Discussions initiées par :
	<div id="search-affiner">
		<form method="get" action="'.$Configuration['WEB_ROOT'].'discussion">
			<input type="hidden" name="View" value="ByUser" />
			<input type="text" class="champs" name="username" value="'.filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING).'" />
			<input type="submit" class="valid" value="Go" />
		</form>
	</div>
</li>';
$blocks['affiner'] = array('html' => '<h2>Affiner</h2><ul class="label-links">'.$filters.'</ul>');

// Topic actions
// Find out if topic hosts links to mp3s
$minerResponse = CI_Miner_Client::getInstance()->query('links', 'mp3', array('discussion_id' => $discussionID));
$linksActions = array();
if (is_array($minerResponse) && $minerResponse['num_found'] > 0) {
	$linksActions[] = array(
		'href'  => sprintf('%sradio/?discussion_id=%d', $Configuration['WEB_ROOT'], $discussionID),
		'title' => 'Écouter tous les morceaux postés dans ce topic',
		'text'  => sprintf('Écouter ce topic ♫%d', $minerResponse['num_found'])
	);
}

$blocks['topicActions'] = array('html' => '');
$htmlLinks = '';
if (count($linksActions)) {
	foreach ($linksActions as $link) {
		$htmlLinks .= sprintf('<li><a href="%s" title="%s">%s</a></li>', $link['href'], $link['title'], $link['text']);
	}
	$blocks['topicActions']['html'] = sprintf('
<h2>Facettes</h2>
<ul class="ailleurs-links">
	%s
</ul>
', $htmlLinks);
}

// Une discussion au hasard
$htmlRandom = '<h1><a style="background-color: #ccc;" href="'.$Configuration['WEB_ROOT'].'discussions/random">Une discussion au hasard !</a></h1>';
$blocks['randomDiscussion'] = array('html' => $htmlRandom);

// Introspection
// TODO : this should come from "Œil" extension
ob_implicit_flush(false);
ob_end_clean();
ob_start();
include(dirname(__FILE__).'/../SidepanelRotator/rotator.php');
$blocks['introspection'] = array('html' => ob_get_clean());

// Introspection
// TODO : this should come from "Œil" extension
ob_implicit_flush(false);
@ob_end_clean();
ob_start();
include(dirname(__FILE__).'/../vanilla-events/sidebar.php');
$blocks['metadata-events'] = array('html' => ob_get_clean());

// Zeitgeist
// -- current
ob_implicit_flush(false);
@ob_end_clean();
ob_start();
include(dirname(__FILE__).'/../MiZeitgeist/blocks/current.php');
$blocks['zeitgeistCurrent'] = array('html' => ob_get_clean(), 'userIds' => $Configuration['BETA_TESTERS_IDS']);

// -- about
ob_implicit_flush(false);
@ob_end_clean();
ob_start();
include(dirname(__FILE__).'/../MiZeitgeist/blocks/about.php');
$blocks['zeitgeistAbout'] = array('html' => ob_get_clean());

// -- navigation
ob_implicit_flush(false);
@ob_end_clean();
ob_start();
include(dirname(__FILE__).'/../MiZeitgeist/blocks/navigation.php');
$blocks['zeitgeistNavigation'] = array('html' => ob_get_clean());

// Statistiques
// TODO : this is still provided by the "Statistics" extension

// Setup controller <=> blocks mappings
$mappings = array(
	'default'     => array('randomDiscussion', 'zeitgeistCurrent', 'understand', 'introspection'),
	'discussions' => array('randomDiscussion', 'zeitgeistCurrent', 'understand', 'introspection', 'affiner'),
	'comments'    => array('randomDiscussion', 'zeitgeistCurrent', 'topicActions', 'instrospection', 'metadata-events'),
	'events'      => array(),
	'label'       => array(),
	'show'        => array(),
	'labels'      => array(),
	'shows'       => array(),
	'zeitgeist'   => array('zeitgeistAbout', 'zeitgeistNavigation'),
);

// Compute controller name
$controllerName = 'default';

$categoryID = ForceIncomingInt('CategoryID', null);
if (in_array($categoryID, MiProjectsDatabasePeer::getCategoryIdsForType('labels', $Context))) {
	$controllerName = 'label';
} else if (in_array($categoryID, MiProjectsDatabasePeer::getCategoryIdsForType('shows', $Context))) {
	$controllerName = 'show';
} else if (ForceIncomingString('PostBackAction', '') == 'Labels') {
	$controllerName = 'labels';
} else if (ForceIncomingString('PostBackAction', '') == 'Shows') {
	$controllerName = 'shows';
} else if (ForceIncomingString('PostBackAction', '') == 'Events') {
	$controllerName = 'events';
} else if (ForceIncomingString('PostBackAction', '') == 'Zeitgeist') {
	$controllerName = 'zeitgeist';
}else if ($Context->SelfUrl == 'index.php') {
	$controllerName = 'discussions';
} else if ($Context->SelfUrl == 'comments.php') {
	$controllerName = 'comments';
}

// Select appropriate mapping
if (isset($mappings[$controllerName])) {
	$mapping = $mappings[$controllerName];
}

// Inject blocks into Panel
foreach ($mapping as $block) {
	if (isset($blocks[$block])) {
		if (isset($Panel)) {
			// "premium" features			
			if (isset($blocks[$block]['userIds'])) {
				if (in_array($Context->Session->UserID, $blocks[$block]['userIds'])) {
					$Panel->addString($blocks[$block]['html']);	
				}
			} else {
				$Panel->addString($blocks[$block]['html']);
			}
		}
	}
}
