<?php 
$images = array(
	'top'    => filter_input(INPUT_GET, 'part1'),
	'middle' => filter_input(INPUT_GET, 'part2'),
	'bottom' => filter_input(INPUT_GET, 'part3')
);
$urlRoot = sprintf('http://%s%s', $_SERVER['SERVER_NAME'], dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html xmlns:og="http://ogp.me/ns#">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<title>Mille Milliards de Hasard</title>
		
		<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.12.custom.min.js"></script>
		<script src="js/behaviors.js"></script>
		
		<link rel="stylesheet" type="text/css" href="css/main.css" />
		<link href='http://fonts.googleapis.com/css?family=Expletus+Sans' rel='stylesheet' type='text/css'>
		<link rel="shortcut icon" type="image/png" href="images/static/favicon.png" /> 
	
		<!-- Opengraph -->
<?php if ($images['top'] && $images['middle'] && $images['bottom']): ?>
		<meta property="og:image" content="<?php echo sprintf('%s/download.php?part1=%s&part2=%s&part3=%s', $urlRoot, $images['top'], $images['middle'], $images['bottom']) ?>" />
<?php endif;  ?>
		
		<script type="text/javascript">
			var urlRoot = '<?php echo $urlRoot ?>';
<?php if (isset($_GET['refresh']) && filter_var($_GET['refresh'], FILTER_VALIDATE_INT)): ?>
			setInterval('$("a#random").click()', <?php echo $_GET['refresh'] ?>);
<?php endif; ?>
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
			<p>
				Le projet recense TODO identités à ce jour.
			</p>

			<p class="button">
				<a href="api.php" id="random" title="Générer une nouvelle identité">mixer</a>
				&bull; <a href="<?php echo sprintf('%s/?part1=%s&part2=%s&part3=%s', $urlRoot, $images['top'], $images['middle'], $images['bottom']) ?>" id="permalink" title="Accéder à l'URL vers l'identité courante">partager</a>
				&bull; <a href="contribute.php" title="Soumettre de nouvelles identités">contribuer</a>
				&bull; <a href="<?php echo sprintf('%s/download.php?part1=%s&part2=%s&part3=%s', $urlRoot, $images['top'], $images['middle'], $images['bottom']) ?>" title="Télécharger l'image" id="download">télécharger</a>
				<br /><input id="permalinkUrl" type="text" style="display:none" size="50" value="<?php echo sprintf('%s/?part1=%s&part2=%s&part3=%s', $urlRoot, $images['top'], $images['middle'], $images['bottom']) ?>" /> 
			</p>
		</div>

		<div id="bubble">
			<img src="images/static/bubble.gif" />
		</div>
	</div>

	<div id="content">
		<div>
			<div class="scrollable" id="top">
				<div class="items">
					<div>
<?php if ($images['top']): ?>
						<img src="<?php echo sprintf('images/parts/1/%s', $images['top']) ?>" />
<?php else: ?>
						<img src="images/static/cidrolin_top.png" />
<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		
		<div>
			<div class="scrollable" id="middle">
				<div class=items>
					<div>
<?php if ($images['middle']): ?>
						<img src="<?php echo sprintf('images/parts/2/%s', $images['middle']) ?>" />
<?php else: ?>
						<img src="images/static/cidrolin_middle.png" />
<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
			
		<div>
			<div class="scrollable" id="bottom">
				<div class=items>
					<div>
<?php if ($images['bottom']): ?>
						<img src="<?php echo sprintf('images/parts/3/%s', $images['bottom']) ?>" />
<?php else: ?>
						<img src="images/static/cidrolin_bottom.png" />
<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<p id="footer">
			Mille Milliards de Hasard est développé conjointement par <a href="http://cobrafoutre.tumblr.com" title="Meet the Cobra">Cobra Foutre</a> et <a href="http://www.constructions-incongrues.net" title="Les Constructions Incongrues">Constructions Incongrues</a>.
			Le code source est <a href="https://github.com/contructions-incongrues/musiques-incongrues.net/tree/master/forum/oeil/milliards/">diffusé</a> sous license <a href="" title="">AGPL3</a>. Le projet est hébergé par <a href="http://www.pastis-hosting.net" title="L'hébergeur dopé au Pastis">Pastis Hosting</a>.
		</p>
	</div>
		<script type="text/javascript" src="http://www.google-analytics.com/urchin.js"></script>
		<script type="text/javascript"> 
            // <![CDATA[
            _uacct="UA-673133-2";
            urchinTracker();
            // ]]>
		</script>


	</body>

</html>