<?php

$plainTextOldDiscussion = '
Hello ' . $mName . ',
The following comment was posted by ' . $mPosterName . ' in the discussion: ' . $discussionName . '

' . $mComment . '

Visit the following URL to view the comment on the forum:
' . $DiscussionForm->Context->Configuration['BASE_URL'] . 'comments.php?DiscussionID=' . $DiscussionID . '&page=' . $pageNumber . '#Item_' . $jumpToComment . '

Kind regards,
' . $DiscussionForm->Context->Configuration['SUPPORT_NAME'];

$plainTextNewDiscussion = '
Hello ' . $mName . ',
A new discussion called ' . $discussionName . ' was started by ' . $mPosterName . ', the comment is as follows: 

' . $mComment . '

Visit the following URL to view the new discussion on the forum:
' . $DiscussionForm->Context->Configuration['BASE_URL'] . 'comments.php?DiscussionID=' . $DiscussionID . '&page=' . $pageNumber . '#Item_' . $jumpToComment . '

Kind regards,
' . $DiscussionForm->Context->Configuration['SUPPORT_NAME'];

$htmlOldDiscussion = '
<html>
	<head>
		<style type="text/css">
			blockquote {
				padding: 8px 16px;
				margin: 0 0 8px;
				background-color: #E5EAF6;
				border-left: 6px solid #ACBEDF;
				color: #56568F;
			}
			blockquote blockquote {
				border-right: 1px solid #ACBEDF;
				border-top: 1px solid #ACBEDF;
				border-bottom: 1px solid #ACBEDF;
			}
			blockquote cite {
				font-weight: bold;
				display: block;
				margin-bottom: 8px;
				padding-bottom: 8px;
				border-bottom: 1px solid #ACBEDF;
				color: #56568F;
			}
			.CommentQuote {
				display: inline;
				font-size: 10px;
				color: #ccc;
			}
		</style>
	</head>
	<body style="background-color:#fff;">
		Hello ' . $mName . ',<br /><br />
		The following comment was posted in the discussion <strong>' . $discussionName . '</strong>.
		<div style="margin: 20px 0; padding: 10px; background-color: #fef9e9; border: 1px #ffedae solid;">
			<p style="padding: 5px; margin: 0 0 5px 0; background-color: #fff; border: 1px #ccc solid;">
				Post by: <strong>' . $mPosterName . '</strong>
			</p>
			' . $mComment . '<br />
		</div>
		<a href="' . $DiscussionForm->Context->Configuration['BASE_URL'] . 'comments.php?DiscussionID=' . $DiscussionID . '&page=' . $pageNumber . '#Item_' . $jumpToComment . '">Click here to view the comment on the forum</a><br /><br />
		Kind regards,<br />
		' . $DiscussionForm->Context->Configuration['SUPPORT_NAME'] . '
	</body>
</html>';
$htmlNewDiscussion = '
<html>
	<head>
		<style type="text/css">
			blockquote {
				padding: 8px 16px;
				margin: 0 0 8px;
				background-color: #E5EAF6;
				border-left: 6px solid #ACBEDF;
				color: #56568F;
			}
			blockquote blockquote {
				border-right: 1px solid #ACBEDF;
				border-top: 1px solid #ACBEDF;
				border-bottom: 1px solid #ACBEDF;
			}
			blockquote cite {
				font-weight: bold;
				display: block;
				margin-bottom: 8px;
				padding-bottom: 8px;
				border-bottom: 1px solid #ACBEDF;
				color: #56568F;
			}
			.CommentQuote {
				display: inline;
				font-size: 10px;
				color: #ccc;
			}
		</style>
	</head>
	<body style="background-color:#fff;">
		Hello ' . $mName . ',<br /><br />
		A new discussion called <strong>' . $discussionName . '</strong> was started, the comment is as follows.
		<div style="margin: 20px 0; padding: 10px; background-color: #fef9e9; border: 1px #ffedae solid;">
			<p style="padding: 5px; margin: 0 0 5px 0; background-color: #fff; border: 1px #ccc solid;">
				Post by: <strong>' . $mPosterName . '</strong>
			</p>
			' . $mComment . '<br />
		</div>
		<a href="' . $DiscussionForm->Context->Configuration['BASE_URL'] . 'comments.php?DiscussionID=' . $DiscussionID . '&page=' . $pageNumber . '#Item_' . $jumpToComment . '">Click here to view the new discussion on the forum</a><br /><br />
		Kind regards,<br />
		' . $DiscussionForm->Context->Configuration['SUPPORT_NAME'] . '
	</body>
</html>';
?>