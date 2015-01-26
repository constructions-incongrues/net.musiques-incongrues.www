===================================
EXTENSION INSTALLATION INSTRUCTIONS
===================================

In order for Vanilla to recognize an extension, it must be contained within it's
own directory within the extensions directory. So, once you have downloaded and
unzipped the extension files, you can then place the folder containing the
default.php file into your installation of Vanilla. The path to your extension's
default.php file should look like this:

/path/to/vanilla/extensions/IpHistory/default.php

Once this is complete, you can enable the extension through the "Manage
Extensions" form on the settings tab in Vanilla.

You will also need to make Vanilla log ip addresses if it isn't set up to do this
already. Go to the Settings tab, "Application Settings" and check the box next
to "Log & monitor all IP addresses".