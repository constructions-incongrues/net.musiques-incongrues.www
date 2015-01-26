<?php

echo '
	<li>
		<label for="txtFirstName">'.$ApplyForm->Context->GetDefinition('FirstName').'</label>
		<input id="txtFirstName" type="text" name="FirstName" value="'.$ApplyForm->Applicant->FirstName.'" class="Input" maxlength="40" />
	</li>
	<li>
		<label for="LastName">'.$ApplyForm->Context->GetDefinition('LastName').'</label>
		<input id="txtLastName" type="text" name="LastName" value="'.$ApplyForm->Applicant->LastName.'" class="Input" maxlength="40" />
	</li>
';