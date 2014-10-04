<?php
/*
 Extension Name: MiRadioTeleZ
 Extension Url: https://github.com/contructions-incongrues
 Description: TODO
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */
$calendarId = '9g7ifo9c5b5f8o4snnudgkjv7o@group.calendar.google.com';
$apiKey = 'AIzaSyBeRWahXXReRY6Ko_kAMPhgnjNRYoEY5Uw';
$dateMin = new DateTime('now');
$dateMax = new DateTime('now + 2 hours');
$url = sprintf(
    'https://www.googleapis.com/calendar/v3/calendars/9g7ifo9c5b5f8o4snnudgkjv7o%%40group.calendar.google.com/events?timeMin=%s&timeMax=%s&key=%s',
    urlencode($dateMin->format(DateTime::RFC3339)),
    urlencode($dateMax->format(DateTime::RFC3339)),
    $apiKey
);

var_dump($url);
