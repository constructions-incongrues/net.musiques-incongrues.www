<?php if (count($discussions)): ?>
    <ul id="discussions">
        <?php foreach ($discussions as $discussion): ?>

    		<li><?php echo $discussion->name ?></li>

    	<?php endforeach; ?>
    </ul>
<?php else: ?>

	<p>Aucune discussion à afficher. Je peine à le croire !</p>

<?php endif; ?>