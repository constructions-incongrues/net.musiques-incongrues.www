<?php
/*
 Extension Name: MiZeitgeist
 Extension Url: https://github.com/contructions-incongrues
 Description: Zeitgeist generator
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */
// Configure "Shows" page rendering
$postBackAction = ForceIncomingString("PostBackAction", "");
if(in_array($postBackAction, array('Zeitgeist'))) {
	$Context->PageTitle = 'Zeitgeist';
	$Menu->CurrentTab = $postBackAction;
	$Body->CssClass = 'Discussions';

	// Assets
	$Head->AddStyleSheet('forum/extensions/MiZeitgeist/css/MiZeitgeist.css');

	// Instanciate DB
	$dsn = sprintf('mysql:dbname=%s;host=%s', $Configuration['DATABASE_NAME'], $Configuration['DATABASE_HOST']);
	$dbh = new PDO($dsn, $Configuration['DATABASE_USER'], $Configuration['DATABASE_PASSWORD']);

	// Guess last Zeitgeist ID
	$stmt = $dbh->prepare('SELECT MAX(ZeitgeistID) as LastZeitgeistID from LUM_Zeitgeist');
	$stmt->execute();
	$idLastZeitgeist = $stmt->fetchObject()->LastZeitgeistID;

	// Select appropriate Zeitgeist
	$stmt = $dbh->prepare('SELECT ZeitgeistID, DateStart, DateEnd, Image, Description FROM LUM_Zeitgeist WHERE ZeitgeistID = :id');
	$stmt->execute(array('id' => ForceIncomingInt('id', $idLastZeitgeist)));
	$zeitgeist = $stmt->fetchObject();
	
	// Page rendering controller
	$Page->AddRenderControl(new MiZeitgeistPage($Context, $Head, $zeitgeist, $idLastZeitgeist, $dbh), $Configuration["CONTROL_POSITION_BODY_ITEM"]);
}

class MiZeitgeistPage {
	
	public function __construct(Context $context, Head $head, $zeitgeist, $idLastZeitgeist, $dbh) {
		$this->context = $context;
		$this->head = $head;
		$this->zeitgeist = $zeitgeist;
		$this->dbh = $dbh;
		$this->idLastZeitgeist = $idLastZeitgeist;

		// Set meta properties
		$dateStart = new DateTime($zeitgeist->DateStart);
		$dateEnd = new DateTime($zeitgeist->DateEnd);
		$this->context->PageTitle = sprintf('Zeitgeist #%s : du %s au %s', $zeitgeist->ZeitgeistID, $dateStart->format('d/m/Y'), $dateEnd->format('d/m/Y'));

	}
	
	public function render() {

		$zeitgeist = $this->zeitgeist;
		$dbh = $this->dbh;
		$idLastZeitgeist = $this->idLastZeitgeist;

		$sqlReleases = sprintf("
SELECT r.DiscussionID, d.Name, r.DownloadLink, d.DateCreated
FROM  LUM_Discussion d
INNER JOIN LUM_Releases r ON r.DiscussionID = d.DiscussionID
WHERE d.DateCreated >= '%s'
AND d.DateCreated <= '%s' 
AND r.isMix = 0
ORDER BY d.DateCreated ASC
", $zeitgeist->DateStart, $zeitgeist->DateEnd);

		$releases = $dbh->query($sqlReleases)->fetchAll();
		$htmlReleases = array();
		if (count($releases)) {
			$htmlReleases = array('<h3>Les sorties de la semaine</h3>');
			foreach ($releases as $release) {
				$htmlReleases[] = sprintf('<li class="Discussion Release"><ul><li class="DiscussionTopic"><a href="%sdiscussion/%s">%s</a></li></ul></li>', $this->context->Configuration['WEB_ROOT'], $release['DiscussionID'], $release['Name']);
			}
		}

	$sqlMixes = sprintf("
SELECT r.DiscussionID, d.Name, r.DownloadLink, d.DateCreated
FROM  LUM_Discussion d
INNER JOIN LUM_Releases r ON r.DiscussionID = d.DiscussionID
WHERE d.DateCreated >= '%s'
AND d.DateCreated <= '%s' 
AND r.isMix = 1
ORDER BY d.DateCreated ASC
		", $zeitgeist->DateStart, $zeitgeist->DateEnd);

		$htmlMixes = array();
		$mixes = $dbh->query($sqlMixes)->fetchAll();
		if (count($mixes)) {
			$htmlMixes = array('<h3>Les nouveaux mixes</h3>');
			foreach ($mixes as $mix) {
				$htmlMixes[] = sprintf('<li class="Discussion Release"><ul><li class="DiscussionTopic"><a href="%sdiscussion/%s">%s</a></li></ul></li>', $this->context->Configuration['WEB_ROOT'], $mix['DiscussionID'], $mix['Name'], $mix['DownloadLink']);
			}
		}
		
		$sqlUsers = sprintf("
SELECT Name, UserID
FROM LUM_User
WHERE DateFirstVisit >= '%s'
AND DateFirstVisit <= '%s'
ORDER BY DateFirstVisit ASC
		", $zeitgeist->DateStart, $zeitgeist->DateEnd);

		$htmlUsers = array();
		$users = $dbh->query($sqlUsers)->fetchAll();
		if (count($users)) {
			$htmlUsers = array('<h3>Les nouveaux venus</h3>');
			foreach ($users as $user) {
				$htmlUsers[] = sprintf('<li class="Discussion Release"><ul><li class="DiscussionTopic"><a href="%saccount/%s">%s</a></li></ul></li>', $this->context->Configuration['WEB_ROOT'], $user['UserID'], $user['Name']);
			}
		}

		$dateNextWeek = new DateTime($zeitgeist->DateStart);
		$dateNextWeek->modify('+7 day');
		$sqlEvents = sprintf("
SELECT e.DiscussionID, d.Name, e.Date, e.City
FROM  `LUM_Event` e
INNER JOIN LUM_Discussion d ON d.DiscussionID = e.DiscussionID
WHERE e.Date >= '%s'
AND e.date <=  '%s'
ORDER BY e.Date ASC
		", $zeitgeist->DateStart, $dateNextWeek->format('Y-m-d'));

		$htmlEvents = array();
		$events = $dbh->query($sqlEvents)->fetchAll();
		if (count($events)) {
			$htmlEvents = array('<h3>La semaine prochaine, on sort !</h3>');
			foreach ($events as $event) {
				$dateEvent = new DateTime($event['Date']);
				$htmlEvents[] = sprintf('<li class="Discussion Release"><ul><li class="DiscussionTopic"><a href="%sdiscussion/%s">%s</a> [%s] [%s]</li></ul></li>', $this->context->Configuration['WEB_ROOT'], $event['DiscussionID'], $event['Name'], $dateEvent->format('l j'), ucfirst(strtolower($event['City'])));
			}
		}

		// Render view
		$html = '
<div id="ContentBody" class="zeitgeist">
	<ol id="Discussions">
			%s
			%s
			%s
			%s
	</ol>
</div>
';
		echo sprintf($html, implode("\n", $htmlReleases), implode("\n", $htmlMixes), implode("\n", $htmlEvents), implode("\n", $htmlUsers));
	}
}
