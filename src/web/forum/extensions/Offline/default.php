<?php
/*
Extension Name: Offline
Extension Url: http://lussumo.com/addons
Description: Close the forum for maintainance 
Version: 1.4
Author: MightyMango (Based on Mary by squirrel)
Author Url: http://mightymango.com

Note: Requires Vanilla 1.1.3 or above

Version 1.4
- Fixes problem where board was not going offline if time set to unlimited (thanks timfire)

Version 1.3
- Fixes problem where setting closed time as 'Unlimited' failed.
- Resets Offline status if timeout has expired.
- Added check for Redirect()

*/

	// Make sure the Redirect function is available
	if (!function_exists('Redirect')) $Context->WarningCollector->Add("Offline requires the Redirect() function. Please make sure that you have the latest version of Vanilla. If you do please check for the function in the 'library/Framework/Framework.Functions' file.");


// Set up dictionary definitions
$Context->SetDefinition('ExtensionOptions', 'Extension Options');
$Context->SetDefinition('Offline', 'Offline');
$Context->SetDefinition('OfflineSettings', 'Offline Settings');
$Context->SetDefinition('PERMISSION_USE_OFFLINE_FORUM', 'Can use the forum normally when it is offline');
$Context->SetDefinition('OfflineMessageLabel', 'Message');
$Context->SetDefinition('OfflineMessage', 'Sorry, the message board is closed at the moment. Please try again later.');
$Context->SetDefinition('OfflineNotes', 'Set the forum offline and the message that is displayed.');
$Context->SetDefinition('OfflineOffline', 'Message');
$Context->SetDefinition('OfflineStatus', 'Take the message board offline');
$Context->SetDefinition('OfflineTimeout', 'Message board will be offline for:');
$Context->SetDefinition('OfflineTimeoutMessage', 'Choose how long you would like the message board to be offline. If you select \'Unlimited time\' it will stay offline until you manually put it back online.');
$Context->SetDefinition('OfflineError', 'You do not have the correct privilages to be able to sign-in to a closed message board. Please change your role settings.');


/****** DO NOT EDIT BELOW THIS LINE ******/

// Get Permission to use offline
if( !array_key_exists('PERMISSION_USE_OFFLINE_FORUM', $Configuration)) {
	AddConfigurationSetting($Context, 'PERMISSION_USE_OFFLINE_FORUM', '0');
}
$PERMISSION_USE_OFFLINE_FORUM = $Context->Configuration['PERMISSION_USE_OFFLINE_FORUM'];

// Get offline status
if( !array_key_exists('FORUM_IS_OFFLINE', $Configuration)) {
	AddConfigurationSetting($Context, 'FORUM_IS_OFFLINE', '0');
}
$FORUM_IS_OFFLINE = $Context->Configuration['FORUM_IS_OFFLINE'];

// Get offline message
if( !array_key_exists('FORUM_OFFLINE_MESSAGE', $Configuration)) {
	AddConfigurationSetting($Context, 'FORUM_OFFLINE_MESSAGE', $Context->GetDefinition('OfflineMessage'));
}
$FORUM_OFFLINE_MESSAGE = $Context->Configuration['FORUM_OFFLINE_MESSAGE'];

// Get offline timeout length
if( !array_key_exists('FORUM_OFFLINE_TIMEOUT_LENGTH', $Configuration)) {
	AddConfigurationSetting($Context, 'FORUM_OFFLINE_TIMEOUT_LENGTH', "0");
}
$FORUM_OFFLINE_TIMEOUT_LENGTH = $Context->Configuration['FORUM_OFFLINE_TIMEOUT_LENGTH'];

// Get offline timeout 
if( !array_key_exists('FORUM_OFFLINE_TIMEOUT', $Configuration)) {
	AddConfigurationSetting($Context, 'FORUM_OFFLINE_TIMEOUT', "0");
}
$FORUM_OFFLINE_TIMEOUT = $Context->Configuration['FORUM_OFFLINE_TIMEOUT'];

//Reset Offline status if timeout has expired
if ((time() >= $FORUM_OFFLINE_TIMEOUT) and ($FORUM_OFFLINE_TIMEOUT_LENGTH != 0) and $FORUM_IS_OFFLINE)  {
  AddConfigurationSetting($Context, 'FORUM_IS_OFFLINE', 0);
}


