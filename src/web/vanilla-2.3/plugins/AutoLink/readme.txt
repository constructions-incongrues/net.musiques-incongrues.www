original Plugin by @peregrine,Redistributed by VrijVlinder with  Peregrine's permission.


Thanks for using the Plugin.

Please go to the settings page in the dashboard for this plugin.
It is always a good idea to disable the plugin and then update to new version, and then re-enable, and check settings.


you can modify your autolinks via the  class.autolink.plugin.php

look for $wordlinkArray around line 90 and set the word (or words) you are looking to link and the link.
The search will search for one word, or two and three word phrases.   Each word to search for must be between 4 and 15 characters.

the plugin may not work with  unicode and you will need to read other discussions relating to the plugin and  dealing with unicode. 

caveat:  inline images must not have tags or autolinks within the title or alt.

e.g.
if "homerun"  is in your title and is a tag or autolink precede the homerun with a dash.  

i.e.  in title and alt  change homerun to -homerun

from

//bad
<img alt="homerun image" src="http://w5.vanillicon.com/551750a6e2dc8df0429928f3b745be34_50.png" title="homerun">

change above to 

//good
<img alt="-homerun image" src="http://w5.vanillicon.com/551750a6e2dc8df0429928f3b745be34_50.png" title="-homerun">

