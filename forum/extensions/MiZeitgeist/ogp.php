<?php
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

// Date objects
$dateStart = new DateTime($zeitgeist->DateStart);
$dateEnd = new DateTime($zeitgeist->DateEnd);

// Define tags
$ogMetaTags['title'] = sprintf('Zeitgeist #%s | Musiques Incongrues', $zeitgeist->ZeitgeistID);
$ogMetaTags['description'] = sprintf("Le résumé de l'activité du forum des Musiques Incongrues du %s au %s", $dateStart->format('d/m/Y'), $dateEnd->format('d/m/Y'));
