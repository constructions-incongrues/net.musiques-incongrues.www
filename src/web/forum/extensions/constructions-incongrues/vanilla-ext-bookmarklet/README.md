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

- [] User documentation (with gifcasts !)
- [] Better looking Ananas It button + explanations
- [] Inject configuration in bookmarklet
- [] Widen comment body text area if needed
- [] Focus on comment text area after post
- [] Fix oembed problems (mixcloud https, bandcamp player not showing up)
- [] openagenda => prévenir le mec
- [] rules engine for label extraction from url
- [] use traits for parameterbag initialization on Mapper and Matcher
- [] add a template system for post bodies
- [] in WebExtractor use Request instead of array $query
- [] inject Request via services.yml (could RequestStack help ?)
- [] use symfony/expression language
- [] rename to constructions-incongrues/vanilla-extension-bookmarks ?
- [x] Extension scoped composer.json
- [x] Unicode character on bookmarklet title
- [x] Tool for local bookmarklet generation
- [x] Other platforms metadata extraction
- [x] empilements.incongru.org integration
- [x] ouiedire.net integration
- [x] metadata extraction based on the strategy pattern
- [x] Never expiring Facebook token
- [x] mappers: force_value & map must be default capabilities

## Development

### Bookmarklet compilation

```sh
npm install -g bookmarklet
bookmarklet ./assets/bookmarklet.js > ./assets/bookmarklet.compiled.js
```
