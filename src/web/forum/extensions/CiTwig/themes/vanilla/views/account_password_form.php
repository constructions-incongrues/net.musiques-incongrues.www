{% if self.Context.Session.UserID != self.User.UserID and not self.Context.Session.User.Permission('PERMISSION_EDIT_USERS') %}
	{{ self.Context.WarningCollector.Add(self.Context.GetDefinition('PermissionError')) }}

<div id="Form" class="Account Password">
	{{ self.Get_Warnings()|raw }}
</div>

{% else %}
	{{ self.PostBackParams.Set('PostBackAction', 'ProcessPassword') }}
	{{ self.PostBackParams.Set('u', self.User.UserID) }}
<div id="Form" class="Account Password">
	<fieldset>
		<legend>{{ self.Context.GetDefinition('ChangeYourPassword') }}</legend>'

		{{ self.CallDelegate('PreWarningsRender') }}

		{{ self.Get_Warnings()|raw }}
		{{ self.Get_PostBackForm('frmAccountPassword')|raw }}

		{{ self.CallDelegate('PreInputsRender') }}

		<ul>
			<li>
				<label for="txtOldPassword">{{ self.Context.GetDefinition('YourOldPassword') }} <small>{{ self.Context.GetDefinition('Required') }}</small></label>
				<input type="password" name="OldPassword" value="{{ self.User.OldPassword }}" maxlength="100" class="SmallInput" id="txtOldPassword" />
				<p class="Description">{{ self.Context.GetDefinition('YourOldPasswordNotes') }}</p>
			</li>
			<li>
				<label for="txtNewPassword">{{ self.Context.GetDefinition('YourNewPassword') }} <small>{{ self.Context.GetDefinition('Required') }}</small></label>
				<input type="password" name="NewPassword" value="{{ self.User.NewPassword }}" maxlength="100" class="SmallInput" id="txtNewPassword" />
				<p class="Description">{{ self.Context.GetDefinition('YourNewPasswordNotes') }}</p>
			</li>
			<li>
				<label for="txtConfirmPassword">{{ self.Context.GetDefinition('YourNewPasswordAgain') }} <small>{{ self.Context.GetDefinition('Required') }}</small></label>
				<input type="password" name="ConfirmPassword" value="{{ self.User.ConfirmPassword }}" maxlength="100" class="SmallInput" id="txtConfirmPassword" />
				<p class="Description">{{ self.Context.GetDefinition('YourNewPasswordAgainNotes') }}</p>
			</li>
		</ul>

		{{ self.CallDelegate('PreButtonsRender') }}

		<div class="Submit">
			<input type="submit" name="btnSave" value="{{ self.Context.GetDefinition('Save') }}" class="Button SubmitButton" />
			<a href="{{ GetUrl(self.Context.Configuration, "account.php", "", "u", self.User.UserID) }}" class="CancelButton">{{ self.Context.GetDefinition('Cancel') }}</a>
		</div>
		</form>
	</fieldset>
</div>
{% endif %}