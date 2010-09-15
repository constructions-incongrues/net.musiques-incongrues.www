<?php
/*
Extension Name: Random Quotes (Header)
Extension Url: http://www.lussumo.com/addons
Description: This will produce a random quote above or below the forum header.
Version: 1.0.2
Author: zero & jimw
Author Url: http://www.lussumo.com/discussions
*/
//Parts of the code come from the Random Simpsons Quotes Module, a Joomla! module by Josh (webmaster@gotgtek.com)

//Add hook
$Context->SetDefinition('RandomQuoteError','Cannot find the quotes file.');
$Context->SetDefinition('RandomQuoteHeaderTitle',"Today's Quote");//You can change Today's Quote to something else or remove it if you don't want a title.

if (in_array($Context->SelfUrl, array("index.php", "account.php", "categories.php", "comments.php", "post.php", "search.php", "settings.php"))) {
	$Head->AddStyleSheet('extensions/RandomQuotesHeader/style.css');
	//For a header banner quote
	class QuoteBanner extends Control {
		function QuoteBanner(&$Context) {
			$this->Name = "QuoteBanner";
			$this->Control($Context);
		}
		function Render() {
			$quotesPath = 'extensions/RandomQuotesHeader'; //Path to the randomquotes text file.
			$quotesSource = 'quotes.txt'; //Name of the randomquotes text file.
			$GLOBAL["quotesPath"] = $quotesPath;
			$lineSeparator = "\n";
			$quotesString = '';
			$quoteFile = fopen( implode("/", array ( $quotesPath, $quotesSource)), "r") or DIE($this->Context->GetDefinition('RandomQuoteError'));
			$i = 0;
			while (!feof($quoteFile)) {
				$readstring = fgets($quoteFile, 1024);
				if (strlen(trim($readstring)) > 1) {
					$i++;
					$quotesString .= $readstring;
				}
			}
			fclose($quoteFile);
			$quotes = explode( $lineSeparator, $quotesString); //Here we split it into lines...
			$chosen = $quotes[ mt_rand(0, $i - 1 ) ]; //...and then randomly choose a line.
			//This just echoes the chosen line, we'll position it later.
			preg_match ( "/^([^:]+).(.*)/", $chosen, $matches);
			list ($nada, $quote ) = $matches;
//			$quotedisplay = '<p class="QuoteTitle">' .$this->Context->GetDefinition('RandomQuoteHeaderTitle') .'</p>';
			$quotedisplay = '<p class="QuoteBody">' .$quote .'</p>';
			echo '<div class="BannerContainer">
			<div class="Banner">
			<div class="BannerMenu">';
			echo $quotedisplay;
			echo '</div>
			</div>
			</div>';
		}
	}
	$QuoteBanner = new QuoteBanner($Context);
	
	// Only use one of the following - comment out the one you don't want to use
		// The following adds the quote at the top of the screen
		$Page->AddRenderControl($QuoteBanner,$Configuration["CONTROL_POSITION_MENU"]);
		//print_r($Configuration);
		// The following adds the quote after the header and before any Announcement you have set
		//$Page->AddRenderControl($QuoteBanner,$Configuration["CONTROL_POSITION_BODY_ITEM"]-10);
}
?>
