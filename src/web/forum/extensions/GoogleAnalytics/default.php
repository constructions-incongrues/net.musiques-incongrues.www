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
if (!in_array($Context->SelfUrl, array('settings.php')) && isset($Head))
{
    class AddGoogleAnalytics extends Control
    {
        function Render()
        {
            global $Configuration;
            echo '
            <!-- Google Analytics -->
            <script>
            (function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,"script","//www.google-analytics.com/analytics.js","ga");

            ga("create", "'.$Configuration['GoogleAnalyticsAccountNumber'].'", "auto");
            ga("send", "pageview");

            </script>
            <!-- End Google Analytics -->
';
        }
    }
    $AddGoogleAnalytics = $Context->ObjectFactory->NewContextObject($Context, "AddGoogleAnalytics");
    $Page->AddControl("Page_Render", $AddGoogleAnalytics, $Configuration["CONTROL_POSITION_FOOT"]);
}
