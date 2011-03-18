<?php
// Find out if discussion contains any MP3s
if (!function_exists('checkForMp3s')) {
	function checkForMp3s($discussionID) {
		$url = sprintf('http://data.musiques-incongrues.net/collections/links/segments/mp3/get?format=json&discussion_id=%d', $discussionID);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = json_decode(curl_exec($curl), true);
		return $response;
	}
}
// TODO : this is definitely *not* the right place for this
// Setup autoloading
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

// Instanciate and configure cache handler
$cache = Zend_Cache::factory('Function', 'File');

// Call service and cache response
$response = $cache->call('checkForMp3s', array($Discussion->DiscussionID));

// Note: This file is included from the library/Vanilla/Vanilla.Control.SearchForm.php
// class and also from the library/Vanilla/Vanilla.Control.DiscussionForm.php's
// themes/discussions.php include template.

$UnreadUrl = GetUnreadQuerystring($Discussion, $this->Context->Configuration, $CurrentUserJumpToLastCommentPref);
$NewUrl = GetUnreadQuerystring($Discussion, $this->Context->Configuration, 1);
$LastUrl = GetLastCommentQuerystring($Discussion, $this->Context->Configuration, $CurrentUserJumpToLastCommentPref);

$this->DelegateParameters['Discussion'] = &$Discussion;
$this->DelegateParameters['DiscussionList'] = &$DiscussionList;

$DiscussionList .= '
<li id="Discussion_'.$Discussion->DiscussionID.'" class="Discussion'.$Discussion->Status.($Discussion->CountComments == 1?' NoReplies':'').($this->Context->Configuration['USE_CATEGORIES'] ? ' Category_'.$Discussion->CategoryID:'').($Alternate ? ' Alternate' : '').'">';
	$this->CallDelegate('PreDiscussionOptionsRender');
	if (is_array($response) && $response['num_found'] > 0 && in_array($this->Context->Session->UserID, array(1, 2, 21, 132, 9, 3, 14, 665, 366, 95, 90))) {
	$DiscussionList .= '<ul>
		<li class="DiscussionType">
			<span>'.$this->Context->GetDefinition('DiscussionType').'</span>'.DiscussionPrefix($this->Context, $Discussion).'
		</li>
		<li class="DiscussionTopic">
			<span>'.$this->Context->GetDefinition('DiscussionTopic').'</span><a href="'.$UnreadUrl.'">'.$Discussion->Name.'</a>
			<a href="'.$this->Context->Configuration['WEB_ROOT'].'radio/?discussion_id='.$Discussion->DiscussionID.'" title="Écouter le(s) '.$response['num_found'].' morceau(x) contenu(s) dans cette discussion avec la radio du forum" style="background-color:yellow;">♫'.$response['num_found'].'</a>
		</li>
	';
	} else {
	$DiscussionList .= '<ul>
		<li class="DiscussionType">
			<span>'.$this->Context->GetDefinition('DiscussionType').'</span>'.DiscussionPrefix($this->Context, $Discussion).'
		</li>
		<li class="DiscussionTopic">
			<span>'.$this->Context->GetDefinition('DiscussionTopic').'</span><a href="'.$UnreadUrl.'">'.$Discussion->Name.'</a>
		</li>
	';		
	}
		if ($this->Context->Configuration['USE_CATEGORIES']) {
			$DiscussionList .= '
			<li class="DiscussionCategory">
				<span>'.$this->Context->GetDefinition('Category').' </span><a href="'.GetUrl($this->Context->Configuration, 'index.php', '', 'CategoryID', $Discussion->CategoryID).'">'.$Discussion->Category.'</a>
			</li>
			';
		}
		$DiscussionList .= '<li class="DiscussionStarted">
			<span><a href="'.GetUrl($this->Context->Configuration, 'comments.php', '', 'DiscussionID', $Discussion->DiscussionID, '', '#Item_1', CleanupString($Discussion->Name).'/').'">'.$this->Context->GetDefinition('StartedBy').'</a> </span><a href="'.GetUrl($this->Context->Configuration, 'account.php', '', 'u', $Discussion->AuthUserID).'">'.$Discussion->AuthUsername.'</a>
		</li>
		<li class="DiscussionComments">
			<span>'.$this->Context->GetDefinition('Comments').' </span>'.$Discussion->CountComments.'
		</li>
		<li class="DiscussionLastComment">
			<span><a href="'.$LastUrl.'">'.$this->Context->GetDefinition('LastCommentBy').'</a> </span><a href="'.GetUrl($this->Context->Configuration, 'account.php', '', 'u', $Discussion->LastUserID).'">'.$Discussion->LastUsername.'</a>
		</li>
		<li class="DiscussionActive">
			<span><a href="'.$LastUrl.'">'.$this->Context->GetDefinition('LastActive').'</a> </span>'.TimeDiff($this->Context, $Discussion->DateLastActive,mktime()).'
		</li>';
		if ($this->Context->Session->UserID > 0) {
				$DiscussionList .= '
			<li class="DiscussionNew">
				<a href="'.$NewUrl.'"><span>'.$this->Context->GetDefinition('NewCaps').' </span>'.$Discussion->NewComments.'</a>
			</li>
			';
		}

	$this->CallDelegate('PostDiscussionOptionsRender');

$DiscussionList .= '</ul>
</li>';
$this->CallDelegate('PostDiscussionRender');
?>