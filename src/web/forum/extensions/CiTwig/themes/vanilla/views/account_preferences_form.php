{% if self.Context.Session.UserID != self.User.UserID and not self.Context.Session.User.Permission('PERMISSION_EDIT_USERS') %}
	{{ self.Context.WarningCollector.Add(self.Context.GetDefinition('PermissionError')) }}
<div id="Form" class="Account Preferences">
	{{ self.Get_Warnings()|raw }}
</div>

{% else %}
<div id="Form" class="Account Preferences">
	<fieldset>
		<legend>{{ self.Context.GetDefinition('ForumFunctionality') }}</legend>
		<form name="frmFunctionality" method="post" action="">
		<p class="Description">{{ self.Context.GetDefinition('ForumFunctionalityNotes') }}</p>
	{% for sectionLanguageCode, sectionPreferences in self.Preferences %}
		<h2>{{ self.Context.GetDefinition(sectionLanguageCode) }}</h2>
		<ul>
		{% for preference in sectionPreferences %}
			<li>
				<p>
			{% if preference.IsUserProperty %}
				{% set prefVal = self.Context.Session.User[preference.Name] %}
			{% else %}
				{% set prefVal = self.Context.Session.User.Preference(preference.Name) %}
			{% endif %}
					<span id="{{ preference.Name }}">
						{{ GetDynamicCheckBox(
							preference.Name, 
							ForceBool(self.Context.Configuration['PREFERENCE_' ~ preference.Name], 0), 
							prefVal,
							"SwitchPreference('" ~ self.Context.Configuration.WEB_ROOT ~ "ajax/switch.php', '" ~ preference.Name ~ "', " ~ ForceBool(preference.RefreshPageAfterSetting, 0) ~ ", '" ~ self.Context.Session.GetCsrfValidationKey() ~ "');",
							self.Context.GetDefinition(preference.LanguageCode))|raw
						 }}
					</span>
				</p>
			</li>
		{% endfor %}
		</ul>
	{% endfor %}
		</form>
	</fieldset>
</div><!-- #Form -->
{% endif %}