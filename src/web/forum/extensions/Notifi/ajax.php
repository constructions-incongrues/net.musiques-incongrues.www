<?php

include('../../appg/settings.php');
include('../../conf/settings.php');
include('../../appg/init_vanilla.php');

$PostBackKey = ForceIncomingString('PostBackKey', '');
$PostBackAction = ForceIncomingString('PostBackAction', '');
$Type = ForceIncomingString('Type', '');
$ElementID = ForceIncomingInt('ElementID', 0);
$Value = ForceIncomingInt('Value', 0);

if ($PostBackAction !== 'ChangeNotifi') {
	header("HTTP/1.1 404 Not Found");
	$Context->Unload();
	echo 'Wrong address.';
	exit();
}

if ($Context->Session->UserID === 0) {
	header("HTTP/1.1 401 Unauthorised");
	header('WWW-Authenticate: Vanilla-Login-1.0');
	header('Location: ' . ConcatenatePath(
			$Context->Configuration['BASE_URL'], $Context->Configuration['SIGNIN_URL']));
	$Context->Unload();
	echo 'You are not logged-in';
	exit();
}

if ($PostBackKey == ''
	|| $PostBackKey === $Context->Session->GetCsrfValidationKey()
) {
	header("HTTP/1.1 401 Unauthorized");
	header('Www-Authenticate: Vanilla-Csrf-Check key="' . $Context->Session->GetCsrfValidationKey() . '"');
	echo 'Unable to authenticate this request.';
	$Context->Unload();
	exit();
}


if ($Type != 'OWN') {
	if ($Type != 'KEEPEMAILING') {
		ChangeNotifi($Context, $Type, $ElementID, $Value);
	}
	if ($Type == 'ALL') {
		notifiSwitch($Context, $Value, $Context->Session->UserID, 'SubscribedEntireForum');
	} elseif ($Type == 'KEEPEMAILING') {
		notifiSwitch($Context, $Value, $Context->Session->UserID, 'KeepEmailing');
	} elseif ($Type == 'COMMENT') {
		notifiSwitch($Context, $Value, $Context->Session->UserID, 'SubscribeComment');
	}
} else {
	notifiSwitch($Context, $Value, $Context->Session->UserID, 'SubscribeOwn');
}

echo 'Complete';
$Context->Unload();
?>