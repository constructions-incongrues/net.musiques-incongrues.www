<!-- Discussion types additional properties -->
<fieldset>
	<legend>
		<input
                type="checkbox"
                name="DiscussionType_Event_IsEvent"
                onclick="$('#DiscussionType_Event_Options').toggle();"
			<?php echo $this->Data['DiscussionTypes']['Event']['EventID'] ? 'checked' : '' ?>
			<?php echo $this->Data['DiscussionTypes']['Event']['EventID'] ? 'disabled' : '' ?>
        />
		<?php echo t('Discussion is related to an event') ?>
	</legend>
    <div style="display: <?php echo $this->Data['DiscussionTypes']['Event']['EventID'] ? 'block' : 'none' ?>;" id="DiscussionType_Event_Options">
        <label for="DiscussionType_Event_StartDate"><?php echo t('Start date') ?></label>
        <input
                type="date"
                name="DiscussionType_Event_StartDate"
                value="<?php echo $this->Data['DiscussionTypes']['Event']['StartDate'] ?>"
                class="required" />
        <label for="DiscussionType_Event_EndDate"><?php echo t('End date') ?></label>
        <input
                type="date"
                name="DiscussionType_Event_EndDate"
                value="<?php echo $this->Data['DiscussionTypes']['Event']['EndDate'] ?>" />
        <label for="DiscussionType_Event_Country"><?php echo t('Country') ?></label>
        <input
                type="text"
                name="DiscussionType_Event_Country"
                value="<?php echo $this->Data['DiscussionTypes']['Event']['Country'] ?>"
                class="required" />
        <label for="DiscussionType_Event_City"><?php echo t('City') ?></label>
        <input
                type="text"
                name="DiscussionType_Event_City"
                value="<?php echo $this->Data['DiscussionTypes']['Event']['City'] ?>"
                class="required"
        />
        <label for="DiscussionType_Event_Venue"><?php echo t('Venue') ?></label>
        <input
                type="text"
                name="DiscussionType_Event_Venue"
                value="<?php echo $this->Data['DiscussionTypes']['Event']['Venue'] ?>" />
        <label for="DiscussionType_Event_Url"><?php echo t('Url') ?></label>
        <input
                type="url"
                name="DiscussionType_Event_Url"
                value="<?php echo $this->Data['DiscussionTypes']['Event']['Url'] ?>" />
    </div>
</fieldset>