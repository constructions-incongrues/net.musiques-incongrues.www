<?php
$imagesTop = array();
foreach (glob(sprintf('%s/images/parts/1/*.png', dirname(__FILE__))) as $path) {
	$imagesTop[] = basename($path);
}
$imagesMiddle = array();
foreach (glob(sprintf('%s/images/parts/2/*.png', dirname(__FILE__))) as $path) {
	$imagesMiddle[] = basename($path);
}
$imagesBottom = array();
foreach (glob(sprintf('%s/images/parts/3/*.png', dirname(__FILE__))) as $path) {
	$imagesBottom[] = basename($path);
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Mille Milliard de Hasard - Musiques Incongrues</title>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.js"></script>
		<script src="js/jquery.tools.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/scrollable-horizontal.css" />
		<link rel="shortcut icon" type="image/png" href="http://www.musiques-incongrues.net/forum/themes/vanilla/styles/scene/favicon.png" /> 
		
		<script type="text/javascript">
		$(document).ready(function() {
			var generate = function() {
				// get access to the API
				$("#top").data("scrollable").seekTo(Math.floor(Math.random() * <?php echo count($imagesTop) ?>));
				$("#middle").data("scrollable").seekTo(Math.floor(Math.random() * <?php echo count($imagesMiddle) ?>));
				$("#bottom").data("scrollable").seekTo(Math.floor(Math.random() * <?php echo count($imagesBottom) ?>));
			};
			$('.scrollable').scrollable({touch: false, keyboard: false, mousewheel: false});
			$('a#random').click(generate);
		});
		</script>
		
	</head>

	<body>

		<a id="random" href="#">RAND</a>

		<div>
		<div class="scrollable" id="top">
			<div class=items>
<?php foreach ($imagesTop as $image): ?>	
				<div>
					<img src="<?php echo sprintf('images/parts/1/%s', $image) ?>" />
				</div>
<?php endforeach; ?>
			</div>
		</div>
		</div>
		
		<div>
		<div class="scrollable" id="middle">
			<div class=items>
<?php foreach ($imagesMiddle as $image): ?>	
				<div>
					<img src="<?php echo sprintf('images/parts/2/%s', $image) ?>" />
				</div>
<?php endforeach; ?>
			</div>
		</div>
		</div>
			
		<div>
		<div class="scrollable" id="bottom">
			<div class=items>
<?php foreach ($imagesBottom as $image): ?>	
				<div>
					<img src="<?php echo sprintf('images/parts/3/%s', $image) ?>" />
				</div>
<?php endforeach; ?>
			</div>
		</div>
		</div>
	</body>

</html>