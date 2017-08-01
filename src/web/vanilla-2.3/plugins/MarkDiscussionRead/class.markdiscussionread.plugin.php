<?php

$PluginInfo['MarkDiscussionRead'] = [
    'Name' => 'MarkDiscussionRead',
    'Description' => 'Selectively mark discussions as read.',
    'Version' => '1.0.2',
    'MobileFriendly' => true,
    'Author' => 'Bleistivt',
    'License' => 'GNU GPL2'
];

class MarkDiscussionReadPlugin extends Gdn_Plugin {

    public function discussionController_markRead_create($sender, $args) {
        if (!$sender->Request->isAuthenticatedPostBack()) {
            throw permissionException('Javascript');
        }

        $discussion = (new DiscussionModel())->getID(val(0, $args));
        if (!$discussion) {
            throw notFoundException('Discussion');
        }

        $count = $discussion->CountComments;
        (new CommentModel())->setWatch($discussion, $count, $count, $count);

        $sender->jsonTarget("#Discussion_{$discussion->DiscussionID}", 'New Unread', 'RemoveClass');
        $sender->jsonTarget("#Discussion_{$discussion->DiscussionID} .NewCommentCount", null, 'Remove');
        $sender->jsonTarget("#Discussion_{$discussion->DiscussionID}", 'Read', 'AddClass');

        $discussion->CountUnreadComments = 0;
        $sender->sendOptions($discussion);

        $sender->render('blank', 'utility', 'dashboard');
    }

    public function discussionsController_discussionOptions_handler($sender, $args) {
        if (!Gdn::session()->isValid() || !$args['Discussion']->CountUnreadComments) {
            return;
        }

        $sender->Options .= wrap(anchor(
            T('Mark as read'),
            '/discussion/markread/'.$args['Discussion']->DiscussionID,
            'MarkRead Hijack'
        ), 'li');
    }

    public function categoriesController_discussionOptions_handler($sender, $args) {
        $this->discussionsController_discussionOptions_handler($sender, $args);
    }

}
