<?php
// Note: This file is included from the library/Vanilla/Vanilla.Control.Menu.php class.

echo '<div id="Session">';
	if (in_array($this->Context->Session->UserID, $this->Context->Configuration['BETA_TESTERS_IDS'])) {
		echo '[beta] ';
	}
	if ($this->Context->Session->UserID > 0) {
		echo $this->Context->Session->User->Name
			. ' : <a href="/forum/account/">gérer son compte</a> - <a href="'
			. FormatStringForDisplay(AppendUrlParameters(
				$this->Context->Configuration['SIGNOUT_URL'],
				'FormPostBackKey=' . $this->Context->Session->GetCsrfValidationKey() ))
			. '">'.$this->Context->GetDefinition('SignOut').'</a> - <a href="/forum/page/contact">nous contacter</a>';
	} else {
		echo $this->Context->GetDefinition('NotSignedIn') . ' (<a href="'
			. FormatStringForDisplay(AppendUrlParameters(
				$this->Context->Configuration['SIGNIN_URL'],
				'ReturnUrl='. urlencode(GetRequestUri(0))))
			. '">'.$this->Context->GetDefinition('SignIn').'</a>)';
	}
	echo '</div>';
	if ($this->Context->PageTitle != 'Ici on cause :') {
	echo '
<div id="search">
	<p>

		<form id="SearchSimple" method="get" action="/forum/search/">
			<label for="search" style="color:white">Rechercher</label>
			<input type="text" name="Keywords" class="champs" />
			<input type="hidden" name="PostBackAction" value="Search" />
			<input name="Submit" value="Search" class="valid" type="submit" />
		</form>
	</p>
</div>
	';
	} else {
	echo '
<div id="search" class="notitle">
	<p>

		<form id="SearchSimple" method="get" action="/forum/search/">
			<label for="search" style="color:white">Rechercher</label>
			<input type="text" name="Keywords" class="champs" />
			<input type="hidden" name="PostBackAction" value="Search" />
			<input name="Submit" value="Search" class="valid" type="submit" />
		</form>
	</p>
</div>
	';
	}
	$this->CallDelegate('PreHeadRender');
	echo '<div id="Header">
			<a name="pgtop"></a>
			<h1>
				<a href="/forum/">
					<span class="first">Musiques</span>
					<span class="last">Incongrues</span>
				</a>
			</h1>
			';
			if ($this->Context->PageTitle != 'Ici on cause :') {
				echo sprintf("<h2>&laquo; %s &raquo;</h2>", $this->Context->PageTitle);
			}
	echo 	'<ul>';
				while (list($Key, $Tab) = each($this->Tabs)) {
					echo '<li'.$this->TabClass($this->CurrentTab, $Tab['Value']).' '.$Tab['Attributes'].'><a href="'.$Tab['Url'].'" '.$Tab['Attributes'].'>'.$Tab['Text'].'</a></li>';
				}
			echo '</ul>
	</div>';
	$this->CallDelegate('PreBodyRender');
	echo '<div id="Body">';
?>
