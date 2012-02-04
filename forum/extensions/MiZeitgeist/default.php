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
	$Head->AddStyleSheet('extensions/MiZeitgeist/css/MiZeitgeist.css');

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
		$this->dateStart = new DateTime($zeitgeist->DateStart);
		$this->dateEnd = new DateTime($zeitgeist->DateEnd);
		$this->context->PageTitle = sprintf('Zeitgeist #%s : du %s au %s', $zeitgeist->ZeitgeistID, $this->dateStart->format('d/m/Y'), $this->dateEnd->format('d/m/Y'));
	}
	
	public function render() {
		// Releases
		$htmlReleases = $this->renderReleases($this->getReleases());

		// Mixes
		$htmlMixes = $this->renderMixes($this->getMixes());

		// Newcomers
		$htmlUsers = $this->renderNewcomers($this->getNewcomers());
		
		// Upcoming events
		$htmlEvents = $this->renderUpcomingEvents($this->getUpcomingEvents());
		
		// Ananas Ex Machina
		$htmlAnanasExMachina = $this->renderAnanasExMachina($this->getAnanasExMachina());
		
		// Description
		$htmlDescription = $this->renderDescription($this->getDescription());
		
		// Image
		$htmlImage = array();
		if ($this->zeitgeist->Image) {
			$htmlImage[] = "<h3>L'image de la semaine</h3>";
			$htmlImage[] = sprintf('<img src="%s" />', $this->zeitgeist->Image);
		}

		// Render view
		$html = '
<div id="ContentBody" class="zeitgeist">
	<h1>Période couverte : du %s au %s</h1>
	%s
	<ol id="Discussions">
		%s
		<h2>Les nouveautés</h2>
		%s
		%s
		%s
		%s
		<h2>À venir</h2>
		%s
	</ol>
</div>
';
		echo sprintf($html,
			$this->dateStart->format('d/m/Y'),
			$this->dateEnd->format('d/m/Y'),
			implode("\n", $htmlDescription),
			implode("\n", $htmlImage),
			implode("\n", $htmlReleases), 
			implode("\n", $htmlMixes), 
			implode("\n", $htmlUsers),
			implode("\n", $htmlAnanasExMachina), 
			implode("\n", $htmlEvents)
		);
	}
	
	public function getReleases() 
	{
		$sqlReleases = sprintf("
			SELECT r.DiscussionID, d.Name, r.DownloadLink, d.DateCreated
			FROM  LUM_Discussion d
			INNER JOIN LUM_Releases r ON r.DiscussionID = d.DiscussionID
			WHERE d.DateCreated >= '%s'
			AND d.DateCreated <= '%s' 
			AND r.isMix = 0
			ORDER BY d.DateCreated ASC",
			$this->zeitgeist->DateStart, $this->zeitgeist->DateEnd
		);
		
		$releases = $this->dbh->query($sqlReleases)->fetchAll();
		
		return $releases;
	}

	public function renderReleases(array $releases)
	{
		$htmlReleases = array();
		if (count($releases)) {
			$htmlReleases = array(sprintf('<h3>%d sorties</h3>', count($releases)));
			foreach ($releases as $release) {
				$htmlReleases[] = sprintf('<li class="Discussion Release"><ul><li class="DiscussionTopic"><a href="%sdiscussion/%s">%s</a></li></ul></li>', $this->context->Configuration['WEB_ROOT'], $release['DiscussionID'], $release['Name']);
			}
		}
		
		return $htmlReleases;
	}
	
	public function getMixes()
	{
		$sqlMixes = sprintf("
		SELECT r.DiscussionID, d.Name, r.DownloadLink, d.DateCreated
		FROM  LUM_Discussion d
		INNER JOIN LUM_Releases r ON r.DiscussionID = d.DiscussionID
		WHERE d.DateCreated >= '%s'
		AND d.DateCreated <= '%s' 
		AND r.isMix = 1
		ORDER BY d.DateCreated ASC
				", $this->zeitgeist->DateStart, $this->zeitgeist->DateEnd);

		$mixes = $this->dbh->query($sqlMixes)->fetchAll();
		
		return $mixes;
	}

	public function renderMixes(array $mixes)
	{
		$htmlMixes = array();
		if (count($mixes)) {
			$htmlMixes = array(sprintf('<h3>%d mixes</h3>', count($mixes)));
			foreach ($mixes as $mix) {
				$htmlMixes[] = sprintf('<li class="Discussion Release"><ul><li class="DiscussionTopic"><a href="%sdiscussion/%s">%s</a></li></ul></li>', $this->context->Configuration['WEB_ROOT'], $mix['DiscussionID'], $mix['Name'], $mix['DownloadLink']);
			}
		}
		
		return $htmlMixes;
	}
	
	public function getNewcomers()
	{
		$sqlUsers = sprintf("
		SELECT Name, UserID
		FROM LUM_User
		WHERE DateFirstVisit >= '%s'
		AND DateFirstVisit <= '%s'
		ORDER BY DateFirstVisit ASC
				", $this->zeitgeist->DateStart, $this->zeitgeist->DateEnd);
		$users = $this->dbh->query($sqlUsers)->fetchAll();

		return $users;
	}

	public function renderNewcomers(array $users)
	{
		$htmlUsers = array();
		if (count($users)) {
			$htmlUsers = array(sprintf('<h3>%d nouveaux venus</h3>', count($users)));
			foreach ($users as $user) {
				$htmlUsers[] = sprintf('<li class="Discussion Release"><ul><li class="DiscussionTopic"><a href="%saccount/%s">%s</a></li></ul></li>', $this->context->Configuration['WEB_ROOT'], $user['UserID'], $user['Name']);
			}
		}
		
		return $htmlUsers;
	}
	
	public function getUpcomingEvents()
	{
		$dateNextWeek = new DateTime($this->zeitgeist->DateEnd);
		$dateNextWeek->modify('+7 day');
		$sqlEvents = sprintf("
		SELECT e.DiscussionID, d.Name, e.Date, e.City
		FROM  `LUM_Event` e
		INNER JOIN LUM_Discussion d ON d.DiscussionID = e.DiscussionID
		WHERE e.Date > '%s'
		AND e.Date <=  '%s'
		ORDER BY e.Date ASC
				", $this->zeitgeist->DateEnd, $dateNextWeek->format('Y-m-d'));
		
		$events = $this->dbh->query($sqlEvents)->fetchAll();

		return $events;
	}
	
	public function renderUpcomingEvents(array $events)
	{
		$htmlEvents = array();
		if (count($events)) {
			$htmlEvents = array(sprintf('<h3>%d évènements</h3>', count($events)));
			foreach ($events as $event) {
				$dateEvent = new DateTime($event['Date']);
				$htmlEvents[] = sprintf('<li class="Discussion Release"><ul><li class="DiscussionTopic"><a href="%sdiscussion/%s">%s</a> [%s] [%s]</li></ul></li>', $this->context->Configuration['WEB_ROOT'], $event['DiscussionID'], $event['Name'], $dateEvent->format('l j'), ucfirst(strtolower($event['City'])));
			}
		}
		
		return $htmlEvents;
	}
	
	public function renderAnanasExMachina($ananasExMachina)
	{
		$htmlAnanasExMachina = array();
		if ($ananasExMachina) {
			$htmlAnanasExMachina[] = '<h3>Ananas Ex Machina</h3>';
			$htmlAnanasExMachina[] = sprintf('<p>%s</p>', $this->simpleFormatText($ananasExMachina));
		}
		
		return $htmlAnanasExMachina;
	}
	
	public function getAnanasExMachina()
	{
		$sqlAnanasExMachina = sprintf("
				SELECT z.AnanasExMachina
				FROM  LUM_Zeitgeist z
				WHERE z.ZeitgeistID >= '%d'",
		$this->zeitgeist->ZeitgeistID);
		
		$ananasExMachina = $this->dbh->query($sqlAnanasExMachina)->fetchAll();
		
		return utf8_encode($ananasExMachina[0]['AnanasExMachina']);
	}

	public function renderDescription($description)
	{
		$htmlDescription = array();
		if ($description) {
			$htmlDescription[] = sprintf('<p>%s</p>', $this->simpleFormatText($description));
		}
	
		return $htmlDescription;
	}
	
	public function getDescription()
	{
		$sqlDescription = sprintf("
					SELECT z.Description
					FROM  LUM_Zeitgeist z
					WHERE z.ZeitgeistID >= '%d'",
		$this->zeitgeist->ZeitgeistID);
	
		$description = $this->dbh->query($sqlDescription)->fetchAll();
	
		return utf8_encode($description[0]['Description']);
	}
	
	/**
	 * Returns +text+ transformed into html using very simple formatting rules
	 * Surrounds paragraphs with <tt>&lt;p&gt;</tt> tags, and converts line breaks into <tt>&lt;br /&gt;</tt>
	 * Two consecutive newlines(<tt>\n\n</tt>) are considered as a paragraph, one newline (<tt>\n</tt>) is
	 * considered a linebreak, three or more consecutive newlines are turned into two newlines
	 */
	public function simpleFormatText($text, $options = array())
	{
		  $css = (isset($options['class'])) ? ' class="'.$options['class'].'"' : '';
		
		  $text = $this->pregtr($text, array("/(\r\n|\r)/"        => "\n",               // lets make them newlines crossplatform
		                                         "/\n{2,}/"           => "</p><p$css>"));    // turn two and more newlines into paragraph
		
		  // turn single newline into <br/>
		  $text = str_replace("\n", "\n<br />", $text);
		  return '<p'.$css.'>'.$text.'</p>'; // wrap the first and last line in paragraphs before we're done
	}
	
	public function pregtr($search, $replacePairs)
	{
		return preg_replace(array_keys($replacePairs), array_values($replacePairs), $search);
	}
}
