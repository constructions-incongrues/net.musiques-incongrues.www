# constructions-incongrues/vanill-ext-bookmarklet

Adds a bookmarklet that makes it possible to create post from saving website pages metadata. It can extract metadata from a range
a range of websites going from facebook.com to moncul.org. It is compatible with the constructions-incongrues/vanilla-event-type
and constructions-incongrues/vanilla-event-release and will use appropriately the extracted metadata

## Installation

```sh
cd extensions/constructions-incongrues/vanilla-ext-bookmarklet
composer install
```

`conf/extensions.php`

```php
// ...
include($Configuration['EXTENSIONS_PATH']."constructions-incongrues/vanilla-ext-bookmarklet/default.php");
```

## TODO

- [] Unicode character on bookmarklet title
- [x] Never expiring Facebook token
- [] User documentation (with gifcasts !)
- [] Better looking Ananas It button + explanations
- [] Inject configuration in bookmarklet
- [] Tool for local bookmarklet generation
- [] Widen comment body text area if needed
- [] Other platforms metadata extraction
- [] Focus on comment text area after post
- [] Extension scoped composer.json
- [] Fix oembed problems (mixcloud https, bandcamp player not showing up)
- [] empilements.incongru.org integration
- [] ouiedire.net integration
- [] metadata extraction based on the strategy pattern
- [] openagenda => prévenir le mec
- [] rules engine for label extraction from url

## Notes

Bookmarklet generator : https://mrcoles.com/bookmarklet/
