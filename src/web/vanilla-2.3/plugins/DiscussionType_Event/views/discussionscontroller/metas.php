<!-- Displays discussion's event metadata -->
<?php if (isset($this->Data['DiscussionTypes']['Event'])): ?>
    <?php $event = $this->Data['DiscussionTypes']['Event']; ?>
<div class="DiscussionType_Event">
    <span class="MItem">

    <?php if ($event['Url']): ?>
        <a href="<?php echo $event['Url'] ?>" target="_blank">
    <?php endif; ?>

    <?php echo sprintf('%s %s', c('Starts on'), $event['StartDate']) ?>
    <?php if ($event['EndDate']): ?>
        <?php echo sprintf('%s %s', c(', ends on'), $event['EndDate']) ?>
    <?php endif; ?>

    <?php if ($event['Url']): ?>
        </a>
    <?php endif; ?>

        @
    <?php if ($event['Venue']): ?>
        <?php echo $event['Venue'] ?>,
    <?php endif; ?>
    <?php echo $event['City'] ?>, <?php echo $event['Country'] ?>
    </span>
</div>
<?php endif; ?>