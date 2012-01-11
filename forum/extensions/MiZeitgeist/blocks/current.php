<?php
// Instanciate DB
$dsn = sprintf('mysql:dbname=%s;host=%s', $Configuration['DATABASE_NAME'], $Configuration['DATABASE_HOST']);
$dbh = new PDO($dsn, $Configuration['DATABASE_USER'], $Configuration['DATABASE_PASSWORD']);

// Guess last Zeitgeist ID
$stmt = $dbh->prepare('SELECT MAX(ZeitgeistID) as LastZeitgeistID from LUM_Zeitgeist WHERE IsPublished = 1');
$stmt->execute();
$idLastZeitgeist = $stmt->fetchObject()->LastZeitgeistID;
?>
<h2>Zeitgeist #<?php echo $idLastZeitgeist ?></h2>
<p style="text-align:center;"><a href="<?php echo sprintf('%szeitgeist/issue/%s', $Configuration['WEB_ROOT'], $idLastZeitgeist) ?>">Un résumé hebdomadaire de l'activité du forum des Musiques Incongrues</a></p>