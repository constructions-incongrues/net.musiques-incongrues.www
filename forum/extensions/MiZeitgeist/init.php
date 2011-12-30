<?php
// Settings
require(dirname(__FILE__).'/../../conf/database.php');
require(dirname(__FILE__).'/../../conf/settings.php');

// Instanciate PDO object
$dsn = sprintf('mysql:dbname=%s;host=%s', $Configuration['DATABASE_NAME'], $Configuration['DATABASE_HOST']);
$pdo = new PDO($dsn, $Configuration['DATABASE_USER'], $Configuration['DATABASE_PASSWORD']);

// Generate Zeitgeists from starting date
$dateInit = new DateTime('2006-08-06');
$dateCurrent = $dateInit;
$id = 0;
while ($dateCurrent->format('U') < time()) {
        $zeitgeist = array('id' => $id++, 'dateStart' => $dateCurrent->format('Y-m-d'));
        $dateCurrent->modify('+7 day');
        $zeitgeist['dateEnd'] = $dateCurrent->format('Y-m-d');
        $statement = $pdo->prepare('REPLACE INTO LUM_Zeitgeist SET ZeitgeistID = :id, DateStart = :dateStart, DateEnd = :dateEnd');
        $statement->execute(array('id' => $id, 'dateStart' => $zeitgeist['dateStart'], 'dateEnd' => $zeitgeist['dateEnd']));
        var_dump($zeitgeist);
}
unset($pdo);

