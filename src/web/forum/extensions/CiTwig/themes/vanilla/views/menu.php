{{ self.CallDelegate('PreHeadRender') }}

<div id="Header">
	<div id="Session">
{# User is authenticated #}
{% if self.Context.Session.UserID > 0 %}
		{{ self.Context.GetDefinition('SignedInAsX')|replace({ '//1': self.Context.Session.User.Name }) }}
		(<a href="{{ AppendUrlParameters(self.Context.Configuration.SIGNOUT_URL, 'FormPostBackKey=' ~ self.Context.Session.GetCsrfValidationKey()) }}">
			{{ self.Context.GetDefinition('SignOut') }}
		</a>)
{# User is not authenticated #}
{% else %}
		{{ self.Context.GetDefinition('NotSignedIn') }}
		(<a href="{{ AppendUrlParameters(self.Context.Configuration.SIGNIN_URL, 'ReturnUrl=' ~ GetRequestUri(0)|url_encode) }}">{{ self.Context.GetDefinition('SignIn') }}</a>)
		or <a href="{{ AppendUrlParameters(self.Context.Configuration.REGISTRATION_URL, 'ReturnUrl=' ~ GetRequestUri(0)|url_encode) }}">{{ self.Context.GetDefinition('Register') }}</a>
{% endif %}
	</div><!-- #Session -->

	<a name="pgtop"></a>

	<h1>{{ self.Context.Configuration.BANNER_TITLE }} </h1>

	<ul>
{# Navigation tabs #}
{% for tab in self.Tabs %}
		<li {{ self.TabClass(self.CurrentTab, tab.Value)|raw }}>
			<a href="{{ tab.Url }}" {{ tab.Attributes }}>{{ tab.Text }}</a>
		</li>
{% endfor %}
	</ul>
</div><!-- #Header -->

{{ self.CallDelegate('PreBodyRender') }}

<div id="body">
