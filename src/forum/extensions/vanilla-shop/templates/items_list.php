<div id="ContentBody" class="shop">
	<ol id="Discussions">
		<?php if (count($items)): ?>
			<?php foreach ($items as $item): ?>
				<li>
					<a href="<?php echo $item['url'] ?>" title="Discuter de <?php echo $item['Name'] ?>"><?php echo $item['Name']?></a>
				</li>
			<?php endforeach; ?>
		<?php else: ?>
			<p>Rien à vendre. C'est Moscou !</p>
		<?php endif; ?>
	</ol>
</div>
<!--
$href = GetUrl($this->Context->Configuration, 'comments.php', '', 'DiscussionID', $item['DiscussionID'], '', '#Item_1', CleanupString($item['Name']).'/');
$link = sprintf('<a href="%s" title="Discuter de %s">%s</a>', $href, $item['Name'], $item['Name']);
$alternate = $i % 2 == 0 ? '' : 'modulo';
$discussions .= sprintf('<li class="Discussion CatalogItem %s"><ul><li class="DiscussionTopic">%s</li></ul></li>', $alternate, $link);
-->
