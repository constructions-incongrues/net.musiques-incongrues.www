<li id="button-Sellables">
  <label for="CiDiscussionMetadata_Sellable_isSellable" style="display:inline;">C'est à vendre ?</label>
  <input
    type="checkbox"
    class="check_Sellable"
    name="CiDiscussionMetadata_Type[]"
    value="sellable"
    onclick="jQuery('#CiDiscussionMetadata_Sellable_fieldset').toggle();"
{% if self.Discussion.Metadata.sellable is defined %}
    checked
{% endif %}
    />
</li>

<fieldset id="CiDiscussionMetadata_Sellable_fieldset" {% if self.Discussion.Metadata.sellable is not defined %}style="display:none;"{% endif %}>
  <li>
    <label for="CiDiscussionMetadata_Sellable_Price">Price</label>
    <input name="CiDiscussionMetadata_Sellable_Price" class="Sellable-input" value="{{ self.Discussion.Metadata.sellable.Price }}" />
  </li>
</fieldset>
