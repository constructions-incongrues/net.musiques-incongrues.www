<?php defined('APPLICATION') or die;

$PluginInfo['Discussants'] = array(
    'Name' => 'Discussants',
    'Description' => 'Shows the avatars of all discussants in discussion indexes and also the count of discussants',
    'Version' => '1.0.1',
    'HasLocale' => true,
    'Author' => 'Robin Jurinka',
    'AuthorUrl' => 'http://vanillaforums.org/profile/r_j',
    'RequiredApplications' => array('Vanilla' => '>=2.2'),
    'RequiredTheme' => false,
    'RequiredPlugins' => false,
    'MobileFriendly' => true,
    'License' => 'MIT'
);

/**
 * Vanilla Plugin that shows a list of avatars at the discussion list.
 *
 * The avatars get different classes depending on their participation in the discussion.
 *
 * @package Discussants
 * @author Robin Jurinka
 * @license MIT
 */
class DiscussantsPlugin extends Gdn_Plugin {
    /**
     * Change db, initialize settings, etc.
     *
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function setup() {
        if (!c('Discussants.MaxVisible')) {
            saveToConfig('Discussants.MaxVisible', 20);
        }
        $this->structure();
        $this->calculate();
    }

    /**
     * Add a new column to table Discussion.
     *
     * The additional column holds a serialized array of following
     * format: [[user->postingcount], [user->percentage], author, last annotator]
     *
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function structure() {
        // Discussants = serialized arr(arr(user->postingcount)
        // , arr(user->percentage), author, last annotator)
        Gdn::structure()
            ->table('Discussion')
            ->column('Discussants', 'text', true)
            ->set(false, false);
    }

    /**
     * Return discussants of a discussion.
     *
     * @param integer $discussionID ID of the disucssion from which the discussants should be returned.
     * @return mixed array of users in the discussion specified with the discussion ID.
     * @package Discussants
     * @since 1.0
     */
    private function get($discussionID) {
        $result = Gdn::sql()
            ->select('Discussants')
            ->from('Discussion')
            ->where('DiscussionID', $discussionID)
            ->get()
            ->resultArray();
        return unserialize($result[0]['Discussants']);
    }

    /**
     * Write list of discussion members to discussion table.
     *
     * @param integer $discussionID The discussion where the info should be added.
     * @param mixed $discussants Discussion member info.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    private function set($discussionID, $discussants) {
        Gdn::sql()
            ->update('Discussion')
            ->set('Discussants', serialize($discussants))
            ->where('DiscussionID', $discussionID)
            ->put();
    }

    /**
     * Updates Discussants info in discussion
     *
     * @param type $discussionID
     */

