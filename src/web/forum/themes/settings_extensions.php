<?php
// Note: This file is included from the library/Framework/Framework.Control.ExtensionForm.php control.

echo '
	<div id="Form" class="Account Extensions">
		<fieldset>
			<legend>'.$this->Context->GetDefinition('Extensions').'</legend>'.
			$this->Get_Warnings().
			'<form action="#" method="post">
				<p>'.$this->Context->GetDefinition('ExtensionFormNotes').'</p>
				<ul>';
					if (is_array($this->Extensions)) {
						$ExtensionList = '';
						while (list($ExtensionKey, $Extension) = each($this->Extensions)) {
							if ($Extension->Official) {
								$namePrefix = '<strong class="ExtensionNameColor">';
								$nameSuffix = '</strong>';
								$versionSpan = '';
								$authorSpan = '';
								$authorUrlSpan = '';
								$cssOfficialStatusClass = ' Official';
							} else {
								$namePrefix = '';
								$nameSuffix = '';
								$versionSpan = '<span class="Version">'.$Extension->Version.'</span>';
								$authorSpan = '<span class="Author">by '.FormatHyperlink($Extension->AuthorUrl,1,$Extension->Author).'</span>';
								$authorUrlSpan = '<span class="AuthorUrl">'.FormatHyperlink($Extension->Url).'</span>';
								$cssOfficialStatusClass = ' Unofficial';
							}
							$ExtensionList .= '
								<li id="'.$ExtensionKey.'" class="'.($Extension->Enabled ? 'Enabled' : 'Disabled').$cssOfficialStatusClass.'">
									<h3>
										'.GetDynamicCheckBox(
											'chk'.$ExtensionKey,
											1,
											$Extension->Enabled,
											"SwitchExtension('".$this->Context->Configuration['WEB_ROOT']."ajax/switchextension.php', '".$ExtensionKey."', '".$this->SessionPostBackKey."');",
											$namePrefix.$Extension->Name.$nameSuffix
										).
										$versionSpan.
										$authorSpan.
										$authorUrlSpan.'
									</h3>
									<p>'.$Extension->Description.'</p>
								</li>
							';
						}
						echo $ExtensionList;
					} else {
						echo '<li><p>'.$this->Context->GetDefinition('NoExtensions').'</p></li>';
					}
				echo '</ul>
			</form>
		</fieldset>
	</div>
';
?>
