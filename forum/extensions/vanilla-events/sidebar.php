<?php 
$event = EventsPeer::getEvent($Context, ForceIncomingInt('DiscussionID', null));
?>
<?php if ($event): ?>
<h2>Événement</h2>
<ul class="ailleurs-links">
	<li>Date : <a href="<?php echo GetUrl($Context->Configuration, 'extension.php', '/', '', '', '?PostBackAction=Events&start='.$event['Date'].'&end='.$event['Date']) ?>" title="Voir tous les événements se déroulant à cette date"><?php echo $event['Date'] ?></a></li>
	<li>Lieu : <a href="<?php echo GetUrl($Context->Configuration, 'extension.php', '/', '', '', '?PostBackAction=Events&city='.$event['City']) ?>" title="Voir tous les événements à venir à <?php echo $event['City'] ?>"><?php echo $event['City'] ?> (<?php echo $event['Country'] ?>)</a></li>
</ul>
<p style="text-align:center;margin-top:1em;"><a href="<?php echo $Configuration['WEB_ROOT'] ?>events/" title="L'agenda du forum des Musiques Incongrues">Voir tous les événements à venir</a></p>
<?php endif; ?>