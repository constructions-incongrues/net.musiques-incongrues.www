<?php
/*
Extension Name: Guest Post
Extension Url: http://lussumo.com/docs/
Description: Adds the "Add Comments" form to the page for unauthenticated users, along with a username and password input, allowing users who have not yet signed in to do so and post a message at the same time, or guests to post as 'guest' (based on Mark's 'Add Comments')
Version: 1.4.1
Author: Gerrard Cowburn
Author Url: http://int0rw3b.com/
*/

//YOU MUST SET UP A GUEST ACCOUNT AND DEFINE THE USERNAME AND PASSWORD FOR IT HERE.
//I STRONGLY RECOMMEND YOU DO NOT USE AN EASY TO GUESS PASSWORD
//Replace "Guest" with your guest account username (keep the single quotes)
define('GuestUsername', 'Guest');
//Replace "guest" with your guest account password (keep the single quotes)
define('GuestPassword', 'iamalooserbeacauseimaguest');
//Do you want to use CAPTCHA?  1 for yes, 0 for no.
define('GuestPostCaptcha','0');

$Context->SetDefinition('GuestPostWarning', 'If the following boxes are left blank your comment will be posted as Guest.');
$Context->SetDefinition('GuestPostSpamCheck', 'Please enter the following code:');

if (in_array($Context->SelfUrl, array('comments.php', 'post.php'))) {

      $Context->AddToDelegate('CommentGrid',
            'Constructor',
            'CommentGrid_ShowGuestPostForm');

      function CommentGrid_ShowGuestPostForm(&$CommentGrid) {
         if ($CommentGrid->ShowForm == 0
            && $CommentGrid->Context->Session->UserID == 0
            && ($CommentGrid->pl->PageCount == 1 || $CommentGrid->pl->PageCount == $CommentGrid->CurrentPage)
            && ((!$CommentGrid->Discussion->Closed && $CommentGrid->Discussion->Active))
            && $CommentGrid->CommentData ) $CommentGrid->ShowForm = 1;
      }

      if ($Context->Session->UserID <= 0) {
         $Context->AddToDelegate('DiscussionForm',
            'CommentForm_PreWhisperInputRender',
            'CommentForm_AddGuestPostInfo');

         function CommentForm_AddGuestPostInfo(&$DiscussionForm) {
            echo '<label for="GuestPostWarning">'.$DiscussionForm->Context->GetDefinition('GuestPostWarning').'</label><br />';
            echo '<table border="0" cellpadding="0" cellspacing="0">
               <tr>
                  <td class="CredentialsLabel LabelUsername">'.$DiscussionForm->Context->GetDefinition('Username').'</td>
                  <td class="CredentialsLabel LabelPassword">'.$DiscussionForm->Context->GetDefinition('Password').'</td>';
		  if (GuestPostCaptcha == 1) echo '<td class="CredentialsLabel LabelUsername">'.$DiscussionForm->Context->GetDefinition('GuestPostSpamCheck').'</td>';
            echo '</tr>
               <tr>
                  <td class="CredentialsInput InputUsername"><input type="text" name="Username" value="'.FormatStringForDisplay(ForceIncomingString('Username', '')).'" /></td>
                  <td class="CredentialsInput InputPassword"><input type="password" name="Password" value="'.FormatStringForDisplay(ForceIncomingString('Password', '')).'" /></td>';
		  if (GuestPostCaptcha == 1) echo '<td class="CredentialsInput InputUsername"><script language="JavaScript" name="randyPic"><!--
showImage();
//--></script><input type="text" name="SpamCheck" /></td>';
            echo '</tr>
            </table>';
         }

         $Head->AddStyleSheet('extensions/GuestPost/style.css');
	 $Head->AddScript('extensions/GuestPost/randy.js');

         $Context->AddToDelegate('DiscussionForm',
            'PreSaveComment',
            'DiscussionForm_SignInGuest');

         function DiscussionForm_SignInGuest(&$DiscussionForm) {
            if ($DiscussionForm->PostBackAction == 'SaveComment') {
		$GU = GuestUsername;
		$GP = GuestPassword;

               $Password = ForceIncomingString('Password', $GP);
		if ($Password == $GP) {
	               $Username = $GU;
		} else {
			$Username = ForceIncomingString('Username','');
		}
               $UserManager = $DiscussionForm->Context->ObjectFactory->NewContextObject($DiscussionForm->Context, 'UserManager');

	      if ((ForceIncomingString('very','1') == ForceIncomingString('SpamCheck','2')) || GuestPostCaptcha == 0) {
               if (!$UserManager->ValidateUserCredentials($Username, $Password, 0)) {
                  $DiscussionForm->PostBackAction = 'SaveCommentFailed';
                  $DiscussionForm->Context->Session->UserID = -1;

                  $DiscussionForm->Comment->Clear();
                  $DiscussionForm->Comment->GetPropertiesFromForm();
                  $DiscussionForm->Comment->DiscussionID = $DiscussionForm->DiscussionID;
                  $dm = &$DiscussionForm->DelegateParameters['DiscussionManager'];
                  $DiscussionForm->Discussion = $dm->GetDiscussionById($DiscussionForm->Comment->DiscussionID);
                  $DiscussionForm->Comment->FormatPropertiesForDisplay(1);
               } elseif ($Password == $GP) {
			$DisplayName = ForceIncomingString('Username',$GU);
			//$Comment = &$DiscussionForm->DelegateParameters['Comment'];
			$DiscussionForm->Comment->Body = $DiscussionForm->Comment->Body;
			$DiscussionForm->Comment->FormatType = "Html";
	       }
	      } else {
                  $DiscussionForm->PostBackAction = 'SaveCommentFailed';
                  $DiscussionForm->Context->Session->UserID = -1;

                  $DiscussionForm->Comment->Clear();
                  $DiscussionForm->Comment->GetPropertiesFromForm();
                  $DiscussionForm->Comment->DiscussionID = $DiscussionForm->DiscussionID;
                  $dm = &$DiscussionForm->DelegateParameters['DiscussionManager'];
                  $DiscussionForm->Discussion = $dm->GetDiscussionById($DiscussionForm->Comment->DiscussionID);
                  $DiscussionForm->Comment->FormatPropertiesForDisplay(1);
	      }
            }
         }

         $Context->AddToDelegate('DiscussionForm','PostSaveComment','DiscussionForm_SignOutGuest');
         function DiscussionForm_SignOutGuest(&$DiscussionForm) {
            if (isset($GP) && isset($Password)) {
               if ($DiscussionForm->PostBackAction == 'SaveComment' && $Password == $GP) {
                  $DiscussionForm->Context->Session->End($DiscussionForm->Context->Authenticator);
               }
            }
         }
      }
}

?>
