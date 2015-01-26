===================================
EXTENSION INSTALLATION INSTRUCTIONS
===================================

In order for Vanilla to recognize an extension, it must be contained within it's
own directory within the extensions directory. So, once you have downloaded and
unzipped the extension files, you can then place the folder containing the
default.php file into your installation of Vanilla. The path to your extension's
default.php file should look like this:

/path/to/vanilla/extensions/UnansweredDiscussions/default.php

Once this is complete, you can enable the extension through the "Manage
Extensions" form on the settings tab in Vanilla.

SETTINGS
========
In the 'settings' section of the default.php file, you'll be able to decide how
to access the Unanswered Discussions grid:
UD_ADD_TAB      set to 'true' for a tab
UD_ADD_FILTER   set to 'true' for a filter
