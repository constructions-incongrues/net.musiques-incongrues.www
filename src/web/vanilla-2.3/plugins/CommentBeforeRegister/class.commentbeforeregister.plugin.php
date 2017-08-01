<?php

$PluginInfo['CommentBeforeRegister'] = array(
	'Description' => 'Offer comment form to guests instead of login button and ask for login or registration on submit.',
	'Version' => '0.1',
	'RequiredApplications' => array('Vanilla' => '2.1'),
	'MobileFriendly' => true,
	'License' => 'MIT',
	'Author' => "Martin Tschirsich",
	'AuthorEmail' => 'm.tschirsich@gmx.de'
);

class CommentBeforeRegisterPlugin extends Gdn_Plugin {
	public static $ApplicationFolder = 'plugins/CommentBeforeRegister';
	
	public function __construct($Sender = '') {
		parent::__construct($Sender, self::$ApplicationFolder);
	}
	
	public function DiscussionController_AfterComments_Handler($Sender) {
		$Session = Gdn::session();
		$Discussion = $Sender->data('Discussion');
		$PermissionCategoryID = val('PermissionCategoryID', $Discussion);
		
		$UserCanComment = $Session->checkPermission('Vanilla.Comments.Add', TRUE, 'Category', $PermissionCategoryID);
		
		// Check if user would be shown a register or login-button:
		if (!$UserCanComment && !Gdn::session()->isValid()) {

			// Set permission to add comment for this runtime only:
			$Session->setPermission('Vanilla.Comments.Add', array($PermissionCategoryID ));
			
			// Add fix for non-embedded comment stashing in js/global.js:
			$Sender->AddJsFile('script.js', self::$ApplicationFolder);
						
			// Add fix for non-embedded comment stashing in vanilla/views/post/comment.php:
			$Sender->setdata('ForeignUrl', $Sender->SelfUrl);
		} else {
			
			// Add fix for non-embedded comment stashing in DiscussionController::index():
			if (!$Sender->Form->getValue('Body')) {
				$StashComment = $Session->Stash('CommentForDiscussionID_' . $Discussion->DiscussionID, '', false);
				if ($StashComment) {
					$Sender->Form->setValue('Body', $StashComment);
					$Sender->Form->setFormValue('Body', $StashComment);
				}
			}
		}
	}
}