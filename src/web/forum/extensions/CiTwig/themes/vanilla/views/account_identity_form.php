{% if self.Context.Session.UserID != self.User.UserID and not self.Context.Session.User.Permission.PERMISSION_EDIT_USERS %}
<div id="Form" class="Account Identity">
	{{ self.Context.WarningCollector.Add(self.Context.GetDefinition('PermissionError')) }}
	{{ self.Get_Warnings() }}
</div>
{% else %}
	{{ self.PostBackParams.Set('PostBackAction', 'ProcessIdentity') }}
	{{ self.PostBackParams.Set('u', self.User.UserID) }}
	{{ self.PostBackParams.Set('LabelValuePairCount', self.User.Attributes|length, 1, 'LabelValuePairCount') }}

	<div id="Form" class="Account Identity">
		<fieldset>
			<legend>{{ self.Context.GetDefinition('ChangePersonalInfo') }}</legend>

			{# Form warnings #}
			{{ self.CallDelegate('PreWarningsRender') }}
			{{ self.Get_Warnings()|raw }}
			{{ self.Get_PostBackForm('frmAccountPersonal')|raw }}

			{{ self.CallDelegate('PreInputsRender') }}

			<h2>{{ self.Context.GetDefinition('DefineYourAccountProfile') }}</h2>
			<ul>
	{% if self.Context.Configuration.ALLOW_NAME_CHANGE %}
				<li>
					<label for="txtUsername">{{ self.Context.GetDefinition('YourUsername') }} <small>{{ self.Context.GetDefinition('Required') }}</small></label>
					<input type="text" name="Name" value="{{ self.User.Name }}" maxlength="20" class="SmallInput" id="txtUsername" />
					<p class="Description">{{ self.Context.GetDefinition('YourUsernameNotes') }}</p>
				</li>
	{% endif %}

	{% if self.Context.Configuration.USE_REAL_NAMES == "1" %}
				<li>
					<label for="txtFirstName">{{ self.Context.GetDefinition('YourFirstName') }}</label>
					<input type="text" name="FirstName" value="{{ self.User.FirstName }}" maxlength="50" class="SmallInput" id="txtFirstName" />
					<p class="Description">{{ self.Context.GetDefinition('YourFirstNameNotes') }}</p>
				</li>
				<li>
					<label for="txtLastName">{{ self.Context.GetDefinition('YourLastName') }}</label>
					<input type="text" name="LastName" value="{{ self.User.LastName }}" maxlength="50" class="SmallInput" id="txtLastName" />
					<p class="Description">
						{{ self.Context.GetDefinition('YourLastNameNotes') }}
						<span>{{ GetDynamicCheckBox('ShowName', 1, self.User.ShowName, '', self.Context.GetDefinition('MakeRealNameVisible'))|raw }}</span>
					</p>
				</li>
	{% endif %}

	{% if self.Context.Configuration.ALLOW_EMAIL_CHANGE == '1' %}
				<li>
					<label for="txtEmail">{{ self.Context.GetDefinition('YourEmailAddress') }} <small>{{ self.Context.GetDefinition('Required') }}</small></label>
					<input type="text" name="Email" value="{{ self.User.Email }}" maxlength="200" class="SmallInput" id="txtEmail" />
					<p class="Description">
						{{ self.Context.GetDefinition('YourEmailAddressNotes') }}
						<span>{{ GetDynamicCheckBox('UtilizeEmail', 1, self.User.UtilizeEmail, '', self.Context.GetDefinition('CheckForVisibleEmail'))|raw }}</span>
					</p>
				</li>
	{% else %}
				<li>
					<p class="Description">
						<span>{{ GetDynamicCheckBox('UtilizeEmail', 1, self.User.UtilizeEmail, '', self.Context.GetDefinition('CheckForVisibleEmail'))|raw }}</span>
					</p>
				</li>
	{% endif %}
				<li>
					<label for="txtPicture">{{ self.Context.GetDefinition('AccountPicture') }}</label>
					<input type="text" name="Picture" value="{{ self.User.Picture }}" maxlength="255" class="SmallInput" id="txtPicture" />
					<p class="Description">
						{{ self.Context.GetDefinition('AccountPictureNotes') }}
					</p>
				</li>
				<li>
					<label for="txtIcon">{{ self.Context.GetDefinition('Icon') }}</label>
					<input type="text" name="Icon" value="{{ self.User.Icon }}" maxlength="255" class="SmallInput" id="txtIcon" />
					<p class="Description">
						{{ self.Context.GetDefinition('IconNotes') }}
					</p>
				</li>
			</ul>

	{# Extensions customizations #}
			<h2>{{ self.Context.GetDefinition('OtherSettings') }}</h2>
			<ul>
	{% for key in self.Context.Configuration|keys if 'CUSTOMIZATION_' in key %}
				<li>
					<label for="{{ key }}">{{ self.Context.GetDefinition(key) }}</label>
					<input id="{{ key }}" type="text" name="{{ key }}" value="{{ ForceIncomingString(key, self.User.Customization(key)) }}" maxlength="255" class="SmallInput" />
					<p class="Description">
		{% if self.Context.GetDefinition(key ~ '_DESCRIPTION') != key ~ '_DESCRIPTION' %}
						{{ self.Context.GetDefinition(key ~ '_DESCRIPTION') }}
		{% endif %}
					</p>
				</li>
	{% endfor %}
			</ul>

			{{ self.CallDelegate('PreCustomInputsRender') }}

			<h2>{{ self.Context.GetDefinition('AddCustomInformation') }}</h2>
			<p class="Description">{{ self.Context.GetDefinition('AddCustomInformationNotes')|raw }}</p>
			<ul id="CustomInfo" class="clearfix">
				<li>{{ self.Context.GetDefinition('Label') }}</li>
				<li>{{ self.Context.GetDefinition('Value') }}</li>
		{% for attribute in self.User.Attributes %}
				<li><input type="text" name="Label{{ loop.index }}" value="{{ attribute.Label }}" maxlength="20" class="LVLabelInput" /></li>
				<li><input type="text" name="Value{{ loop.index }}" value="{{ attribute.Value }}" maxlength="200" class="LVValueInput" /></li>
		{% endfor %}
			</ul><!-- #CustomInfo -->

			{{ self.CallDelegate('PreButtonsRender') }}

			<p><a href="javascript:AddLabelValuePair();">{{ self.Context.GetDefinition('AddLabelValuePair') }}</a></p>
			<div class="Submit">
				<input type="submit" name="btnSave" value="{{ self.Context.GetDefinition('Save') }}" class="Button SubmitButton" />
				<a href="{{ GetUrl(self.Context.Configuration, 'account.php', '', 'u', self.User.UserID) }}" class="CancelButton">{{ self.Context.GetDefinition('Cancel') }}</a>
			</div>
			</form>
		</fieldset>
	</div><!-- #Form -->
{% endif %}
