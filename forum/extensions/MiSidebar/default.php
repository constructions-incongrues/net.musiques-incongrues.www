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
$discussionID = filter_var($_GET['DiscussionID'], FILTER_VALIDATE_INT);

// TODO : move code to this extension
$Head->AddStyleSheet('extensions/SidepanelRotator/style.css');

// List all available blocks
// Sample structure
$blocks = array('sample' => array('html' => '', 'css' => array(''), 'js' => array()));

// Radio
$blocks['radio'] = array('html' => '
<h2>Écouter la radio</h2>
<a href="/forum/radio-random.php" onclick="window.open(this.href, \'Substantifique Mo&euml;lle Incongrue et Inodore\', \'height=700, width=340, top=100, left=100, toolbar=no, menubar=no, location=no, resizable=yes, scrollbars=no, status=no\'); return false;">
<br />
<img src="/forum/uploads/radio.png" alt="Écouter la radio" style="color:#666;text-align:center;" border="0px"/></a>
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
$url = sprintf('http://data.musiques-incongrues.net/collections/links/segments/mp3/get?format=json&limit=0&discussion_id=%d', $discussionID);
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = json_decode(curl_exec($curl), true);
$linksActions = array();
if (is_array($response) && $response['num_found'] > 0) {
	$linksActions[] = array(
		'href'  => sprintf('%sradio/?discussion_id=%d', $Configuration['WEB_ROOT'], $discussionID),
		'title' => 'Écouter tous les morceaux postés dans ce topic',
		'text'  => 'Écouter ce topic ♫'
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

// Statistiques
// TODO : this is still provided by the "Statistics" extension

// Setup controller <=> blocks mappings
$mappings = array(
	'default'     => array('randomDiscussion', 'ailleurs', 'introspection'),
	'discussions' => array('randomDiscussion', 'ailleurs', 'introspection', 'affiner'),
	'comments'    => array('randomDiscussion', 'topicActions', 'instrospection'),
	'label'       => array(),
	'show'        => array(),
	'labels'      => array(),
	'shows'       => array(),
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
} else if ($Context->SelfUrl == 'index.php') {
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
			$Panel->addString($blocks[$block]['html']);
		}
	}
}