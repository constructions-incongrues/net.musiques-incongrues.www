{% set FirstRow = 1 %}
{% set Alternate = 0 %}

<div class="ContentInfo Top">
	<h1>{{ self.Context.PageTitle }}</h1>
</div>

<div id="ContentBody">
	<ol id="Categories">
{% for category in self.Data %}
		<li id="Category_{{ category.CategoryID }}" class="Category {{ category.Blocked ? BlockedCategory : 'UnblockedCategory' }} {{ FirstRow ? 'FirstCategory' }} Category_{{ category.CategoryID }} {{ Alternate ? 'Alternate' }}">
			<ul>
				<li class="CategoryName">
					<span>{{ self.Context.GetDefinition('Category') }}</span> <a href="{{ GetUrl(self.Context.Configuration, 'index.php', '', 'CategoryID', category.CategoryID) }}">{{ category.Name }}</a>
				</li>
				<li class="CategoryDescription">
					<span>{{ self.Context.GetDefinition('CategoryDescription') }}</span> {{ category.Description }}
				</li>
				<li class="CategoryDiscussionCount">
					<span>{{ self.Context.GetDefinition('Discussions') }}</span> {{ category.DiscussionCount }}
				</li>
	{% if self.Context.Session.UserID > 0 %}
				<li class="CategoryOptions">
					<span>{{ self.Context.GetDefinition('Options') }}</span> 
		{% if category.Blocked %}
					<a id="BlockCategory{{ category.CategoryID }}" onclick="ToggleCategoryBlock('{{ self.Context.Configuration.WEB_ROOT }}ajax/blockcategory.php', {{ category.CategoryID }}, 0, 'BlockCategory{{ category.CategoryID }}', '{{ self.Context.Session.GetCsrfValidationKey() }}');">{{ self.Context.GetDefinition('UnblockCategory') }}</a>
		{% else %}
					<a id="BlockCategory{{ category.CategoryID }}" onclick="ToggleCategoryBlock('{{ self.Context.Configuration.WEB_ROOT }}ajax/blockcategory.php', {{ category.CategoryID }}, 1, 'BlockCategory{{ category.CategoryID }}', '{{ self.Context.Session.GetCsrfValidationKey() }}');">{{ self.Context.GetDefinition('BlockCategory') }}</a>
		{% endif %}
				</li>
	{% endif %}
			</ul>
		</li>
{% endfor %}
	</ol>
</div>
{% set FirstRow = 0 %}
{% set Alternate = FlipBool(Alternate) %}