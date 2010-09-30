<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo htmlspecialchars( Asaph_Config::$title ); ?> - Musiques Incongrues</title>
	<link rel="stylesheet" type="text/css" href="<?php echo Asaph_Config::$absolutePath; ?>templates/incongrues/incongrues.css" />
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php echo ASAPH_LINK_PREFIX; ?>feed" />
	<link rel="Shortcut Icon" href="<?php echo Asaph_Config::$absolutePath; ?>templates/incongrues/asaph.ico" />
	<script type="text/javascript" src="<?php echo Asaph_Config::$absolutePath; ?>templates/incongrues/incongrues.js"></script>
	<meta name="description" content="La pinacothèque du forum des Musiques Incongrues." />
</head>
<body>


<div id="Header">

<h1><a href="/forum/oeil/"><?php echo htmlspecialchars( Asaph_Config::$title ); ?></a></h1>

<ul>
<li class="Eyes"><a href="<?php echo Asaph_Config::$vanillaPath; ?>events/" class="Eyes">Events</a></li>
<li class="Eyes"><a href="<?php echo Asaph_Config::$vanillaPath; ?>oeil/" class="Eyes">Œil</a></li>
<li class="Eyes"><a href="<?php echo Asaph_Config::$vanillaPath; ?>releases/" class="Eyes">Releases</a></li>
<li><a href="<?php echo Asaph_Config::$vanillaPath; ?>discussions/" >Discussions</a></li>
<li class="Pink"><a href="<?php echo Asaph_Config::$vanillaPath; ?>categories/" class="Pink">Categories</a></li>
<li class="Pink"><a href="<?php echo Asaph_Config::$vanillaPath; ?>search/" class="Pink">Search</a></li>
<li class="dons"><a href="<?php echo Asaph_Config::$vanillaPath; ?>page/dons" class="dons">Donate</a></li>
</ul>

</div>

<!--
<div id="title">
	<h1><a href="http://www.musiques-incongrues.net/forum/asaph/"><?php echo htmlspecialchars( Asaph_Config::$title ); ?></a> <span class="forum"><a href="http://www.musiques-incongrues.net/forum">FORUM</a></span></h1>

</div>
-->
<?php $classes = array('', 'modulo', 'modulo_bis'); ?>
<?php foreach( $posts as $p ) { ?>
<?php $modulo_class = $classes[rand(0, count($classes))] ?>
	<div class="post <?php echo $modulo_class ?>">
		<?php if( $p['image'] ) { ?>
			<a href="<?php echo $p['image']; ?>" rel="whitebox" title="<?php echo $p['title']; ?>">
				<img src="<?php echo $p['thumb']; ?>" alt="<?php echo $p['title']; ?>"/>
			</a>
		<?php } else { ?>
			<p>
				<a href="<?php echo $p['source']; ?>"><?php echo nl2br($p['title']); ?></a>
			</p>
		<?php } ?>


		<div class="postInfo">
		<a href="<?php echo $p['source']; ?>" title="<?php echo $p['title'] ?>"><?php echo substr($p['title'], 0, 24); ?> ...</a>
		</div>

	</div>
<?php } ?>
<div class="clear"></div>

<div id="pages">

	<div class="pageInfo">
	<a href="http://www.musiques-incongrues.net/forum/discussion/1253/une-poussiere-dans-loeil">À propos</a>
	</div>


	<div class="pageLinks">
		<?php echo 9 * $pages['total'] ?> pics &bull;
		<a href="<?php echo ASAPH_LINK_PREFIX.'page/' ?>">En voir d'autres !</a>
	</div>
	<div class="clear"></div>
</div>
<!--
<div class="author">
<p>Remerciement à <a href="http://www.phoboslab.org/projects/asaph">Asaph</a> pour le script</p></div>
-->
<script type="text/javascript" src="http://www.google-analytics.com/urchin.js"></script>
<script type="text/javascript">

// <![CDATA[
_uacct="UA-673133-2";
urchinTracker();
// ]]>
</script>
</body>
</html>
