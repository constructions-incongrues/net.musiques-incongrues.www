<li id="button-events">
  <label for="CiDiscussionMetadata_Event_isevent" style="display:inline;">C'est un évènement ?</label>
  <input
    type="checkbox"
    class="check_event"
    name="CiDiscussionMetadata_Type[]"
    value="event"
    onclick="jQuery('#CiDiscussionMetadata_Event_fieldset').toggle();"
{% if self.Discussion.Metadata.event is defined %}
    checked
    disabled
{% endif %}
    />
</li>

<fieldset id="CiDiscussionMetadata_Event_fieldset" {% if self.Discussion.Metadata.event is not defined %}style="display:none;"{% endif %}>
  <li>
    <label for="CiDiscussionMetadata_Event_StartsOn">Date de début (DD/MM/YYYY) !</label>
    <input name="CiDiscussionMetadata_Event_StartsOn" class="datepicker event-input-date" value="{{ self.Discussion.Metadata.event.StartsOn }}" />
  </li>
  <li>
    <label for="CiDiscussionMetadata_Event_EndsOn">Date de fin (DD/MM/YYYY) !</label>
    <input name="CiDiscussionMetadata_Event_EndsOn" class="datepicker event-input-date" value="{{ self.Discussion.Metadata.event.EndsOn }}" />
  </li>

  <li>
    <label for="CiDiscussionMetadata_Event_City">Ville</label>
    <input name="CiDiscussionMetadata_Event_City" value="{{ self.Discussion.Metadata.event.City }}" class="event-input-city" />
  </li>

  <li>
    <label for="CiDiscussionMetadata_Event_Country">Pays</label>
    <input name="CiDiscussionMetadata_Event_Country" value="{{ self.Discussion.Metadata.event.Country }}" class="event-input-country"/>
  </li>
</fieldset>
