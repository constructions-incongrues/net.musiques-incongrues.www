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

	if (!filter_input(INPUT_GET, 'id')) {
		// Guess last Zeitgeist ID
		$stmt = $dbh->prepare('SELECT MAX(ZeitgeistID) as LastZeitgeistID from LUM_Zeitgeist WHERE IsPublished = 1');
		$stmt->execute();
		$idLastZeitgeist = $stmt->fetchObject()->LastZeitgeistID;
		$urlRedirect = sprintf('%szeitgeist/issue/%d', $Configuration['BASE_URL'], $idLastZeitgeist);
		header('Status: 302 Found');
		header('Location: '.$urlRedirect);
		exit;
	}
	
	// Assets
	$Head->AddStyleSheet('forum/extensions/MiZeitgeist/css/MiZeitgeist.css');

	// Instanciate DB
	$dsn = sprintf('mysql:dbname=%s;host=%s', $Configuration['DATABASE_NAME'], $Configuration['DATABASE_HOST']);
	$dbh = new PDO($dsn, $Configuration['DATABASE_USER'], $Configuration['DATABASE_PASSWORD']);

	// Select appropriate Zeitgeist
	$stmt = $dbh->prepare('SELECT ZeitgeistID, DateStart, DateEnd, Image, Description FROM LUM_Zeitgeist WHERE ZeitgeistID = :id AND IsPublished = 1');
	
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
			$htmlReleases = array(sprintf('<h3>%d sorties</h3>', count($releases)));
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
			$htmlMixes = array(sprintf('<h3>%d mixes</h3>', count($mixes)));
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
			$htmlUsers = array(sprintf('<h3>%d nouveaux venus</h3>', count($users)));
			foreach ($users as $user) {
				$htmlUsers[] = sprintf('<li class="Discussion Release"><ul><li class="DiscussionTopic"><a href="%saccount/%s">%s</a></li></ul></li>', $this->context->Configuration['WEB_ROOT'], $user['UserID'], $user['Name']);
			}
		}

		$dateNextWeek = new DateTime($zeitgeist->DateEnd);
		$dateNextWeek->modify('+7 day');
		$sqlEvents = sprintf("
SELECT e.DiscussionID, d.Name, e.Date, e.City
FROM  `LUM_Event` e
INNER JOIN LUM_Discussion d ON d.DiscussionID = e.DiscussionID
WHERE e.Date > '%s'
AND e.Date <=  '%s'
ORDER BY e.Date ASC
		", $zeitgeist->DateEnd, $dateNextWeek->format('Y-m-d'));

		$htmlEvents = array();
		$events = $dbh->query($sqlEvents)->fetchAll();
		if (count($events)) {
			$htmlEvents = array(sprintf('<h3>%d évènements</h3>', count($events)));
			foreach ($events as $event) {
				$dateEvent = new DateTime($event['Date']);
				$htmlEvents[] = sprintf('<li class="Discussion Release"><ul><li class="DiscussionTopic"><a href="%sdiscussion/%s">%s</a> [%s] [%s]</li></ul></li>', $this->context->Configuration['WEB_ROOT'], $event['DiscussionID'], $event['Name'], $dateEvent->format('l j'), ucfirst(strtolower($event['City'])));
			}
		}

		// Image
		$htmlImage = array();
		if ($zeitgeist->Image) {
			$htmlImage[] = "<h3>L'image de la semaine</h3>";
			$htmlImage[] = sprintf('<img src="%s" />', $zeitgeist->Image);
		}

		// Render view
		$html = '
<div id="ContentBody" class="zeitgeist">
	%s
	<ol id="Discussions">
		<h2>Les nouveautés</h2>
		%s
		%s
		%s
		<h2>À venir</h2>
		%s
	</ol>
</div>
';
		echo sprintf($html, implode("\n", $htmlImage), implode("\n", $htmlReleases), implode("\n", $htmlMixes), implode("\n", $htmlUsers), implode("\n", $htmlEvents));
	}
}
