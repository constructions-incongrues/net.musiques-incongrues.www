<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
  </head>
  <body>
    <div id="Session">
    <p>
    <a href="/forum/account/">gérer son compte</a>
     - <a href="/forum/people.php?PostBackAction=SignOutNow&amp;FormPostBackKey=cdbac4275e6f54b5bf58bd73381999ee">se déconnecter</a>
     - <a href="/forum/page/contact">nous contacter</a>
     </p>

    </div> <!-- #session -->

	<div id="search"> 
		<p> 
			<form id="SearchSimple" method="get" action="/forum/search/"> 
				<label for="search">Rechercher</label> 
				<input type="text" name="Keywords" class="champs" /> 
				<input type="hidden" name="PostBackAction" value="Search" /> 
				<input name="Submit" value="Go !" class="valid" type="submit" /> 
			</form> 
		</p> 
	</div> 

    <div id="Header">

    <h1>Musiques Incongrues</h1>

    <ul>
	    <li class="Eyes"><a href="http://www.musiques-incongrues.net/forum/events/" class="Eyes">Agenda</a></li>
	    <li class="Eyes"><a href="http://www.musiques-incongrues.net/forum/oeil/" class="Eyes">Œil</a></li>
	    <li class="Eyes"><a href="http://www.musiques-incongrues.net/forum/releases/" class="Eyes">Releases</a></li>
	    <li class="Eyes"><a href="http://www.tele-incongrue.net/" class="Eyes">TVi</a></li>
	    <li class="TabOn"><a href="/forum/discussions/" class="Pink">Discussions</a></li>
	    <li class="Pink"><a href="/forum/categories/" class="Pink">Categories</a></li>
	    <li class="dons"><a href="/forum/page/dons" class="dons">Dons</a></li>
	    <li class="Faq"><a href="http://www.musiques-incongrues.net/forum/page/faq" class="Faq">Faq</a></li>
    </ul>

    </div>
<div id="Panel">

<h1><a href="/forum/post/">Lancer une discussion</a></h1>


<h2>Ailleurs</h2>

<ul class="ailleurs-links">
<li><a href="http://www.daheardit-records.net" title="Da ! Heard It Records">Da ! Heard It Records</a></li>
<li><a href="http://www.egotwister.com" title="Ego Twister">Ego Twister</a></li>
<li><a href="http://istotassaca.blogspot.com/" title="Istota Ssaca">Istota Ssaca</a></li>
<li><a href="http://www.mazemod.org" title="Mazemod">Mazemod</a></li>
<li><a href="http://www.musiqueapproximative.net" title="Musique Approximative">Musique Approximative</a></li>
<li><a href="http://www.ouiedire.net" title="Ouïedire">Ouïedire</a></li>
<li><a href="http://www.pardon-my-french.fr" title="Pardon My French">Pardon My French</a></li>
<li><a href="http://www.thisisradioclash.org" title="Radioclash">Radioclash</a></li>
<li><a href="http://thebrain.lautre.net" title="The Brain">The Brain</a></li>
<li><a href="http://want.benetbene.net" title="WANT">WANT</a></li>
</ul>
</div>
    <?php echo $sf_content ?>
  </body>
</html>
