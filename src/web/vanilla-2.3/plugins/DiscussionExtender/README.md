Discussion Extender
===================
A Vanilla Forums plugin that provides an interface to add arbitrary fields to 
discussions via the dashboard.

It is released under the GPLv2 license. Please see LICENSE file for more information.

I want to thank jakeish for sponsoring this plugin.

Install
=======
1. Drop the DiscussionExtender folder into your vanilla/plugins folder
2. Enable the plugin in your dashboard
3. Visit the new settings page at `/dashboard/settings/discussionextender` to 
   manage your fields

What The Creation Options Mean
==============================
**Type**: TextBox will show a standard textbox that can contain arbitrary input. 
Dropdown will have a dropdown selector where the author can choose from the 
pre-defined list of options. Checkbox is a simple boolean input that will show 
the label and a checkbox.

**Position**: This indicates where on the form the field will be inserted.

**Label**: This is the text that will be shown before the field on the new/edit 
discussion form.

**Options**: This is a list of options available for the field if it is a 
Dropdown type.

**Required on all discussions**: If this field is left blank, or an invalid 
input, an error will be shown to the user.

**Show in discussion meta**: If checked, this field will be shown on the 
discussion meta list. Empty fields will not be shown.

**Add a column to the discussion table for this?**:  If this is left unchecked, 
the data will be stored as a serialized attribute in the discussion table. This 
is the preferred method of storing data. If checked, a new column in the 
discussion table will be created for your field. This should only be done for 
fields you know you want to be stored in a separate column or would exceed the 
existing attributes column when serialized.

What The Removal Options Mean
=============================
**Confirm removal of the field:**: This box must be checked to successfully 
delete a field from the system.

**Completely remove all associated data?**: This will cycle through the entire
discussion list and forcefully remove all data currently stored for the selected 
field. This is a permanent change and is not recommended to do on live systems. 
This is also not recommended to do for large datasets (millions of discussions).
