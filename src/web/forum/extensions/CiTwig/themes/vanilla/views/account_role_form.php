{% if not self.Context.Session.User.Permission('PERMISSION_CHANGE_USER_ROLE') %}
	{{ self.Context.WarningCollector.Add(self.Context.GetDefinition('PermissionError')) }}
	<div id="Form" class="Account Role">
		{{ self.Get_Warnings()|raw }}
	</div>
{% else %}
	{{ self.PostBackParams.Set('PostBackAction', 'ProcessRole') }}
	{{ self.PostBackParams.Set('u', self.User.UserID) }}
	<div id="Form" class="Account Role">
		<fieldset>
			<legend>{{ self.Context.GetDefinition('ChangeRole') }}: {{ self.User.Name }}</legend>
			{{ self.Get_Warnings()|raw }}
			{{ self.Get_PostBackForm('frmRole')|raw }}
			<ul>
				<li>
					<label for="ddRoleID">{{ self.Context.GetDefinition('AssignToRole') }} <small>{{ self.Context.GetDefinition('Required') }}</small></label>
					{{ self.RoleSelect.Get()|raw }}
					<p class="Description">{{ self.Context.GetDefinition('AssignToRoleNotes') }}</p>
				</li>
				<li>
					<label for="txtNotes">{{ self.Context.GetDefinition('RoleChangeInfo') }} <small>{{ self.Context.GetDefinition('Required') }}</small></label>
					<input type="text" name="Notes" id="txtNotes" value="" class="PanelInput" />
					<p class="Description">{{ self.Context.GetDefinition('RoleChangeInfoNotes') }}</p>
				</li>
			</ul>
			<div class="Submit">
				<input type="submit" name="btnSave" value="{{ self.Context.GetDefinition('ChangeRole') }}" class="Button SubmitButton ChangeRoleButton" />
				<a href="{{ GetUrl(self.Context.Configuration, 'account.php', '', 'u', self.User.UserID) }}" class="CancelButton">{{ self.Context.GetDefinition('Cancel') }}</a>
			</div>
			</form>
		</fieldset>
	</div>
{% endif %}