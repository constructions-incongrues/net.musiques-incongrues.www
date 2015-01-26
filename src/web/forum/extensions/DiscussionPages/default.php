<?php

/*
  Extension Name: Discussion Pages
  Extension Url: http://www.krijtenberg.nl/
  Description: Show links Discussion pages in the Discussion Grid
  Version: 1.2
  Author: Maurice "Jazzman" Krijtenberg
  Author Url: http://www.krijtenberg.nl/
 */

if ($Context->SelfUrl == 'index.php') {
	function DiscussionGrid_DiscussionPages($DiscussionGrid) {
		$Discussion = &$DiscussionGrid->DelegateParameters['Discussion'];
		$DiscussionList = &$DiscussionGrid->DelegateParameters['DiscussionList'];
		$CommentsPerPage = $DiscussionGrid->Context->Configuration['COMMENTS_PER_PAGE'];
		if ($Discussion->CountComments > $CommentsPerPage) {
			$PageList = '<font class="DiscussionPageNumbersContainer">';
			$PageList .= $Discussion->Context->GetDefinition('TextPrefix');
			$PageCount = CalculateNumberOfPages($Discussion->CountComments, $CommentsPerPage);
			if ($PageCount > 6) {
				$PageCountMinus2 = $PageCount - 2;
				$PageCountMinus1 = $PageCount - 1;
				$PageList .= ' <a href="' . GetUrl($Discussion->Context->Configuration, 'comments.php', '', 'DiscussionID', $Discussion->DiscussionID, 1) . '">1</a> ';
				$PageList .= ' <a href="' . GetUrl($Discussion->Context->Configuration, 'comments.php', '', 'DiscussionID', $Discussion->DiscussionID, 2) . '">2</a> ';
				$PageList .= ' <a href="' . GetUrl($Discussion->Context->Configuration, 'comments.php', '', 'DiscussionID', $Discussion->DiscussionID, 3) . '">3</a> ... ';
				$PageList .= ' <a href="' . GetUrl($Discussion->Context->Configuration, 'comments.php', '', 'DiscussionID', $Discussion->DiscussionID, $PageCountMinus2) . '">' . $PageCountMinus2 . '</a> ';
				$PageList .= ' <a href="' . GetUrl($Discussion->Context->Configuration, 'comments.php', '', 'DiscussionID', $Discussion->DiscussionID, $PageCountMinus1) . '">' . $PageCountMinus1 . '</a> ';
				$PageList .= ' <a href="' . GetUrl($Discussion->Context->Configuration, 'comments.php', '', 'DiscussionID', $Discussion->DiscussionID, $PageCount) . '">' . $PageCount . '</a> ';
			} else {
				for ($i = 1; $i <= $PageCount; $i++) {
					$PageList .= ' <a href="' . GetUrl($Discussion->Context->Configuration, 'comments.php', '', 'DiscussionID', $Discussion->DiscussionID, $i) . '">' . $i . '</a> ';
				}
			}
			$PageList .= $Discussion->Context->GetDefinition('TextSuffix');
			$PageList .= "</font>";
			$DiscussionList = str_replace('class="DiscussionTopicName">' . $Discussion->Name . '</a>', 'class="DiscussionTopicName">' . $Discussion->Name . '</a> ' . $PageList, $DiscussionList);
		}
	}

	// Add to delegate
	$Context->AddToDelegate('DiscussionGrid', 'PostDiscussionOptionsRender', 'DiscussionGrid_DiscussionPages');
}

if (in_array($Context->SelfUrl, array('comments.php','index.php','account.php','categories.php'))) {
	$Head->AddStyleSheet('extensions/DiscussionPages/style.css', 'screen', 100);
}
?>