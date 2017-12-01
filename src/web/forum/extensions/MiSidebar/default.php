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

// Events
ob_implicit_flush(false);
@ob_end_clean();
ob_start();
include(dirname(__FILE__).'/../vanilla-events/sidebar.php');
$blocks['metadata-events'] = array('html' => ob_get_clean());

// Releases
ob_implicit_flush(false);
@ob_end_clean();
ob_start();
include(dirname(__FILE__).'/../vanilla-releases/sidebar.php');
$blocks['metadata-releases'] = array('html' => ob_get_clean());

// -- alternatives
ob_implicit_flush(false);
@ob_end_clean();
ob_start();
include(dirname(__FILE__).'/../MiVanillaMiner/blocks/gallery.php');
$blocks['data-gallery'] = array('html' => ob_get_clean());

ob_implicit_flush(false);
@ob_end_clean();
ob_start();
include(dirname(__FILE__).'/../MiVanillaMiner/blocks/gallery-user.php');
$blocks['data-gallery-user'] = array('html' => ob_get_clean());

// Setup controller <=> blocks mappings
$mappings = array(
	'default'     => array('randomDiscussion', 'introspection'),
	'discussions' => array('randomDiscussion', 'introspection'),
	'comments'    => array('randomDiscussion', 'introspection', 'topicActions', 'instrospection', 'metadata-events', 'metadata-releases', 'data-gallery'),
	'events'      => array(),
	'label'       => array(),
	'show'        => array(),
	'labels'      => array(),
	'shows'       => array(),
	'account'     => array('data-gallery-user')
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
}else if ($Context->SelfUrl == 'index.php') {
	$controllerName = 'discussions';
} else if ($Context->SelfUrl == 'comments.php') {
	$controllerName = 'comments';
} else if ($Context->SelfUrl == 'account.php') {
	$controllerName = 'account';
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
