<div id="page-ego">
    <ol id="Discussions">
    	<li class="Discussion Release">

    	<dl class="Shop">
    	<?php foreach ($items as $item): ?>
    		<span class="shop-pictures"><img src="" width="75px" height="75px" /></span>
    		<dd class="shop-object"><a href="<?php echo $item->Discussion->getUri() ?>"><?php echo $item->Discussion->name ?></a></dd>
    		<span class="shop-price"><?php echo $item->price ?> €</span>
    		<span class="shop-buy"><a href="">Contacter le vendeur</a></span>
    		<span class="shop-buy-info"> <a	href="<?php echo $item->Discussion->getUri(sfConfig::get('app_paths_baseuri')) ?>">En
    		savoir plus</a></span>
    		<?php endforeach; ?>
    	</dl>
    	</li>
    </ol>
</div>
