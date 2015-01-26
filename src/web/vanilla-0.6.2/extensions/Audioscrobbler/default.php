<?php
/*
Extension Name: Audioscrobblerizer
Extension Url: http://lussumo.com/addons/
Description: Allows users to add their Audioscrobbler (last.fm) status onto their account profile
Version: 0.2
Author: Alex Marshall
Author Url: http://www.iambigred.com/
*/

// Many thanks to Caoimhin Saile (MethCat) for his help fixing a few compatibility issues with the Vanilla feed extensions

require ('lastRSS.php');

// Define the required Customizations for this extension
$Context->Configuration['CUSTOMIZATION_AUDIOSCROBBLER_USERNAME'] = '';
$Context->Dictionary['CUSTOMIZATION_AUDIOSCROBBLER_USERNAME'] = 'Audioscrobbler Username';
$Context->Dictionary['CUSTOMIZATION_AUDIOSCROBBLER_USERNAME_DESCRIPTION'] = 'You can add your most 10 recent played songs to your account by providing your Audioscrobbler username.';
$Context->Dictionary['AUDIOSCROBBLER_SETTINGS'] = 'Audioscrobbler Settings';
$Context->Dictionary['AudioscrobblerTitle'] = 'Recently Played Songs';
$Context->Dictionary['AudioscrobblerError'] = 'Unable to obtain your recently played songs from Audioscrobbler';

// Attach to the user account being viewed if there is no postback action
if ($Context->SelfUrl == 'account.php' && ForceIncomingString('PostBackAction', '') == '') {
   // Retrieve the RSS Feed before the page is rendered so errors can be trapped properly

   function Account_RetrieveAudioscrobbler(&$Account) {
         		global $Configuration;

      // If there is a Audioscrobbler username defined, retrieve the feed
      if ($Account->User->Customization('CUSTOMIZATION_AUDIOSCROBBLER_USERNAME') != '') {
        $RSSUrl = "http://ws.audioscrobbler.com/1.0/user/" . $Account->User->Customization('CUSTOMIZATION_AUDIOSCROBBLER_USERNAME') . "/recenttracks.rss";
		// Uncomment line below to use a test feed
		// $RSSUrl = "http://www.dur.ac.uk/st-cuthberts.jcr/forum/extensions/Audioscrobbler/test.xml";
		
		// Create lastRSS object
		$rss = new lastRSS;

		// Set cache dir and cache time limit 
		$rss->cache_dir = $Configuration['EXTENSIONS_PATH'] . 'Audioscrobbler/cache';
		$rss->cache_time = 1200;
		$rss->connection_time = 5;

		$toInsert = '<h2>'.$Account->Context->GetDefinition('AudioscrobblerTitle').'</h2>
			<div id="Audioscrobbler" class="clearfix">';
			if ($rs = $rss->get($RSSUrl)) {
				$toInsert = $toInsert .  "<ul>\n";
            foreach ($rs['items'] as $item) {
				$toInsert = $toInsert . "\t<li><h3><a href=\"$item[link]\" title=\"$item[description]\">$item[title]</a></h3><p>" . @substr($item[pubDate], 0, -9) . "</p></li>\n";
            }
            if ($rs['items_count'] <= 0) { $toInsert = $toInsert . "<li>".$Account->Context->GetDefinition('AudioscrobblerError')."</li>"; }
            $toInsert = $toInsert . "</ul>\n";
			}
			else {
			$toInsert = $toInsert . "<ul><li>".$Account->Context->GetDefinition('AudioscrobblerError')."</li></ul>";
			}
			$toInsert = $toInsert . '</div>';
		$Account->DelegateParameters['Audioscrobbler'] = $toInsert;
		}
   }

   // Render the feed
   function Account_RenderAudioscrobbler(&$Account) {
      if (array_key_exists('Audioscrobbler', $Account->DelegateParameters)) {
         echo $Account->DelegateParameters['Audioscrobbler'];
      }
   }
      
   $Context->AddToDelegate('Account',
      'Constructor',
      'Account_RetrieveAudioscrobbler');
      
   $Context->AddToDelegate('Account',
      'PostProfileRender',
      'Account_RenderAudioscrobbler');
}

?>