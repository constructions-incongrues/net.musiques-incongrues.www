Discussants
===========

PlugIn for Vanilla showing images of all discussion participants on discussion index. Plugin activation scans your database and alters discussion table, so this might take some time...

Version 1 has been overworked very much. So best way to upgrade is to deactivate the plugin, replace the folders content and then reenable it. I have tested it with default theme, Bittersweet and Bootstrap.

New in Version 1.0
The layout is _heavily_ inspired by GitHub: avatars are interlaced and transform to a nice list on hover.
Title now shows "Username [count]".
CSS doesn't take percentaged participation into account.
Code has been changed to adhere to the convention of Vanilla 2.2.
Deleting a users content will activate a rescan of the participated discussions and recreating the content of a banned user also forces a rescan of the affected discussions (thanks to @ozonorojo for pointing me to that)