<?php
/*
 Extension Name: Vanilla À la une
 Extension Url: http://vanilla-alaune
 Description: 100% codé avec les pieds !
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://blogmarks.net/user/mbertier/marks
 */

/*
 * TODO : warnings are not shown to user if only event controls have errors.
 * TODO : it could be more clever to subclass the Discussion / DiscussionForm classes (?)
 * TODO : cleanup events rendering code
 */

error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL);

if (!($Context->SelfUrl == 'post.php' || $Context->SelfUrl == 'index.php' || $Context->SelfUrl == 'comments.php' || $Context->SelfUrl == 'extension.php' || $Context->SelfUrl == 'categories.php' || $Context->SelfUrl == 'search.php'))
{
	return;
}

$Head->AddStyleSheet('http://fonts.googleapis.com/css?family=Molengo');
$uid = $Context->Session->UserID;
if (in_array($Context->SelfUrl, array("index.php")) && strtolower(ForceIncomingString('Page', '')) != 'dons' && strtolower(ForceIncomingString('Page', '')) != 'faq' && strtolower(ForceIncomingString('Page', '')) != 'contact' && strtolower(ForceIncomingString('Page', '')) != 'about' && !ForceIncomingInt('CategoryID', null))
{
	$Head->AddScript('extensions/vanilla-alaune/js/behaviors.js');
	$sticky_discussions = DiscussionsPeer::getStickyDiscussions($Context);
	if ($sticky_discussions)
	{
		$discussion_tpl = '
     	<div class="post-%s"> 
     		<a href="%s">
	     		<img src="%s" width="120px" height="120px" title="%s" alt="%s" />
     		</a>
     		<p class="read-more">	
				<a href="%s" title="%s" alt="%s">%s</a> 
			</p> 
     	</div>';
		$discussions_strings = array();
		$i = 0;
		$modulo_class = 'pink';
		foreach ($sticky_discussions as $discussion)
		{
			$url_topic = GetUrl($Context->Configuration, 'comments.php', '', 'DiscussionID', $discussion['DiscussionID'], '', '#Item_1', CleanupString($discussion['Name'].'/'));
			$discussions_strings[] = sprintf($discussion_tpl,
			$modulo_class,
			$url_topic,
			getFirstImageUrl($discussion['DiscussionID'], $NoticeCollector),
			$discussion['Name'],
			$discussion['Name'],
			$url_topic,
			$discussion['Name'],
			$discussion['Name'],
			truncate_text($discussion['Name'], 20));
			$i++;
			if ($i % 2 === 0)
			{
				$modulo_class = 'pink';
			}
			else
			{
				$modulo_class = 'blue';
			}
		}
		$notice = sprintf("
     <!-- dhr:alaune -->
     <div id='etsurtout-v5'>
     <h2 style='display:inline;' class='surtout'>Les discussions essentielles : <a href='#' id='etsurtout-toggle'>Tout voir</a></h2><br />
     %s
     </div>
     ", implode('', $discussions_strings));
		$NoticeCollector->AddNotice($notice);
	}
}

class DiscussionsPeer
{
	public function getStickyDiscussions($context, $limit = null, $random = false)
	{
		$discussions = array();

		// Build selection query
		$sql = $context->ObjectFactory->NewContextObject($context, 'SqlBuilder');
		$sql->SetMainTable('Discussion','d');
		$sql->addSelect('DiscussionID', 'd');
		$sql->addSelect('Name', 'd');
		$sql->addWhere('d', 'Sticky', '', '1', '=');
		$sql_string = $sql->GetSelect();
		if ($random)
		{
			$sql_string = sprintf('%s ORDER BY RAND()', $sql_string);
		}
		else
		{
			$sql_string = sprintf('%s ORDER BY d.DateLastActive DESC', $sql_string);
		}
		if (is_integer($limit))
		{
			$sql_string = sprintf('%s LIMIT %d', $sql_string, $limit);
		}

		// Execute query
		$db = $context->Database;
		$rs = $db->Execute($sql_string, __CLASS__, __FUNCTION__, 'Failed to fetch events from database.');

		// Gather and return events
		if ($db->RowCount($rs) > 0)
		{
			while($db_discussion = $db->GetRow($rs))
			{
				$discussions[] = $db_discussion;
			}
		}

		return $discussions;
	}
}

/**
 * Returns URL to first available image in discussion.
 * 
 * @param int $discussion_id
 * 
 * @return string URL to image
 */
function getFirstImageUrl($discussionId)
{
	$parameters = array(
		'discussion_id'  => $discussionId, 
		'sort_field'     => 'contributed_at', 
		'sort_direction' => 'asc',
		'limit'			 => '1'
	);
	$response = CI_Miner_Client::getInstance()->doQuery('links', 'images', $parameters);

	// Default image
	if (is_array($response) && $response['num_found'] > 0) {
		$urlImage = $response[0]['url'];
	} else {
		$urlImage = 'http://img96.imageshack.us/img96/46/faviconxa.png';
	}

	return $urlImage;
}

/**
 * Returns list of all available youtube videos in discussion.
 * 
 * @param int $discussionId
 * 
 * @return array Array of URLs
 */
function getVideosUrls($discussionId)
{
	$urlsVideos = array();
	$parameters = array(
		'discussion_id'  => $discussionId, 
		'sort_field'     => 'contributed_at', 
		'sort_direction' => 'asc',
		'availability'   => 'available',
		'limit'			 => '-1'
	);
	$response = CI_Miner_Client::getInstance()->query('links', 'youtube', $parameters);

	// Default image
	if (is_array($response) && $response['num_found'] > 0) {
		unset($response['num_found']);
		foreach ($response as $link) {
			$urlsVideos[] = $link['url'];
		}
	}

	return $urlsVideos;
}


/**
 * Truncates +text+ to the length of +length+ and replaces the last three characters with the +truncate_string+
 * if the +text+ is longer than +length+.
 *
 * Ripped from symfony !
 */
function truncate_text($text, $length = 30, $truncate_string = '...', $truncate_lastspace = false)
{
	if ($text == '')
	{
		return '';
	}

	$mbstring = extension_loaded('mbstring');
	if($mbstring)
	{
		$old_encoding = mb_internal_encoding();
		@mb_internal_encoding(mb_detect_encoding($text));
	}
	$strlen = ($mbstring) ? 'mb_strlen' : 'strlen';
	$substr = ($mbstring) ? 'mb_substr' : 'substr';

	if ($strlen($text) > $length)
	{
		$truncate_text = $substr($text, 0, $length - $strlen($truncate_string));
		if ($truncate_lastspace)
		{
			$truncate_text = preg_replace('/\s+?(\S+)?$/', '', $truncate_text);
		}
		$text = $truncate_text.$truncate_string;
	}

	if($mbstring)
	{
		@mb_internal_encoding($old_encoding);
	}

	return $text;
}

