<?php
// Instanciate DB
$dsn = sprintf('mysql:dbname=%s;host=%s', $Configuration['DATABASE_NAME'], $Configuration['DATABASE_HOST']);
$dbh = new PDO($dsn, $Configuration['DATABASE_USER'], $Configuration['DATABASE_PASSWORD']);

// Guess last Zeitgeist ID
$stmt = $dbh->prepare('SELECT MAX(ZeitgeistID) as LastZeitgeistID from LUM_Zeitgeist WHERE IsPublished = 1');
$stmt->execute();
$idLastZeitgeist = $stmt->fetchObject()->LastZeitgeistID;

// Select appropriate Zeitgeist
$stmt = $dbh->prepare('SELECT ZeitgeistID, DateStart, DateEnd, Image, Description FROM LUM_Zeitgeist WHERE ZeitgeistID = :id AND IsPublished = 1');
$stmt->execute(array('id' => ForceIncomingInt('id', $idLastZeitgeist)));
$zeitgeist = $stmt->fetchObject();

// Create pagination controls
$paginationPrevious = '';
$idPreviousZeitgeist = $zeitgeist->ZeitgeistID - 1;
if ($idPreviousZeitgeist > 0) {
	$paginationPrevious = sprintf('<a href="%szeitgeist/week/%s" title="Consulter le Zeitgeist de la semaine précédente">&larr; #%s</a>', $Configuration['WEB_ROOT'], $idPreviousZeitgeist, $idPreviousZeitgeist);
}
$paginationNext = '';
$idNextZeitgeist = $zeitgeist->ZeitgeistID + 1;
if ($idNextZeitgeist <= $idLastZeitgeist) {
	$paginationNext = sprintf('<a href="%szeitgeist/week/%s" title="Consulter le Zeitgeist de la semaine suivante">#%s &rarr;</a>', $Configuration['WEB_ROOT'], $idNextZeitgeist, $idNextZeitgeist);
}
?>
<h2>Archives</h2>
<ul class="ailleurs-links">
	<li style="text-align:center;">
<?php echo $paginationPrevious ?>
<?php if ($paginationPrevious && $paginationNext): ?>
 | 
<?php endif; ?>
<?php echo $paginationNext ?>
	</li>
</ul>
