<?php
/*
Extension Name: Comments Permalinks
Extension Url: http://lussumo.com/docs/
Description: Adds a "permalink" link to every comment, so you can share a single comment's url.
Version: 1.0
Author: Remi Cieplicki
Author Url: http://remouk.alt-tab.org/

You should cut & paste this language definition into your
conf/your_language.php file (replace "your_language" with your chosen language,
of course):
*/

$Context->Dictionary['Permalink'] = 'permalink';

function CommentGrid_Permalinks(&$CommentGrid) {
		global $RowNumber;
		
		$CommentList = &$CommentGrid->DelegateParameters["CommentList"];
		$CommentList .= '<a href="#Item_'. $RowNumber .'" id="Permalink_'. $RowNumber .'">' . $CommentGrid->Context->GetDefinition("Permalink").'</a>';
		
		$RowNumber++;
}

$RowNumber = 1;
$Context->AddToDelegate("CommentGrid", "PostCommentOptionsRender", "CommentGrid_Permalinks");
?>