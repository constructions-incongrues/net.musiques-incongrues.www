<div id="AccountProfile">

{% if ForceIncomingBool('Success', 0) %} 
	<div id="Success">{{ self.Context.GetDefinition('ChangesSaved') }}</div>
{% endif %}

{{ self.Render_Warnings() }}

	<ul class="vcard">

{{ self.CallDelegate('PreUsernameRender') }}

{% if self.User.DisplayIcon != '' %}
		<li class="ProfileTitle WithIcon clearfix">
			<div class="ProfileIcon" style="background-image:url('{{ self.User.DisplayIcon }}')">&nbsp;</div>
		{% else %}
			<li class="ProfileTitle clearfix">
		{% endif %}
			<h2>{{ self.User.Name }}</h2>
			<p>{{ self.User.Role }}</p>
		</li>
	{% if self.User.RoleDescription != '' %}
		<li class="Tagline">{{ self.User.RoleDescription }}</li>
	{% endif %}

	{% if self.User.Picture != "" and self.User.Permission('PERMISSION_HTML_ALLOWED') %}
		<li class="Picture" style="background-image: url('{{ self.User.Picture }}')">&nbsp;</li>
	{% endif %}

	{{ self.CallDelegate('PostPictureRender') }}

	{% if self.Context.Configuration.USE_REAL_NAMES. and (self.User.ShowName or self.Context.Session.User.Permission('PERMISSION_EDIT_USERS')) %}
		<li>
			<h3>{{ self.Context.GetDefinition('RealName') }}</h3>
			<p class="fn">{{ ReturnNonEmpty(self.User.FullName) }}</p>
		</li>
	{% endif %}
	<li>
		<h3>{{ self.Context.GetDefinition('Email') }}</h3>
		<p class="email">
	{% if self.Context.Session.UserID > 0 and self.User.UtilizeEmail %}
			{{ GetEmail(self.User.Email) }}
	{% else %}
			{{ self.Context.GetDefinition('NA') }}
	{% endif %}
		</p>
	</li>
	<li>
		<h3>{{ self.Context.GetDefinition('AccountCreated') }}</h3>
		<p>{{ TimeDiff(self.Context, self.User.DateFirstVisit, 'now'|date('U')) }}</p>
	</li>
	<li>
		<h3>{{ self.Context.GetDefinition('LastActive') }}</h3>
		<p>{{ TimeDiff(self.Context, self.User.DateLastActive, 'now'|date('U')) }}</p>
	</li>
	<li>
		<h3>{{ self.Context.GetDefinition('VisitCount') }}</h3>
		<p>{{ self.User.CountVisit }}</p>
	</li>
	<li>
		<h3>{{ self.Context.GetDefinition('DiscussionsStarted') }}</h3>
		<p>{{ self.User.CountDiscussions }}</p>
	</li>
	<li>
		<h3>{{ self.Context.GetDefinition('CommentsAdded') }}</h3>
		<p>{{ self.User.CountComments }}</p>
	</li>

	{{ self.CallDelegate('PostBasicPropertiesRender') }}

	{% if self.Context.Session.User.Permission('PERMISSION_IP_ADDRESSES_VISIBLE') %}
	<li>
		<h3>{{ self.Context.GetDefinition('LastKnownIp') }}</h3>
		<p>{{ self.User.RemoteIp }}</p>
	</li>
	{% endif %}

	{% for attribute in self.User.Attributes %}
		{% set cssClass = '' %}
		{% if 'http://' in attribute.Value|lower|slice(0, 7) %}
			{% set cssClass = 'url' %}
		{% endif %}
		<li>
			<h3>{{ attribute.Label }}</h3>
			<p>{{ FormatHyperlink(attribute.Value, 1, '', cssClass)|raw }}</p>
		</li>
	{% endfor %}

	{{ self.CallDelegate('PostAttributesRender') }}

	</ul>
</div><!-- #AccountProfile -->

<div id="AccountHistory">

{{ self.CallDelegate('PostProfileRender') }}
