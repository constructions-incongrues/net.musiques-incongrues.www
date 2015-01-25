<?php
// Note: This file is included from the library/Framework/Framework.Control.UpdateCheck.php control.

echo '<div id="Form" class="Account UpdateCheck Extensions">
	<fieldset>
		<legend>'.$this->Context->GetDefinition('UpdateCheck').'</legend>
		<form id="frmUpdateCheck" method="post" action="">
		<input type="hidden" id="FormPostBackKey" name="FormPostBackKey" value="'.$this->Context->Session->GetCsrfValidationKey().'" />
		<div class="Errors Invisible" id="UpdateCheckErrors"></div>
		<ul id="UpdateCheckItems">
			<li id="Core" class="UpdateChecking">
				<div id="CoreName" class="Name">'.APPLICATION.' '.APPLICATION_VERSION.'</div>
				<div id="CoreDetails" class="Details">'.$this->Context->GetDefinition('CheckingForUpdates').'</div>
			</li>';
			if (is_array($this->Extensions)) {
				$ExtensionList = '';
				while (list($ExtensionKey, $Extension) = each($this->Extensions)) {
					$OfficialExtensionsArray = explode (';', $this->Context->Configuration['OFFICIAL_EXTENSIONS']);
					$match = false;
					foreach ($OfficialExtensionsArray as $OfficialExtension) {
						if ($Extension->Name == $OfficialExtension) {
							$match = true;
						}
					}
					if ($match == false) {
						$ExtensionList .= '<li id="'.$ExtensionKey.'" class="UpdateChecking">
							<div id="'.$ExtensionKey.'Name" class="Name">'.$Extension->Name.' '.$Extension->Version.'</div>
							<div id="'.$ExtensionKey.'Details" class="Details">'.$this->Context->GetDefinition('CheckingForUpdates').'</div>
						</li>';
					}
				}
				echo $ExtensionList;
			} else {
				echo '<li><p>'.$this->Context->GetDefinition('NoExtensions').'</p></li>';
			}
		echo '
			</ul>
			<strong>Note:</strong> Official extensions are only released with Vanilla, not separately.<br />This updater will not check for updates for them.
		</form>
	</fieldset>
</div>';
?>