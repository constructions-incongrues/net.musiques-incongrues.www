<?php
/**
 * Used by RoleHistory in extensions/RoleHistory/default.php
 */
if (!defined('IN_VANILLA')) {
	exit();
}

echo '<h2>'.$this->Context->GetDefinition("RoleHistory").'</h2>
					<ul>';
// Loop through the user's role history
$UserHistory = $this->Context->ObjectFactory->NewObject(
	$this->Context,
	"UserRoleHistory");

while ($Row = $this->Context->Database->GetRow($this->History)) {
	$UserHistory->Clear();
	$UserHistory->GetPropertiesFromDataSet($Row);
	$UserHistory->FormatPropertiesForDisplay($this->Context);

	echo '<li>
			<h3>
				'
			. $UserHistory->Role
			. ' <small>('
			. TimeDiff($this->Context, $UserHistory->Date, mktime())
			. ')</small></h3>
				
			<p class="Info">
				'.str_replace("//1",
				($UserHistory->AdminUserID == 0 ?
					$this->Context->GetDefinition("Applicant")
					: "<a href=\""
						.GetUrl(
							$this->Context->Configuration,
							"account.php",
							"", "u",
							$UserHistory->AdminUserID)
						."\">".$UserHistory->AdminUsername."</a>")
			,
			$this->Context->GetDefinition("RoleAssignedByX")).'
			</p>
			<p class="Note">
				'.$UserHistory->Notes.'
			</p>
		</li>';
}
echo "</ul>";
