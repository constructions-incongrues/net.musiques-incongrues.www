===================================
EXTENSION INSTALLATION INSTRUCTIONS
===================================

In order for Vanilla to recognize an extension, it must be contained within its own directory within the extensions directory. So, once you have downloaded and unzipped the extension files, you can then place the folder containing the default.php file into your installation of Vanilla. The path to your extension's default.php file should look like this:

/path/to/vanilla/extensions/SubCategories/default.php

Once this is complete, you can enable the extension through the "Manage Extensions" form on the settings tab in Vanilla.

Make sure you have the most up to date version by checking Settings > Updates & Reminders > Check for updates now (and setting reminders).

The latest zip will be in the lussumo.com add-on repository:

http://lussumo.com/addons/index.php?PostBackAction=AddOn&AddOnID=312


================================
CREATING SUB-(-SUB...)CATEGORIES
================================

Creating a sub-category is easy, just create a category in the normal way but  place dash-spaces ("- ", minus the quotes) infront of it's name.

You can now embed unlimited levels of sub-categories by appending more dash-spaces infront of category names; the more dash-spaces, the lower down the hierachy the category falls. A simplistic example of multiple embedded sub-categories goes like so:

Main Category
- Sub Category
- - Sub Sub Category
- - - Sub Sub Sub Category

You should make sure that your order is correct, otherwise some may not display.


=================================
THEMING YOUR NEW (SUB-)CATEGORIES
=================================

To override the look (theme) of sub-categories, copy the following file into your theme folder and then edit it:

/path/to/vanilla/extensions/SubCategories/theme/sub-categories.php

You can alter where this appears by overriding the name of the delegate it attaches to in your settings.php file (if you don't know what a delegate is, don't alter it). The variable is:

$Configuration['SUBCATEGORIES_DELEGATE']

It defaults to "PreRender".

To add the amount of sub-categories a category has to the category list, you need to edit your categories.php theme file, you should add below:

         <li class="CategoryDiscussionCount">
            <span>'.$this->Context->GetDefinition('Discussions').'</span> '.$Category->DiscussionCount.'
         </li>';

this:

   $CategoryList .= '         <li class="CategoryDiscussionCount">
            <span>'.$this->Context->GetDefinition('SubCategories').'</span> '.SubCategoryCount($Category->CategoryID).'
         </li>';

If you wish to have the discussion count of the category include all sub-categories discussion counts, you should change the line:

            <span>'.$this->Context->GetDefinition('Discussions').'</span> '.$Category->DiscussionCount.'


to this:

            <span>'.$this->Context->GetDefinition('Discussions').'</span> '.SubDiscussionCount($Category->CategoryID).'

This is just an example of how you can change the default Vanilla theme to include updated discussion and sub-category counts.


=======
CONTACT
=======

SubCategories is currently maintained by Adam Dunkley - adam [at] webality [dot] co [dot] uk