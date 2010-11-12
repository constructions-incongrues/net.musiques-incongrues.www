<div id="ContentBody">
    <h2 style="display:inline;" class="surtout">On écoute quoi aujourd'hui ?</h2>
    <div class="Notice">
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam tristique gravida sem, ut ultricies ligula venenatis sed. Morbi malesuada ante in augue luctus vitae elementum massa vestibulum. In mattis pulvinar metus, non blandit lorem cursus sit amet. Mauris porta urna et nunc pellentesque aliquam. Integer interdum diam hendrerit nunc volutpat posuere lobortis ac tortor. Morbi at porta mi. In risus orci, sagittis sit amet ultricies quis, tincidunt ac odio. In rutrum lectus vel massa adipiscing consequat. Integer mi risus, molestie feugiat aliquam et, pellentesque non odio. Proin vitae neque at enim suscipit luctus non vel nisl. Mauris a posuere purus. Cras metus orci, hendrerit a lacinia in, fermentum vel quam.
    </div>
    <div id="page-ego">

        <ol id="Discussions">
        	<li class="Discussion Release">

        	<?php foreach ($items as $item): ?>
        	<dl class="Shop">
        		<span class="shop-pictures"><img src="<?php echo $item->Discussion->getFirstImageUrl() ?>" width="75px" height="75px" /></span>
        		<dd class="shop-object"><a href="<?php echo $item->Discussion->getUri(sfConfig::get('app_paths_baseuri')) ?>"><?php echo $item->Discussion->name ?></a></dd>
        		<dt class="shop-legend">Depuis le <?php echo $item->Discussion->getCreationDate() ?> par <a href="<?php echo $item->Discussion->Author->getProfileUri(sfConfig::get('app_paths_baseuri')) ?>"><?php echo $item->Discussion->Author->name ?></a></dt>
        		<span class="shop-price"><?php echo $item->price ?> €</span>
        		<span class="shop-buy"><a href="">Contacter le vendeur</a></span>
        		<span class="shop-buy-info"> <a	href="<?php echo $item->Discussion->getUri(sfConfig::get('app_paths_baseuri')) ?>">En savoir plus</a></span>
           	</dl>
      		<?php endforeach; ?>

    		</li>
        </ol>
    </div>
</div>