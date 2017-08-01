<?php
/**
 * Share what you come up with on vanillaforums.org!
 *
 * @copyright 2015 Lincoln Russell
 * @license GNU GPL2
 * @package Bot
 */

$PluginInfo['bot'] = array(
    'Name' => 'Bot',
    'Description' => 'Program your own bot to reply to catch phrases and special conditions.',
    'Version' => '1.4.1',
    'MobileFriendly' => true,
    'Author' => "Lincoln Russell",
    'AuthorEmail' => 'lincoln@icrontic.com',
    'AuthorUrl' => 'http://lincolnwebs.com',
    'License' => 'GNU GPL2',
    'GitHub' => 'linc/vanilla-bot'
);


/**
 * Controls where the Bot hooks into Vanilla.
 */
class BotPlugin extends Gdn_Plugin {

    /**
     * Bot replies to new comments.
     *
     * @param PostController $sender
     * @param array $args
     */
    public function postController_afterCommentSave_handler($sender, $args) {
        if (!val('Editing', $args)) {
            $post = val('Comment', $args);
            $bot = new Bot();
            $bot->context('comment');
            $bot->discussion(val('Discussion', $args));
            $bot->user(userBuilder($post, 'Insert'));
            $bot->body(val('Body', $post));
            $this->doReplies($bot);
        }
    }

    /**
     * Bot replies to new discussions.
     *
     * @param PostController $sender
     * @param array $args
     */
    public function postController_afterDiscussionSave_handler($sender, $args) {
        if (!val('Editing', $args)) {
            $post = val('Discussion', $args);
            $bot = new Bot();
            $bot->context('discussion');
            $bot->discussion(val('Discussion', $args));
            $bot->user(userBuilder($post, 'Insert'));
            $bot->body(val('Body', $post));
            $this->doReplies($bot);
        }
    }

    /**
     * Have the Bot appear online when it speaks.
     *
     * You can set Bot.Online.AfterSay = false in your config to disable this feature.
     *
     * @param PluginManager $sender
     * @param array $args
     */
    public function bot_afterSay_handler($sender, $args) {
        if (c('Bot.Online.AfterSay', true)) {
            $args['Bot']->online();
        }
    }

    /**
     * Hook into the asyncronous analytics tick event to create a virtual cron job.
     *
     * Requires that memcached is installed with PHP and is configured & enabled in Vanilla.
     * Fires events from Bot object named 'minute', 'hourly', and 'daily'.
     * These events do not actually work like clockwork. They may fire slightly less than their designated frequency.
     * Sites with low traffic may experience unpredictably longer timeframes since a visit is required to trigger these.
     *
     * @param Gdn_Statistics $sender
     */
    public function gdn_statistics_analyticsTick_handler($sender) {
        // This only works if you're using caching.
        if (!Gdn_Cache::activeEnabled()) {
            return;
        }

        // Use cache layer to get a verified lock on firing this event.
        $locked = Gdn::cache()->get('bot.cron.lock', [Gdn_Cache::FEATURE_LOCAL => false]);
        if (!$locked) {
            $key = uniqid();
            Gdn::cache()->store('bot.cron.lock', $key, [Gdn_Cache::FEATURE_EXPIRY => 60]);
            $locked = Gdn::cache()->get('bot.cron.lock', [Gdn_Cache::FEATURE_LOCAL => false]);
            if ($locked == $key) {
                // Fire our base once-per-minute event.
                $bot = new Bot();
                $bot->fireEvent('minute');

                // Is it time for daily or hourly events?
                $counter = Gdn::cache()->get('bot.cron.counter', [Gdn_Cache::FEATURE_LOCAL => false]);
                $counter = ($counter) ? $counter+1 : 1;
                if ($counter % 60 == 0) {
                    $bot->fireEvent('hourly');
                    if ($counter % 3600 == 0) {
                        $bot->fireEvent('daily');
                        $counter = 0; // Daily reset.
                    }
                }

                // Store updated counter.
                Gdn::cache()->store('bot.cron.counter', $counter);
            }
        }
    }

    /**
     * Figure out something clever to say.
     *
     * @param Bot
     */
    public function doReplies($bot) {
        // Get all replies that have been registered.
        //$replies = Gdn::get('bot.replies.%'); // This is bugged.
        $replies = Gdn::userModel()->getMeta(0, 'bot.replies.%', 'bot.replies.');
        asort($replies);

        // Process all possible replies.
        foreach ($replies as $eventName => $priority) {
            // Call bot event handler.
            $bot->fireEvent($eventName);

            // If that event set a reply, let's move on.
            if ($bot->hasReply()) {
                $bot->say();
                break;
            }
        }
    }
}

if (!function_exists('botReply')) :
/**
 * Add a reply to the call stack.
 *
 * @param string $eventName Slug.
 * @param int|bool $priority
 */
function botReply($eventName, $priority = false) {
    // If no priority is set, automatically increment it in the order received.
    static $defaultPriority = 0;
    if (!$priority) {
        // Next consecutive priority.
        $defaultPriority++;
        $priority = $defaultPriority;
    } elseif ($priority > $defaultPriority) {
        // Fast forward our default so that it will be highest existing priority +1.
        $defaultPriority = $priority;
    }

    // Register our reply.
    // Gdn::set('bot.replies.'.$eventName, $priority); // This is bugged. #2923
    Gdn::userModel()->setMeta(0, array($eventName => $priority), 'bot.replies.');
}
endif;

if (!function_exists('botReplyDisable')) :
/**
 * Unregister a reply event.
 *
 * @param string $eventName
 */
function botReplyDisable($eventName) {
    Gdn::userModel()->setMeta(0, array($eventName => null), 'bot.replies.');
}
endif;
