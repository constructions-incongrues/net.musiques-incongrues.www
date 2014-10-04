<?php
/*
Extension Name: Google Analytics
Extension Url: http://lussumo.com/community/discussion/3507/
Description: Adds google analytics code to your vanilla forum pages.
Version: 1.2
Author: dinoboff / ithcy
Author Url: http://lussumo.com/community/discussion/3507/
*/

//replace this with your google analytics account number, of course
$Configuration['GoogleAnalyticsAccountNumber'] = 'UA-673133-2';

//add an array of pages to not analyse
if ( !in_array($Context->SelfUrl, array('settings.php')) && isset($Head) )
{
	class AddGoogleAnalytics extends Control
	{
		function Render()
		{
			global $Configuration;
			echo '
            <script type="text/javascript" src="http://www.google-analytics.com/urchin.js"></script>
            <script type="text/javascript">
            
            // <![CDATA[
            _uacct="'.$Configuration['GoogleAnalyticsAccountNumber'].'";
            urchinTracker();
            // ]]>
            </script>
';
		}
	}
	$AddGoogleAnalytics = $Context->ObjectFactory->NewContextObject($Context, "AddGoogleAnalytics");
	$Page->AddControl("Page_Render", $AddGoogleAnalytics, $Configuration["CONTROL_POSITION_FOOT"]);
}
?>
