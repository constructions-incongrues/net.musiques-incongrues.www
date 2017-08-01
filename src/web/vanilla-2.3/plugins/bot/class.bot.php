<?php
/**
 * @copyright 2015-2016 Lincoln Russell
 * @license GNU GPL2
 * @package Bot
 */

/**
 * Class Bot
 */
class Bot extends Gdn_Plugin {

    /** @var array Discussion we're in. */
    protected $discussion;

    /** @var array User who triggered this mess. */
    protected $user;

    /** @var int Our loveable bot's UserID. */
    protected $botID;

    /** @var string What was said? */
    protected $body;

    /** @var string What triggered this? */
    protected $context;

    /** @var string What do we say back? */
    protected $reply;

    /** @var string What do we say back? */
    protected $format;

    /**
     * First positions.
     */
    public function __construct() {
        parent::__construct();
        $this->reset();
    }

    /**
     * Randomly substitute parts of speech into a phrase template, just like Mad Libs.
     *
     * Each individual selection is run thru `randomize()` to reduce duplicate output.
     * It would be extremely foolhardy to pass user-generated content into this method.
     * Only {keys} appearing in the $options array will be substituted.
     *
     * @example $template = "Did you {get} the latest {software}? It {fixes} your {system} like magic!";
     *    $options = [
     *       'get' = ['download', 'compile', 'grab', 'build', 'get'],
     *       'software' = ['defragger', 'framework', 'antivirus'],
     *       'fixes' = ['destroys', 'rebuilds', 'fixes'],
     *       'system' = ['operating system', 'project', 'tea pot']
     *    ];
     *    Possible outcome: "Did you build the latest framework? It destroys your tea pot like magic!"
     *
     * @param string $template The phrase to be completed by substituting random words.
     * @param array $options Words or phrases to select from in format: [substitution key] => [A, B, C].
     *     The substitution key is not used as a possible selection. Use short, alphanumeric keys.
     * @return string The completed ad-lib.
     */
    public function adlib($template, $options) {
        // Input validation.
        if (!is_string($template) || !is_array($options)) {
            return;
        }

        $result = $template;
        foreach ($options as $key => $suboptions) {
            // Build a nice cache key.
            $state = 'adlib-'.substr(md5($template), 0, 5).'-'.$key;
            // Build a nicely randomized adlib.
            $selected = $this->randomize($state, $suboptions);
            $result = str_replace('{'.$key.'}', $selected, $result);
        }

        return $result;
    }

    /**
     * What was said?
     *
     * @param string $body Set the post body for reference (optional).
     * @return Content of post that triggered this.
     */
    public function body($body = '') {
        if ($body != '') {
            $this->body = (string) $body;
        }
        return $this->body;
    }

    /**
     * What's the userid of our bot?
     *
     * @param int $botID Set the botid (optional).
     * @return int Current botid.
     */
    public function botID($botID = null) {
        if (!is_null($botID)) {
            $this->botID = (int) $botID;
        }
        return $this->botID;
    }

    /**
     * Where was it said?
     *
     * @param string $context Context of the body being replied to (optional).
     * @return string One of: discussion, comment.
     */
    public function context($context = '') {
        if (in_array($context, array('discussion', 'comment'))) { //, 'wallpost', 'wallcomment'
            $this->context = $context;
        }
        return $this->context;
    }

    /**
     * Where are we & who are we talking to?
     *
     * @param array $discussion Set discussion (optional).
     * @return array Discussion data.
     */
    public function discussion($discussion = array()) {
        if ((is_array($discussion) || is_object($discussion)) && count($discussion)) {
            $this->discussion = (array) $discussion;
        }
        return $this->discussion;
    }

    /**
     * What formatting engine to use on our reply. Default is Markdown.
     *
     * @param string $format One of: Html, BBCode, Markdown, TextEx, Wysiwyg, Text (optional).
     * @return string Format being used.
     */
    public function format($format = '') {
        if ($format != '') {
            $this->format = $format;
        }
        return $this->format;
    }

    /**
     * Whether a reply has been set by a bot reply handler.
     *
     * @return bool Whether a reply has been set.
     */
    public function hasReply() {
        return ($this->reply !== false) ? true : false;
    }

    /**
     * Do a simple text match on the body.
     *
     * @param string $text Text to match.
     * @return bool Whether trigger text contains $Text.
     */
    public function match($text) {
        return (strpos(strtolower($this->body), strtolower($text)) !== false);
    }

