<li id="button-releases">
  <label for="CiDiscussionMetadata_Release_isrelease" style="display:inline;">C'est une sortie ?</label>
  <input
    type="checkbox"
    class="check_release"
    name="CiDiscussionMetadata_Type[]"
    value="release"
    onclick="jQuery('#CiDiscussionMetadata_Release_fieldset').toggle();"
{% if self.Discussion.Metadata.release is defined %}
    checked
    disabled
{% endif %}
    />
</li>

<fieldset id="CiDiscussionMetadata_Release_fieldset" {% if self.Discussion.Metadata.release is not defined %}style="display:none;"{% endif %}>
  <li>
    <label for="CiDiscussionMetadata_Release_Label">Label</label>
    <input name="CiDiscussionMetadata_Release_Label" class="release-input" value="{{ self.Discussion.Metadata.release.Label }}" />
  </li>
</fieldset>
