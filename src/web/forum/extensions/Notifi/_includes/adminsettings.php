<?php

$Context->SetDefinition('ExtensionOptions', 'Extension Options');

class NotifiForm extends PostBackControl {
	var $ConfigurationManager;

	function NotifiForm(&$Context) {
		$this->Name = 'NotifiForm';
		$this->ValidActions = array('Notifi', 'ProcessNotifi');
		$this->Constructor($Context);
		if (!$this->Context->Session->User->Permission('PERMISSION_MANAGE_EXTENSIONS')) {
			$this->IsPostBack = 0;
		} else if ($this->IsPostBack) {
			$SettingsFile = $this->Context->Configuration['APPLICATION_PATH'] . 'conf/settings.php';
			$this->ConfigurationManager = $this->Context->ObjectFactory->NewContextObject($this->Context, 'ConfigurationManager');
			if ($this->PostBackAction == 'ProcessNotifi' && $this->IsValidFormPostBack()) {
				$this->ConfigurationManager->GetSettingsFromForm($SettingsFile);
				$this->ConfigurationManager->DefineSetting('NOTIFI_ALLOW_ALL', ForceIncomingBool('NOTIFI_ALLOW_ALL', 0), 0);
				$this->ConfigurationManager->DefineSetting('NOTIFI_ALLOW_CATEGORY', ForceIncomingBool('NOTIFI_ALLOW_CATEGORY', 0), 0);
				$this->ConfigurationManager->DefineSetting('NOTIFI_ALLOW_DISCUSSION', ForceIncomingBool('NOTIFI_ALLOW_DISCUSSION', 0), 0);
				$this->ConfigurationManager->DefineSetting('NOTIFI_ALLOW_BBCODE', ForceIncomingBool('NOTIFI_ALLOW_BBCODE', 0), 0);
				$this->ConfigurationManager->DefineSetting('NOTIFI_FORMAT_PLAINTEXT', ForceIncomingBool('NOTIFI_FORMAT_PLAINTEXT', 0), 0);
				$this->ConfigurationManager->DefineSetting('NOTIFI_AUTO_ALL', ForceIncomingBool('NOTIFI_AUTO_ALL', 0), 0);
				if ($this->ConfigurationManager->SaveSettingsToFile($SettingsFile)) {
					header('Location: ' . GetUrl($this->Context->Configuration, 'settings.php', '', '', '', '', 'PostBackAction=Notifi&Success=1'));
				} else {
					$this->PostBackAction = 'Notifi';
				}
			}
		}
		$this->CallDelegate('Constructor');
	}

	function Render() {
		if ($this->IsPostBack) {
			$this->CallDelegate('PreRender');
			$this->PostBackParams->Clear();
			if ($this->PostBackAction == 'Notifi') {
				$this->PostBackParams->Set('PostBackAction', 'ProcessNotifi');
				echo '<div id="Form" class="Account NotifiSettings">';
				if (ForceIncomingInt('Success', 0)) {
					echo '<div id="Success">' . $this->Context->GetDefinition('ChangesSaved') . '</div>';
				}
				echo '
							<fieldset>
								<legend>' . $this->Context->GetDefinition("Notifi") . '</legend>
								' . $this->Get_Warnings() . '
								' . $this->Get_PostBackForm('frmNotifi') . '
								<ul>
									<li>
										<p><span>' . GetDynamicCheckBox('NOTIFI_ALLOW_ALL', 1, $this->ConfigurationManager->GetSetting('NOTIFI_ALLOW_ALL'), '', $this->Context->GetDefinition('AdminAllowAll')) . '</span></p>
									</li>
									<li>
										<p><span>' . GetDynamicCheckBox('NOTIFI_ALLOW_CATEGORY', 1, $this->ConfigurationManager->GetSetting('NOTIFI_ALLOW_CATEGORY'), '', $this->Context->GetDefinition('AdminAllowCategories')) . '</span></p>
									</li>
									<li>
										<p><span>' . GetDynamicCheckBox('NOTIFI_ALLOW_DISCUSSION', 1, $this->ConfigurationManager->GetSetting('NOTIFI_ALLOW_DISCUSSION'), '', $this->Context->GetDefinition('AdminAllowDiscussions')) . '</span></p>
									</li>
									<li>
										<p><span>' . GetDynamicCheckBox('NOTIFI_ALLOW_BBCODE', 1, $this->ConfigurationManager->GetSetting('NOTIFI_ALLOW_BBCODE'), '', $this->Context->GetDefinition('AdminAllowBbcode')) . '</span></p>
									</li>
									<li>
										<p><span>' . GetDynamicCheckBox('NOTIFI_FORMAT_PLAINTEXT', 1, $this->ConfigurationManager->GetSetting('NOTIFI_FORMAT_PLAINTEXT'), '', $this->Context->GetDefinition('AdminFormatPlaintext')) . '</span></p>
									</li>
									<li>
										<p><span>' . GetDynamicCheckBox('NOTIFI_AUTO_ALL', 1, $this->ConfigurationManager->GetSetting('NOTIFI_AUTO_ALL'), '', $this->Context->GetDefinition('AdminAutoAll')) . '</span></p>
									</li>
								</ul>
								<div class="Submit">
									<input type="submit" name="btnSave" value="' . $this->Context->GetDefinition('Save') . '" class="Button SubmitButton" />
									<a href="' . GetUrl($this->Context->Configuration, $this->Context->SelfUrl) . '" class="CancelButton">' . $this->Context->GetDefinition('Cancel') . '</a>
								</div>
							</fieldset>
						</form>
					</div>
				';
			}
			$this->CallDelegate('PostRender');
		}
	}

}

$NotifiForm = $Context->ObjectFactory->NewContextObject($Context, 'NotifiForm');
$Page->AddRenderControl($NotifiForm, $Configuration["CONTROL_POSITION_BODY_ITEM"] + 1);

$ExtensionOptions = $Context->GetDefinition('ExtensionOptions');
$Panel->AddList($ExtensionOptions, 20);
$Panel->AddListItem($ExtensionOptions, $Context->GetDefinition('Notifi'), GetUrl($Context->Configuration, 'settings.php', '', '', '', '', 'PostBackAction=Notifi'));
?>