// Check to see if the user can access this page
if (@$FORUM_IS_OFFLINE && !$Context->Session->User->Permission('PERMISSION_USE_OFFLINE_FORUM') && ((time() < $FORUM_OFFLINE_TIMEOUT) || ($FORUM_OFFLINE_TIMEOUT_LENGTH == 0))) {

	if ($Context->SelfUrl != 'people.php') {
		$Context->Session->End($Context->Authenticator);
		redirect($Context->Configuration['base_url'].'people.php');
	} elseif (ForceIncomingString('PostBackAction', '') != 'SignIn') {
		if (@$FORUM_OFFLINE_MESSAGE) {
			$Context->WarningCollector->Add($FORUM_OFFLINE_MESSAGE);		
		}
		unset($_GET['PostBackAction']);
		unset($_POST['PostBackAction']);
	}
}

if ($Context->SelfUrl == "settings.php" && $Context->Session->User->Permission('PERMISSION_CHANGE_APPLICATION_SETTINGS')) {

	class OfflineForm extends PostBackControl {
		var $ConfigurationManager;
    
		function OfflineForm(&$Context) {
		
		
			$this->Name = 'OfflineForm';
			$this->ValidActions = array('Offline', 'ProcessOffline');
			
			//Set FORUM_IS_OFFLINE to 0 if it is not set 
			if(!isset($_POST['FORUM_IS_OFFLINE'])) {
			   $_POST['FORUM_IS_OFFLINE'] = 0;
			}
				
			$this->Constructor($Context);
			if (!$this->Context->Session->User->Permission('PERMISSION_CHANGE_APPLICATION_SETTINGS')) {
				$this->IsPostBack = 0;
			} elseif( $this->IsPostBack ) {
				$SettingsFile = $this->Context->Configuration['APPLICATION_PATH'].'conf/settings.php';
				$this->ConfigurationManager = $this->Context->ObjectFactory->NewContextObject($this->Context, 'ConfigurationManager');
				if ($this->PostBackAction == 'ProcessOffline') {
					
					//Get the form values
					$this->ConfigurationManager->GetSettingsFromForm($SettingsFile);      
          
					if ($this->Context->Session->User->Permission('PERMISSION_USE_OFFLINE_FORUM'))
					{
					if ($this->ConfigurationManager->SaveSettingsToFile($SettingsFile)) {
					AddConfigurationSetting($this->Context,  'FORUM_OFFLINE_TIMEOUT', strtotime('+'.ForceIncomingInt('FORUM_OFFLINE_TIMEOUT_LENGTH', 0).' hour'));
					
						redirect(GetUrl($this->Context->Configuration, 'settings.php', '', '', '', '', 'PostBackAction=Offline&Success=1'));
					} else {
						$this->PostBackAction = 'Offline';
					}
					}
					else {$this->PostBackAction = 'Offline';}
				}
			}
			$this->CallDelegate('Constructor');
		}

		function Render() {
			if ($this->IsPostBack) {
				$this->CallDelegate('PreRender');
				$this->PostBackParams->Clear();
				if ($this->PostBackAction == 'Offline') {
					$this->PostBackParams->Set('PostBackAction', 'ProcessOffline');					
					
					 if (!$this->Context->Session->User->Permission('PERMISSION_USE_OFFLINE_FORUM'))
					 $this->Context->WarningCollector->Add($this->Context->GetDefinition("OfflineError"));
										
					echo '
					<div id="Form" class="Account OfflineSettings">';
					if (ForceIncomingInt('Success', 0)) echo '<div id="Success">'.$this->Context->GetDefinition('ChangesSaved').'</div>';
					echo '
						<fieldset>
							<legend>'.$this->Context->GetDefinition("OfflineSettings").'</legend>
							'.$this->Get_Warnings().'
							'.$this->Get_PostBackForm('frmOffline').'
							'.$this->Context->GetDefinition("OfflineNotes");
							
							echo '<ul>
							
						
                <li>
									<p><span>'.GetDynamicCheckBox('FORUM_IS_OFFLINE', 1, $this->ConfigurationManager->GetSetting('FORUM_IS_OFFLINE'), '', $this->Context->GetDefinition('OfflineStatus')).'</span></p>
                </li>

								
								<li>
									<label for="txtOfflineMessage">'.$this->Context->GetDefinition("OfflineMessageLabel").'</label>
									<input type="text" name="FORUM_OFFLINE_MESSAGE" id="txtOfflineMessage"  value="'.ltrim(trim($this->ConfigurationManager->GetSetting('FORUM_OFFLINE_MESSAGE')),'/').'" maxlength="255" class="SmallInput" style="width: 95%;"/>
								</li>
								
								<li>
								  <label for="txtOfflineTimeoutLength"><b>'.$this->Context->GetDefinition("OfflineTimeout").'</b></label>
								  <select name="FORUM_OFFLINE_TIMEOUT_LENGTH" id="txtOfflineTimeoutLength" style="width: 120px;">
                    <option value="0"'.(($this->ConfigurationManager->GetSetting('FORUM_OFFLINE_TIMEOUT_LENGTH') == 0)?' selected':'').'>Unlimited time</option>
                    <option value="1"'.(($this->ConfigurationManager->GetSetting('FORUM_OFFLINE_TIMEOUT_LENGTH') == 1)?' selected':'').'>1 hour</option>
                    <option value="2"'.(($this->ConfigurationManager->GetSetting('FORUM_OFFLINE_TIMEOUT_LENGTH') == 2)?' selected':'').'>2 hours</option>
                    <option value="3"'.(($this->ConfigurationManager->GetSetting('FORUM_OFFLINE_TIMEOUT_LENGTH') == 3)?' selected':'').'>3 hours</option>
                    <option value="4"'.(($this->ConfigurationManager->GetSetting('FORUM_OFFLINE_TIMEOUT_LENGTH') == 4)?' selected':'').'>4 hours</option>
                    <option value="5"'.(($this->ConfigurationManager->GetSetting('FORUM_OFFLINE_TIMEOUT_LENGTH') == 5)?' selected':'').'>5 hours</option>
                    <option value="6"'.(($this->ConfigurationManager->GetSetting('FORUM_OFFLINE_TIMEOUT_LENGTH') == 6)?' selected':'').'>6 hours</option>
                    <option value="7"'.(($this->ConfigurationManager->GetSetting('FORUM_OFFLINE_TIMEOUT_LENGTH') == 7)?' selected':'').'>7 hours</option>
                    <option value="8"'.(($this->ConfigurationManager->GetSetting('FORUM_OFFLINE_TIMEOUT_LENGTH') == 8)?' selected':'').'>8 hours</option>
                    <option value="9"'.(($this->ConfigurationManager->GetSetting('FORUM_OFFLINE_TIMEOUT_LENGTH') == 9)?' selected':'').'>9 hours</option>
                    <option value="10"'.(($this->ConfigurationManager->GetSetting('FORUM_OFFLINE_TIMEOUT_LENGTH') == 10)?' selected':'').'>10 hours</option>
								  </select>
							
								</li>					
								</ul>
								<p>'.$this->Context->GetDefinition("OfflineTimeoutMessage").'</p>
							<div class="Submit">
								<input type="submit" name="btnSave" value="'.$this->Context->GetDefinition('Save').'" class="Button SubmitButton" />
								<a href="'.GetUrl($this->Context->Configuration, $this->Context->SelfUrl).'" class="CancelButton">'.$this->Context->GetDefinition('Cancel').'</a>
							</div>
							
							
							</form>
						</fieldset>
					</div>
					';
				}
				$this->CallDelegate('PostRender');
			}
		}
	}

	$OfflineForm = $Context->ObjectFactory->NewContextObject($Context, 'OfflineForm');
	$Page->AddRenderControl($OfflineForm, $Configuration["CONTROL_POSITION_BODY_ITEM"] + 1);
	
	$ExtensionOptions = $Context->GetDefinition('ExtensionOptions');
	$Panel->AddList($ExtensionOptions, 20);
	$Panel->AddListItem($ExtensionOptions, $Context->GetDefinition('Offline'), GetUrl($Context->Configuration, 'settings.php', '', '', '', '', 'PostBackAction='.$Context->GetDefinition('Offline')));
}


?>