    /**
     * Formatted mention of user we're interacting with.
     *
     * @return Mention of the user who triggered this.
     */
    public function mention() {
        return '@'.val('Name', $this->user);
    }

    /**
     * Show Bot as online (via Who's Online).
     *
     * @return Bot
     */
    public function online() {
        if (!class_exists('WhosOnlinePlugin')) {
            return;
        }
        $now = Gdn_Format::toDateTime();
        $px = Gdn::sql()->Database->DatabasePrefix;
        $botID = $this->botID();
        $sql = "insert {$px}Whosonline (UserID, Timestamp, Invisible) values ({$botID}, :Timestamp, :Invisible)
            on duplicate key update Timestamp = :Timestamp1, Invisible = :Invisible1";
        Gdn::database()->query($sql, array(':Timestamp' => $now, ':Invisible' => 0, ':Timestamp1' => $now, ':Invisible1' => 0));
        return $this;
    }

    /**
     * Randomize a reply from a list ($options).
     *
     * This is better than just using rand, because we track previous answers and avoid repeats.
     * 70% of possible answers will be stored as a "no repeat" log that cycles.
     *
     * @param string $event Slug.
     * @param array $options Numeric array of string replies.
     * @return string Reply.
     */
    public function randomize($event, $options) {
        // Get what he's said recently
        $previous = $this->state($event, array());
        $total = count($options);
        // Always make sure 30% isn't in the log so it's selectable.
        $limit = round($total * .7);

        // Randomize an option, but skip recently used ones.
        do {
            $selected = rand(0, $total-1);
        } while (in_array($selected, $previous));

        // Store what we chose & say it.
        $previous = array_merge(array($selected), array_slice($previous, 0, $limit));
        $this->setState($event, $previous);

        return val($selected, $options);
    }

    /**
     * Do a regex match on the body.
     *
     * @param string $pattern Regex pattern.
     * @param array $matches See preg_match().
     * @return bool Whether trigger text matches $Pattern.
     */
    public function regex($pattern, &$matches = array()) {
        return (preg_match('/'.$pattern.'/i', $this->body, $matches));
    }

    /**
     * Reset the bot.
     *
     * @return Bot
     */
    public function reset() {
        $this->discussion = [];
        $this->user = [];
        $this->body = '';
        $this->context = 'comment';
        $this->reply = false;
        $this->format = 'Markdown';
        $this->botID = c('Bot.UserID', Gdn::userModel()->getSystemUserID());

        return $this;
    }

    /**
     * Save the current reply as a new comment in the discussion.
     *
     * @return Bot
     */
    public function say() {
        if ($this->reply && $this->reply !== true) {
            $commentModel = new CommentModel();
            $botComment = array(
                'DiscussionID' => val('DiscussionID', $this->discussion),
                'InsertUserID' => $this->botID,
                'Format' => $this->format,
                'Body' => $this->reply
            );
            $commentModel->save($botComment);
        }

        $this->fireEvent('afterSay');

        return $this;
    }

    /**
     * What are we gonna say?
     *
     * Set to `true` to have no reply at all and skip further reply checks.
     *
     * @param string $reply Fully formatted post in set format.
     * @return Bot
     */
    public function setReply($reply) {
        $this->reply = ($reply !== true) ? (string) $reply : true;
        return $this;
    }

    /**
     * Set a state/value into the database.
     *
     * @param $event Slug. 50 characters max. Avoid spaces.
     * @param null $value
     * @param int $userid
     * @return Bot
     */
    public function setState($event, $value = null, $userid = 0) {
        $event = substr(strtolower($event), 0, 50);
        UserModel::setMeta($userid, array($event => $value), 'bot.state.');
        return $this;
    }

    /**
     * Get a state/value in the database.
     *
     * @param $event Slug. 50 characters max. Avoid spaces.
     * @param $default Default value to return if none is set.
     * @param int $userid Use zero for global states.
     * @return int|string|bool
     */
    public function state($event, $default = null, $userid = 0) {
        $event = substr(strtolower($event), 0, 50);
        $meta = UserModel::getMeta($userid, 'bot.state.'.$event, 'bot.state.', $default);
        return val($event, $meta);
    }

    /**
     * Who are we talking to?
     *
     * @param array $user User info (optional).
     * @return array User info.
     */
    public function user($user = array()) {
        if ((is_array($user) || is_object($user)) && count($user)) {
            $this->user = (array) $user;
        }
        return $this->user;
    }
}
