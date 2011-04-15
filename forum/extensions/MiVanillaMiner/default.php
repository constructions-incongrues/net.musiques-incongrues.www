<?php
/*
 Extension Name: MiVanillaMiner
 Extension Url: https://github.com/contructions-incongrues
 Description: Handles communication with a Vanilla Miner instance
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */

// Instanciate and configure Miner client
require(dirname(__FILE__).'/CI/Miner/Client.php');
$cacheBackend = new Zend_Cache_Backend_File(array(
	'cache_dir'        => $Context->Configuration['VANILLA_MINER_CACHEDIR'], 
	'file_name_prefix' => 'mi_miner_cache',
	'cache_file_umask' => 0666
));
CI_Miner_Client::getInstance($Configuration['VANILLA_MINER_URL'], $cacheBackend);

if (in_array($Context->SelfUrl, array('comments.php', 'post.php'))) {
	$Context->AddToDelegate('DiscussionForm','PostSaveComment','MiVanillaMiner_PostComment');
	$Context->AddToDelegate('DiscussionForm','PostSaveDiscussion','MiVanillaMiner_PostComment');
}

function MiVanillaMiner_PostComment(&$DiscussionForm) {
	// Extract URLs from comment body
	$body = $DiscussionForm->Comment->Body;
	if (empty($body)) {
		$body = $DiscussionForm->Discussion->Comment->Body;
	}
	$commentId = $DiscussionForm->Comment->CommentID;
	if (!$commentId) {
		$commentId = $DiscussionForm->Discussion->Comment->CommentID;
	}
	
	$matches = array();
	preg_match_all('#\b..?tps?://[-A-Z0-9+&@\#/%?=~_|!:,.;]*[-A-Z0-9+&@\#/%=~_|]#i', strip_tags($body), $matches);
	$urlsFound = array_unique($matches[0]);

	// Build payload
	if (count($urlsFound)) {
		$resources = array();
		foreach ($urlsFound as $url) {
			$payload = array(
				'url'                => $url, 
				'comment_id'         => $commentId, 
				'contributed_at'     => time(),
				'contributor_id'     => $DiscussionForm->Context->Session->UserID,
				'contributor_name'   => utf8_encode($DiscussionForm->Context->Session->User->Name), 
				'discussion_id'      => $DiscussionForm->Discussion->DiscussionID, 
				'discussion_name'    => utf8_encode($DiscussionForm->Discussion->Name)
			);
			$resources[] = $payload;
		}
	
		// Post payload to Vanilla Miner instance
		$jsonResources = json_encode($resources);
		$curl = curl_init(sprintf('%s/extract', $DiscussionForm->Context->Configuration['VANILLA_MINER_URL']));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonResources);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: '.strlen($jsonResources)
			
		));
		$response = curl_exec($curl);
		$status = curl_getinfo($curl);
		// TODO : use vanilla notice collector
		if ($status['http_code'] > 299) {
			trigger_error(sprintf("Posting resources to %s did not succeed (HTTP status code %d).\nResponse :\n%s", $status['url'], $status['http_code'], print_r($response, true)), E_USER_WARNING);
		}
		curl_close($curl);
	}
}