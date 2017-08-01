# Bot for Vanilla

Sometimes you want a [minion](https://github.com/vanilla/minion) to do your dirty work, but sometimes you want a _personality_ to make your community management more fun. This project is for the latter.

More fun than sock puppet accounts, Bot is a tool for kickstarting a new community or bringing an old one together with a shared experience and knowledge.

Bot has customizable triggers that allow it to participate and take actions in your community as a (bot) member. 

**Bot is for experienced Vanilla plugin developers.** It is a framework for building a clean bot implementation. On its own it doesn't do much of anything. It certainly is not plugin-and-play for non-developers. This guide assumes you understand event handling in Vanilla well.

## Using Bot

First, register your (potential) replies. Each reply gets an event name that will be fired like a normal Vanilla event.

You assign a priority order for possible replies. After any event handler sets a reply on Bot, the rest are skipped. By setting the reply to `true` you can prevent _any_ reply being made.

In your `structure()` method, set your replies with their priority level:

`botReply($eventName, $priority);`

Lowest number goes first. Ties may go in any order. If no priority is given, they are prioritized in the order they are set by your plugin. If multiple plugins set unprioritized replies, there may be unpredictable results (plugins may load in any order).

Events are thrown by the `Bot` object. Therefore in your plugin, event handlers for Bot are given the bot instance as `$sender` (the first argument). So you'll be writing event handlers like this:

`public function bot_eventName_handler($bot) { ... }`

You can get additional data from the `$bot` (just `$sender` by another name) by calling its methods:

* `user()` provides the triggering user's data.
* `discussion()` provides the triggering discussion's data.
* `context()` will tell you if you're in a comment or discussion.
* `body()` gives the full text of the triggering post if you need it.

Because of the convenience methods available, many times you won't need the above info at all.

## Replying with Bot

When deciding whether to respond to a particular post, you have some helpful methods available to you via the `Bot` object passed to your event handler:

* `match($text)` returns `true` or `false` whether the body of the post contains the exact `$text`.
* `regex($pattern, $matches)` returns `true` or `false` whether the body of the post contains the regex `$pattern`. Matches are passed back via `$matches`. See `preg_match()`.

When it's time to reply, just use `setReply($string)`. All further reply events are skipped. First reply is best reply.

There's some data & convenience formatting available to you when crafting your reply:

* `mention()` returns a string of an `@` prepended to the username of the author of the triggering post.

Your reply handler should return a fully formatted string of the post you want the bot to reply with. By defaut, Bot expects Markdown. You can change this with `format($newFormat)`.

## Bot tricks

Bot has a few second-tier capabilities as of 1.2 and beyond.

* `randomize()` lets you pick a random reply from a list without repeating yourself too soon.
* `state()` and `getState()` let you pull off multi-step actions by "remembering" where you are in a sequence.
* `online()` will make your bot user appear online (if you're also using the [Who's Online](https://vanillaforums.org/addon/whosonline-plugin) plugin).
* `adlib()` will build a Mad Lib from the template and options passed to it.
* Some commands are now chainable: `$bot->setState()->setReply()->say()->online();`

## Timed events

Bot learned how to tell time in 1.3.

* Caching is required to use timed events (e.g. memcached).
* Bot now fires events named `minute`, `hourly`, and `daily` at the expected intervals.
* These represent approximate, squishy intervals. It does not run like a clock; they will be delayed a bit.
* Timed events require site traffic to fire. Therefore the level of delay will depend on your level of site traffic (higher = more accurate).
* Example hook: `bot_hourly_handler($sender) { /* stuff */ }`

## Design considerations

* All reply events are fired on every new post until a `true` is returned by one of them. Complex conditions or computations on a busy site could overburden your server.
* Create unique event names so they do not overlap with other plugins.
* Multiple replies may have the same priority. They will be triggered in the order declared, which may be random between plugins.
* Add `'RequiredPlugins' => array('bot' => '1.0'),` to your plugin info.
* Call `botReplyDisable($name)` for all your replies in your plugin's `onDisable()` to prevent unnecessary event throwing.
* You can use `setReply(true)` to say nothing and skip all further reply events.

## Example plugin using Bot

```
<?php
$PluginInfo['shwaipbot'] = array(
   'Name' => 'shwaipbot',
   'Description' => "Example implementation of Bot.",
   'Version' => '1.0',
   'RequiredApplications' => array('Vanilla' => '2.2'),
   'RequiredPlugins' => array('Bot' => '1.0'),
   'MobileFriendly' => true,
   'Author' => "Lincoln Russell",
);

class ShwaipbotPlugin extends Gdn_Plugin {

    /**
     * Simple call and response.
     *
     * User: Shave and a hair cut!
     * Bot: TWO BITS!
     */
    public function bot_shave_handler($bot) {
        if ($bot->match('shave and a hair cut')) {
            $bot->setReply($bot->mention().' TWO BITS!');
        }
    }

    /**
     * Let users send each other beers thru the bot.
     *
     * User: !beer @Lincoln
     * Bot:  /me slides @Lincoln a beer.
     */
    public function bot_sendBeer_handler($bot) {
        if ($bot->pattern('(^|[\s,\.>])\!beer\s@(\w{1,50})\b', $beerTo)) {
            $bot->setReply('/me slides @'.val(2, $beerTo).' a beer.');
        }
    }
    
    /**
     * Just do structure.
     */
    public function setup() {
        $this->structure();
    }

    /**
     * Register replies.
     */
    public function structure() {
        botReply('shave');
        botReply('sendBeer');
    }
}
```

## History

### 1.4 (Aug 2016)

* Add `adlib()` feature.
* Add `reset()` ability to un-pollute Bot.
* Automatically show Bot online when it speaks.

### 1.3 (Jan 2016)

* Add timed events.
* Fix `botID()` param type.
* Add this changelog.
* Add GitHub repo to plugin info.

### 1.2 (Jan 2016)

* Add `online()`, `randomize()`, `state()`, and `setState()` to Bot.
* Make `say()` & `setReply()` chainable.
* Fix `replyDisable()`.
* Make `match()` case insensitive.

### 1.1 (Aug 2015)

* Add `afterSay` event.
* Fix setting `context`, `body`, and `format`.
* Add `botID()`.
* Remove `fireReply()` mechanism (just use normal events instead).

### 1.0 (Aug 2015)

* Initial release.

**This project began in 2013** as a plugin for [Icrontic](https://icrontic.com) named _shwaipbot_. The initial release for Bot was created by abstracting the high-level functionality out of _shwaipbot_ and turning it into an implementation of Bot instead.

The bot Vorgo on [vanillaforums.org](https://vanillaforums.org) has also run on this framework since the initial release.