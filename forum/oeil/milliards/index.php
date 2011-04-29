<?php
$part1 = null;
$part2 = null;
$part3 = null;
if (isset($_GET['part1'])) {
	$part1 = filter_var($_GET['part1']);
}
if (isset($_GET['part2'])) {
	$part2 = filter_var($_GET['part2']);
}
if (isset($_GET['part3'])) {
	$part3 = filter_var($_GET['part3']);
}

$imagesTop = array();
foreach (glob(sprintf('%s/images/parts/1/*.png', dirname(__FILE__))) as $path) {
	$filename = basename($path);
	if (isset($_GET['part1']) && $filename == filter_var($_GET['part1'])) {
		$first = $filename;
		continue;
	}
	$imagesTop[] = $filename;
}
if (isset($first)) {
	array_unshift($imagesTop, $first);
	unset($first);
}

$imagesMiddle = array();
foreach (glob(sprintf('%s/images/parts/2/*.png', dirname(__FILE__))) as $path) {
	$filename = basename($path);
	if (isset($_GET['part2']) && $filename == filter_var($_GET['part2'])) {
		$first = $filename;
		continue;
	}
	$imagesMiddle[] = $filename;
}
if (isset($first)) {
	array_unshift($imagesMiddle, $first);
	unset($first);
}

$imagesBottom = array();
foreach (glob(sprintf('%s/images/parts/3/*.png', dirname(__FILE__))) as $path) {
	$filename = basename($path);
	if (isset($_GET['part3']) && $filename == filter_var($_GET['part3'])) {
		$first = $filename;
		continue;
	}
	$imagesBottom[] = $filename;
}
if (isset($first)) {
	array_unshift($imagesBottom, $first);
	unset($first);
}
?>
<!DOCTYPE html>
<html xmlns:og="http://ogp.me/ns#">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Mille Milliards de Hasard - Musiques Incongrues</title>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.js"></script>
		<script src="js/jquery.tools.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/scrollable-horizontal.css" />
		<link rel="shortcut icon" type="image/png" href="http://www.musiques-incongrues.net/forum/themes/vanilla/styles/scene/favicon.png" />
		<link href='http://fonts.googleapis.com/css?family=Expletus+Sans' rel='stylesheet' type='text/css'> 
	
		<!-- Opengraph -->
<?php if (isset($_GET['part1']) && isset($_GET['part2']) && isset($_GET['part3'])): ?>
		<meta property="og:image" content="<?php echo sprintf('http://www.musiques-incongrues.net/forum/oeil/milliards/download.php?part1=%s&part2=%s&part3=%s', filter_var($_GET['part1']), filter_var($_GET['part2']), filter_var($_GET['part3'])) ?>" />
<?php endif;  ?>
		
		<script type="text/javascript">
		$(document).ready(function() {
			var generate = function() {
				$('a#permalink').hide();
				$('#permalinkUrl').hide();
				$("#top").data("scrollable").seekTo(Math.floor(Math.random() * <?php echo count($imagesTop) ?>));
				$("#top").data("scrollable").onSeek(function(event, index) {
					var img = $($("#top").data("scrollable").getItems()[index]).find('img').attr('src').replace(/\\/g,'/').replace( /.*\//, '' );
					$('#part1').val(img);
				});
				
				$("#middle").data("scrollable").seekTo(Math.floor(Math.random() * <?php echo count($imagesMiddle) ?>));
				$("#middle").data("scrollable").onSeek(function(event, index) {
					var img = $($("#middle").data("scrollable").getItems()[index]).find('img').attr('src').replace(/\\/g,'/').replace( /.*\//, '' );
					$('#part2').val(img);
				});
				
				$("#bottom").data("scrollable").seekTo(Math.floor(Math.random() * <?php echo count($imagesBottom) ?>));
				$("#bottom").data("scrollable").onSeek(function(event, index) {
					var img = $($("#bottom").data("scrollable").getItems()[index]).find('img').attr('src').replace(/\\/g,'/').replace( /.*\//, '' );
					$('#part3').val(img);
				});
				$('a#permalink').show();
			};
			$('.scrollable').scrollable({touch: false, keyboard: false, mousewheel: false});
			$('a#random').click(function(event) {
				event.preventDefault();
				generate();
			});
			$('a#permalink').hover(function(event) {
				$(this).attr('href', '?part1='+$('#part1').val()+'&part2='+$('#part2').val()+'&part3='+$('#part3').val());
			});
			$('a#permalink').click(function(event) {
				event.preventDefault();
				$('#permalinkUrl').val('http://www.musiques-incongrues.net/forum/oeil/milliards/' + $(this).attr('href'));
				$('#permalinkUrl').show();
			});

			$('a#download').hover(function(event) {
				$(this).attr('href', 'download.php?part1='+$('#part1').val()+'&part2='+$('#part2').val()+'&part3='+$('#part3').val());
			});

			setTimeout("$('#info, #bubble').fadeOut('slow')", 10000);
			$('#content').hover(function() {
				$('#info, #bubble').fadeIn();
			});

			$('#info').mouseleave(function() {
				$('#info, #bubble').fadeOut();
			});
			
<?php if (isset($_GET['refresh']) && filter_var($_GET['refresh'], FILTER_VALIDATE_INT)): ?>
			setInterval('$("a#random").click()', <?php echo $_GET['refresh'] ?>);
<?php endif; ?>
		});
		</script>
		
	</head>

	<body>

	<div id="info">
		<div  id="info-about">
			<p>
				Mille Milliards De Hasard est un générateur d'identités incongrues.
			</p>
			<p>
				Un projet inspiré par Raymond Queneau, les livres pour enfants, et l'émerveillement que procure la magie aléatoire de l'Internet.
			</p>

			<p class="button">
				<a href="" id="random" title="Générer une nouvelle identité">mixer</a>
				&bull; <a href="" id="permalink" title="Accéder à l'URL vers l'identité courante">partager</a>
				&bull; <a href="contribute.php" title="Soumettre de nouvelles identités">contribuer</a>
				&bull; <a href="" title="Télécharger l'image" id="download">télécharger</a>
				<br /><input id="permalinkUrl" type="text" style="display:none" size="50" /> 
			</p>
		</div>

		<div id="bubble">
			<img src="images/bubble.gif" alt="bubble" />
		</div>
	</div>

	<div id="content">
		<div>
		<div class="scrollable" id="top">
			<div class="items">
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
	</div>
		<form id="state">
			<input type="hidden" id="part1" value="<?php echo $part1 ?>" />
			<input type="hidden" id="part2" value="<?php echo $part2 ?>" />
			<input type="hidden" id="part3" value="<?php echo $part3 ?>" />
		</form>

		<script type="text/javascript" src="http://www.google-analytics.com/urchin.js"></script>
		<script type="text/javascript"> 
            // <![CDATA[
            _uacct="UA-673133-2";
            urchinTracker();
            // ]]>
		</script>


	</body>

</html>