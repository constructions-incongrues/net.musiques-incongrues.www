========================================
FRIENDLY URL'S INSTALLATION INSTRUCTIONS
========================================

Before you do anything, you must have mod_rewrite installed and configured
properly. Without mod_rewrite, following the instructions below will likely
cause a lot of really ugly Apache based errors. For more information about
mod_rewrite and how to install it, Google it:

   http://www.google.ca/search?q=mod_rewrite

If you have mod_rewrite installed, you need to do two things to get the friendly
urls:

1. Open up conf/settings.php and add this:

   $Configuration['URL_BUILDING_METHOD'] = 'mod_rewrite';

2. Copy the content of the apache.conf file in this add-on to the
.htaccess file in the root folder of your Vanilla; create one
if you don't already have one. NB: files starting with a '.',
like .htaccess files, are hidden on Linux and Mac OSX.

That's it. Browse to your Vanilla installation and enjoy those faux-folder urls.