    /**
     * Extract discussants info from discussion/comments.
     *
     * If discussionID is not provided, it will recalculate discussants
     * info for _all_ discussions.
     * excludeCommentID must only be specified if one special comment should
     * not be counted (when it will be deleted, e.g.).
     *
     * @param integer $discussionID ID of discussion to (re-)calculate or blank.
     * @param integer $excludeCommentID Comment that should be ignored or blank.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    private function calculate($discussionID = 0, $excludeCommentID = 0){
        if (is_array($discussionID)) {
            foreach ($discussionID as $dID) {
                $this->calculate($dID, $excludeCommentID);
            }
        }
        $discussionModel = new DiscussionModel();
        $commentModel = new CommentModel();

        $discussionID = (int)$discussionID;
        if ($discussionID > 0) {
            $discussion = $discussionModel->getID($discussionID);
            $discussions = array();
            $discussions[0]['DiscussionID'] = $discussion->DiscussionID;
            $discussions[0]['InsertUserID'] = $discussion->InsertUserID;
        } else {
            // Update Discussants of _all_ discussions if no id is given.
            $discussions = $discussionModel->get()->resultArray();
        }

        foreach ($discussions as $discussion) {
            $discussionID = $discussion['DiscussionID'];
            $userID = $discussion['InsertUserID'];
            // Init values
            $discussants = array(
                array($userID => 1) // InsertUser has one post in this discussion
                , array($userID => 100) // that makes up 100%
                , $discussion['InsertUserID'] // he is th first
                , $discussion['InsertUserID'] // and the last author
            );
            // We are forced to give a limit, so choose an extremely high value.
            $comments = $commentModel->get($discussionID, 9999999)->resultArray();

            foreach ($comments as $comment) {
                // Only count comments that will not be deleted.
                if ($comment['CommentID'] != $excludeCommentID) {
                    $userID = $comment['InsertUserID'];
                    $discussants[0][$userID] = $discussants[0][$userID] + 1;
                    $discussants[1][$userID] = 100; // init percentage array
                }
            }

            // Set last author.
            $discussants[3] = $userID;
            // Calculate percentage on base of current max posting count
            $maxPostingCount = max($discussants[0]);
            foreach ($discussants[1] as $user => $value) {
                $discussants[1][$user] = intval(ceil($discussants[0][$user] / $maxPostingCount * 100));
            }
            $this->set($discussionID, $discussants);
        }
    }

    /**
     * After a discussion is saved, update discussants.
     *
     * @param object $sender DiscussionModel.
     * @param mixed $args EventArguments.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function discussionModel_afterSaveDiscussion_handler($sender, $args) {
        $this->calculate($args['DiscussionID']);
    }

    /**
     * After a discussion is saved, update discussants.
     *
     * @param object $sender CommentModel.
     * @param mixed $args EventArguments.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function commentModel_afterSaveComment_handler($sender, $args) {
        $this->calculate($args['FormPostValues']['DiscussionID']);
    }

    /**
     * Rebuild discussants when a comment is deleted.
     *
     * @param object $sender CommentModel.
     * @param mixed $args EventArguments.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function commentModel_deleteComment_handler($sender, $args) {
        $commentID = $args['CommentID'];
        $discussionID = $args['Discussion']->DiscussionID;

        // Remain Vanilla 2.1 compatible where Discussion is not passed in $args.
        if (!$discussionID) {
            $commentModel = new CommentModel();
            $comment = $commentModel->getID($commentID);
            $discussionID = $comment->DiscussionID;
        }

        $this->calculate($discussionID, $commentID);
    }

    /**
     * Rebuild discussants when users content is deleted.
     *
     * @param object $sender UserModel.
     * @param mixed $args EventArguments.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function userModel_beforeDeleteUser_handler($sender, $args) {
        $discussionIDs = array();
        foreach ($args['Content']['Comment'] as $comment) {
            $discussionIDs[] = $comment['DiscussionID'];
        }
        foreach ($args['Content']['Discussion'] as $discussion) {
            $discussionIDs[] = $discussion['DiscussionID'];
        }
        $discussionIDs = array_unique($discussionIDs);

        $this->calculate($discussionIDs);
    }

    /**
     * Rebuild discussants when content is restored.
     *
     * @param object $sender LogModel.
     * @param mixed $args EventArguments.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function logModel_afterRestore_handler($sender, $args) {
        if ($args['Log']['RecordType'] == 'Comment') {
            $commentModel = new CommentModel();
            $comment = $commentModel->getID($args['InsertID']);
            $this->calculate($comment->DiscussionID);
        } elseif ($args['Log']['RecordType'] == 'Discussion') {
            $this->calculate($args['InsertID']);
        }
    }

    /**
     * Add CSS resource.
     *
     * @param object $sender CategoriesController.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function categoriesController_render_before($sender) {
        $sender->addCssFile($this->getResource('design/custom.css', false, false));
    }

    /**
     * Add CSS resource.
     *
     * @param object $sender DiscussionsController.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function discussionsController_render_before($sender) {
        // $sender->addCssFile($this->getResource('design/custom.php', false, false));
        $sender->addCssFile($this->getResource('design/custom.css', false, false));
    }

    /**
     * Add CSS resource.
     *
     * @param object $sender ProfileController.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function profileController_render_before($sender) {
        $sender->addCssFile($this->getResource('design/custom.css', false, false));
    }

    /**
     * Add markup to show discussants.
     *
     * @param object $sender CategoriesController.
     * @param mixed $args EventArguments.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function categoriesController_afterDiscussionTitle_handler($sender, $args) {
        $this->discussantsView($sender, $args);
    }

    /**
     * Add markup to show discussants.
     *
     * @param object $sender DiscussionsController.
     * @param mixed $args EventArguments.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function discussionsController_afterDiscussionTitle_handler($sender, $args) {
        $this->discussantsView($sender, $args);
    }

    /**
     * Add markup to show discussants.
     *
     * @param object $sender ProfileController.
     * @param mixed $args EventArguments.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function profileController_afterDiscussionTitle_handler($sender, $args) {
        $this->discussantsView($sender, $args);
    }

    /**
     * Add markup to show discussant count.
     *
     * @param object $sender CategoriesController.
     * @param mixed $args EventArguments.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function categoriesController_afterCountMeta_handler($sender, $args) {
        $this->discussantsCountView($sender, $args);
    }

    /**
     * Add markup to show discussant count.
     *
     * @param object $sender CategoriesController.
     * @param mixed $args EventArguments.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function discussionsController_afterCountMeta_handler($sender, $args) {
        $this->discussantsCountView($sender, $args);
    }

    /**
     * Add markup to show discussant count.
     *
     * @param object $sender CategoriesController.
     * @param mixed $args EventArguments.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function profileController_afterCountMeta_handler($sender, $args) {
        $this->discussantsCountView($sender, $args);
    }

    /**
     * Shows avatars of all members in the discussion.
     *
     * @param object $sender Vanilla Controller.
     * @param mixed $args EventArguments.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function discussantsView($sender, $args) {
        // Get Discussants of current Discussion.
        $discussants = unserialize($args['Discussion']->Discussants);
        if ($discussants == false) {
            return;
        }

        // Get all user in discussion.
        $userModel = new UserModel();
        $users = $userModel->getIDs(array_keys($discussants[1]));

        $discussantsCounter = 0;
        $maxVisible = c('Discussants.MaxVisible', 20);
        $maxVisibleLimit = count($discussants[1]) > ($maxVisible + 2);

        echo '<ul class="DiscussantsContainer">';

        // Loop through all discussants and format as list.
        foreach ($discussants[1] as $key => $value) {
            // Add class that reflects the percentaged participation.
            $cssClass = 'Discussants'.intval($value/10);

            $discussantsCounter += 1;
            // Start sub group if more than max participants.
            if (($discussantsCounter == $maxVisible) && $maxVisibleLimit) {
                echo '<li><ul class="HiddenDiscussantsContainer HoverHelp">';
            }
            // Create list entry with avatar and "username [count]" title.
            echo '<li class="',$cssClass,'">',userPhoto($users[$key], array('Title' => $users[$key]['Name'].' ['.$discussants[0][$key].']')),'</li>';
          }

        // Close sublist if it has been created.
        if (($discussantsCounter >= $maxVisible) && $maxVisibleLimit) {
            echo '</ul></li>';
        }
        echo '</ul>';
    }

    /**
     * Shows number of different discussants in discussion.
     *
     * @param object $sender Vanilla Controller.
     * @param mixed $args EventArguments.
     * @return void.
     * @package Discussants
     * @since 1.0
     */
    public function discussantsCountView($sender, $args) {
        // Get Discussants of current Discussion.
        $discussants = unserialize($args['Discussion']->Discussants);
        if ($discussants != false) {
            // Display discussants count together with discussion meta.
            echo '<span class="MItem DiscussantCount">',plural(count($discussants[1]), '1 discussant', '%s discussants'),'</span>';
        }
    }
}
