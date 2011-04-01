<?php
/*
 Extension Name: MiVanillaMiner
 Extension Url: https://github.com/contructions-incongrues
 Description: Handles communication with a Vanilla Miner instance
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */

if (in_array($Context->SelfUrl, array('comments.php', 'post.php'))) {
	$Context->AddToDelegate('DiscussionForm','PostSaveComment','MiVanillaMiner_PostComment');
}

function MiVanillaMiner_PostComment(&$DiscussionForm) {
	// Extract URLs from comment body
	$matches = array();
	preg_match_all('#\b..?tps?://[-A-Z0-9+&@\#/%?=~_|!:,.;]*[-A-Z0-9+&@\#/%=~_|]#i', $DiscussionForm->Comment->Body, $matches);
	$urlsFound = $matches[0];

	// Build payload
	$resources = array();
	foreach ($urlsFound as $url) {
		$payload = array(
			'url'                => $url, 
			'comment_id'         => $DiscussionForm->Comment->CommentID, 
			'contributed_at'     => time(),
			'contributor_id'     => $DiscussionForm->Context->Session->UserID,
			'contributor_name'   => $DiscussionForm->Context->Session->User->Name, 
			'discussion_id'      => $DiscussionForm->Discussion->DiscussionID, 
			'discussion_name'    => $DiscussionForm->Discussion->Name
		);
		$resources[] = $payload;
	}
	
	// Post payload to Vanilla Miner instance
	$jsonResources = json_encode($resources);
	$curl = curl_init('http://localhost/constructions-incongrues/vanilla-miner/index.php/extract');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonResources);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: '.strlen($jsonResources)
		
	));
	$response = curl_exec($curl);
	$status = curl_getinfo($curl);
	if ($status['http_code'] > 299) {
		trigger_error(sprintf('Posting resources to %s did not succeed (HTTP status code %d)', $status['url'], $status['http_code']), E_USER_WARNING);
		trigger_error($response);
	}
	curl_close($curl);
}