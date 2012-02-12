<?php
// Instanciate DB
$dsn = sprintf('mysql:dbname=%s;host=%s', $Configuration['DATABASE_NAME'], $Configuration['DATABASE_HOST']);
$dbh = new PDO($dsn, $Configuration['DATABASE_USER'], $Configuration['DATABASE_PASSWORD']);

// Guess last Zeitgeist ID
$stmt = $dbh->prepare('SELECT MAX(ZeitgeistID) as LastZeitgeistID from LUM_Zeitgeist');
$stmt->execute();
$idLastZeitgeist = $stmt->fetchObject()->LastZeitgeistID;

// Select appropriate Zeitgeist
$stmt = $dbh->prepare('SELECT ZeitgeistID, DateStart, DateEnd, Image, Description FROM LUM_Zeitgeist WHERE ZeitgeistID = :id AND IsPublished = 1');
$stmt->execute(array('id' => ForceIncomingInt('id', $idLastZeitgeist)));
$zeitgeist = $stmt->fetchObject();

// Date objects
$dateStart = new DateTime($zeitgeist->DateStart);
$dateEnd = new DateTime($zeitgeist->DateEnd);

// Define tags
$ogMetaTags['title'] = sprintf('Zeitgeist Incongru #%s : du %s au %s | Musiques Incongrues', $zeitgeist->ZeitgeistID, $dateStart->format('d/m/Y'), $dateEnd->format('d/m/Y'));
$ogMetaTags['description'] = sprintf("Chaque semaine, le Zeitgeist Incongru résume l'actualité du forum des Musiques Incongrues : nouvelles productions, mixes et autres pièces. Il propose aussi un agenda des concerts pour la semaine à  venir.", $dateStart->format('d/m/Y'), $dateEnd->format('d/m/Y'));
$ogMetaTags['image'] = $zeitgeist->Image;
