<?php
// Note: This file is included from the library/Vanilla/Vanilla.Control.Menu.php class.

echo '<div id="Session">';
	if ($this->Context->Session->UserID > 0) {
		echo $this->Context->Session->User->Name
			. ' : <a href="/forum/account/">gérer son compte</a> - <a href="'
			. FormatStringForDisplay(AppendUrlParameters(
				$this->Context->Configuration['SIGNOUT_URL'],
				'FormPostBackKey=' . $this->Context->Session->GetCsrfValidationKey() ))
			. '">'.$this->Context->GetDefinition('SignOut').'</a> - <a href="mailto:contact (CHEZ) musiques-incongrues (POINT) net">nous contacter</a>';
	} else {
		echo $this->Context->GetDefinition('NotSignedIn') . ' (<a href="'
			. FormatStringForDisplay(AppendUrlParameters(
				$this->Context->Configuration['SIGNIN_URL'],
				'ReturnUrl='. urlencode(GetRequestUri(0))))
			. '">'.$this->Context->GetDefinition('SignIn').'</a>)';
	}
	echo '</div>';
	$this->CallDelegate('PreHeadRender');
	echo '<div id="Header">
			<a name="pgtop"></a>
			<h1>
				'.$this->Context->Configuration['BANNER_TITLE'].'
			</h1>

			<ul>';
				while (list($Key, $Tab) = each($this->Tabs)) {
					echo '<li'.$this->TabClass($this->CurrentTab, $Tab['Value']).' '.$Tab['Attributes'].'><a href="'.$Tab['Url'].'" '.$Tab['Attributes'].'>'.$Tab['Text'].'</a></li>';
				}
			echo '</ul>
	</div>';
	$this->CallDelegate('PreBodyRender');
	echo '<div id="Body">';
?>
