<?php $i = 0; ?>


<div id="page-ego">

		<?php foreach ($people as $person): ?>
			<?php $alt = $i % 2 == 0 ? 1 : 2; ?>
			<?php
			if (!$person['picture'])
			{
			    $picture = sprintf('http://www.gravatar.com/avatar/%s?default=wavatar', md5(strtolower(trim($person['name']))));
			}
			else
			{
			    $picture = $person['picture'];
			}

			?>
    		<div class="member-item<?php echo $alt ?>">
        		<div class="member-avatar"><img src="<?php echo $picture ?>" width="30px" /></div>
        		<div class="member-name"><a href="<?php echo $sf_request->getRelativeUrlRoot() ?>/../account/<?php echo $person['userid'] ?>"><?php echo $person['name']?></a><br /> <?php echo $person['countcomments'] ?> posts</div>
    		</div>

			<?php $i++ ?>
		<?php endforeach; ?>

</div> <!-- #page-ego -->