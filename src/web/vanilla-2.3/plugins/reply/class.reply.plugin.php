<?php

$PluginInfo['reply'] = [
    'Name' => 'Reply',
    'Description' => 'Adds an icon "Reply" to posts which scrolls screen to commentbox and inserts "@authorname". It also adds a link to send author a PM. Initial idea is based on the plugin EasyReply written by @andelf.',
    'Version' => '0.3',
    'RequiredApplications' => ['Vanilla' => '>=2.2'],
    'MobileFriendly' => true,
    'HasLocale' => true,
    'Author' => 'Robin Jurinka',
    'AuthorUrl' => 'https://vanillaforums.org/profile/r_j',
    'License' => 'MIT'
];
/**
 * Add links to quickly reply to a post.
 *
 * This plugin adds two links below each comment and each discussion: "Send PM"
 * and "Reply". Send PM will redirect to /messages/add/username/discussionname
 * which creates a new conversation with the author of the post as the recipient
 * and the title of the discussion as the subject.
 *
 * Both links can be deactivated individually by setting reply.SendPM or
 * reply.Comment = false in the config.php.
 */
class ReplyPlugin extends Gdn_Plugin {
    /**
     * Add css and js resources (if needed).
     *
     * @param discussionController $sender Instance of the calling class.
     * @return void.
     */
    public function discussionController_render_before($sender) {
        if (!Gdn::session()->isValid()) {
            return;
        }
        if (c('reply.Comment', true) == true) {
            $sender->addJsFile('reply.js', 'plugins/reply');
        }
        $sender->addCssFile('reply.css', 'plugins/reply');
    }

    /**
     * Add CSS class "Mine" to discussion if session user is the author.
     *
     * Add the same css class to discussions as there is for comments so that
     * reply buttons can be hidden by css for users own posts.
     *
     * @param discussionController $sender Instance of the calling class.
     * @param array $args Event arguments.
     * @return void.
     */
    public function discussionController_beforeDiscussionDisplay_handler($sender, $args) {
        if (Gdn::session()->UserID == $args['Author']->UserID) {
            $args['CssClass'] .= ' Mine';
        }
    }

    /**
     * Add links for replying by PM and replying by comment.
     *
     * @param discussionController $sender Instance of the calling class.
     * @param array $args Event arguments.
     * @return void.
     */
    public function discussionController_replies_handler($sender, $args) {
        if (!Gdn::session()->isValid()) {
            return;
        }
        echo '<div class="Reply">';

        // Insert new message link which will be addressed to the comment
        // author and has the subject of the discussion.
        if (c('reply.SendPM', true) == true) {
            echo wrap(
                anchor(
                    t('Send PM').'<i class="icon icon-mail"> </i>',
                    'messages/add/'.rawurlencode($args['Author']->Name).'/'.$args['Discussion']->Name,
                    ['class' => 'ReplyPM']
                ),
                'span',
                ['class' => 'Reply ReplyPMWrapper']
            );
        }

        // Insert link which will insert a @UserName into the comment box.
        if (c('reply.Comment', true) == true) {
            echo wrap(
                anchor(
                    t('Reply').'<i class="icon icon-reply"> </i>',
                    'post/comment/'.$args['Discussion']->DiscussionID.'/'.rawurlencode($args['Author']->Name),
                    ['class' => 'ReplyComment']
                ),
                'span',
                ['class' => 'Reply ReplyCommentWrapper']
            );
        }
        echo '</div>';
    }

    /**
     * Prefills subject field with second parameter from messages/add.
     *
     * This allows to use /messages/add/user/subject in order to open a new
     * message window where recipient as well as subject is prefilled.
     *
     * This might be part of Vanilla in future times and this method becomes
     * superfluous, but it is needed at least for version 2.2.1 which is the
     * current stable release when this plugin has been written.
     *
     * @param messagesController $sender Instance of the calling class.
     * @return void.
     */
    public function messagesController_beforeMessageAdd_handler($sender) {
        // Don't proceed if subject is not visible at all.
        if (c('Conversations.Subjects.Visible', false) == false) {
            return;
        }
        if (isset($sender->RequestArgs[1])) {
            $sender->Form->setValue('Subject', $sender->RequestArgs[1]);
        }
    }
}
