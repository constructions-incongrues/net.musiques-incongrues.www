<?php /* Smarty version 2.6.29, created on 2017-05-10 03:39:24
         compiled from /vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'asset', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 6, false),array('function', 'link', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 22, false),array('function', 'logo', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 22, false),array('function', 'home_link', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 31, false),array('function', 'discussions_link', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 32, false),array('function', 'categories_link', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 33, false),array('function', 'activity_link', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 34, false),array('function', 't', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 37, false),array('function', 'dashboard_link', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 39, false),array('function', 'inbox_link', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 40, false),array('function', 'profile_link', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 41, false),array('function', 'signinout_link', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 42, false),array('function', 'module', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 111, false),array('function', 'searchbox', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 112, false),array('function', 'breadcrumbs', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 124, false),array('function', 'event', '/vagrant/src/web/vanilla-2.3/themes/Cloudy/views/default.master.tpl', 143, false),)), $this); ?>
<!DOCTYPE html>
<html>
<head>
<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">
<?php echo smarty_function_asset(array('name' => 'Head'), $this);?>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body id="<?php echo $this->_tpl_vars['BodyID']; ?>
" class="<?php echo $this->_tpl_vars['BodyClass']; ?>
">
<div class="container whitespace">

<!-- Begin of Header -->
	<header>
		
			<div class="row norow">

			
<!-- Begin of Logo -->
					<div class="col-md-3 logocol">
					<a href="<?php echo smarty_function_link(array('path' => "/"), $this);?>
"><?php echo smarty_function_logo(array(), $this);?>
</a>
					</div>
<!-- End of Logo -->


<!-- Begin of Menu -->
					<div id="cssmenu" class="respcfulix">
					<ul>
					
					<?php echo smarty_function_home_link(array(), $this);?>

					<?php echo smarty_function_discussions_link(array(), $this);?>

					<?php echo smarty_function_categories_link(array(), $this);?>

					<?php echo smarty_function_activity_link(array(), $this);?>

					
					<?php if ($this->_tpl_vars['User']['SignedIn']): ?>
					<li class="active has-sub"><a class="demarket" href="#"><?php echo smarty_function_t(array('c' => 'More'), $this);?>
</a>
					<ul>
					<?php echo smarty_function_dashboard_link(array(), $this);?>

					<?php echo smarty_function_inbox_link(array(), $this);?>

					<?php echo smarty_function_profile_link(array(), $this);?>
	
					<?php echo smarty_function_signinout_link(array(), $this);?>

					</li>
					</ul>
					</li>
					<?php endif; ?>
					
					</ul>
					</div>
<!-- End of Menu -->



<!-- Begin of Mobile Menu -->
					<div class="thecover"></div>
					<div id="cssmenumob" class="resmobifulix">
					<ul class="ramnova">
					<li class="active hassub"><a class="demarket"></a>
					<ul class="reiva">
		  
					<?php echo smarty_function_dashboard_link(array(), $this);?>

					<?php echo smarty_function_discussions_link(array(), $this);?>

					<?php echo smarty_function_categories_link(array(), $this);?>

					<?php echo smarty_function_activity_link(array(), $this);?>

					<?php echo smarty_function_inbox_link(array(), $this);?>

					<?php echo smarty_function_profile_link(array(), $this);?>
		
					<?php echo smarty_function_signinout_link(array(), $this);?>

					
					<?php if (! $this->_tpl_vars['User']['SignedIn']): ?>
					<li><a href="<?php echo smarty_function_link(array('path' => "entry/register"), $this);?>
" rel="nofollow"><?php echo smarty_function_t(array('c' => 'Register'), $this);?>
</a></li>
					<?php endif; ?>

					</ul>
					</li>

					</ul>
					</div>
<!-- End of Mobile Menu -->


			</div>
		
	</header>
<!-- End of Header -->




<!-- Begin of Content -->
	<section id="Frame"><h6>frame</h6>
		<section id="Body">
			<div class="relative"><div class="row norow">
			
<!-- Begin of Sidebar -->
				<div class="steam-line"></div>
				
			
				
				<div class="Column PanelColumn" id="Panel">

<?php if (! $this->_tpl_vars['User']['SignedIn']): ?>	
<div class="Box GuestBox">			
	<h4><?php echo smarty_function_t(array('c' => "Howdy, Stranger!"), $this);?>
</h4>		
	<p><?php echo smarty_function_t(array('c' => "It looks like you're new here. If you want to get involved, click one of these buttons!"), $this);?>
</p>	

<a class="Button Primary SignInPopup black" href="<?php echo smarty_function_link(array('path' => "entry/signin"), $this);?>
" rel="nofollow" class=""><?php echo smarty_function_t(array('c' => 'Sign In'), $this);?>
</a>
<a class="Button Primary SignInPopupi gray" href="<?php echo smarty_function_link(array('path' => "entry/register"), $this);?>
" rel="nofollow"><?php echo smarty_function_t(array('c' => 'Register'), $this);?>
</a>
</div>
<?php endif; ?>	
	
				<?php echo smarty_function_module(array('name' => 'MeModule'), $this);?>

				<!-- Search Input --><div class="searchio"><?php echo smarty_function_searchbox(array(), $this);?>
<i class="pe-7s-search"></i></div>
				<div class="steam-panel"><?php echo smarty_function_asset(array('name' => 'Panel'), $this);?>
</div>

<!-- Sidebar Widgets -->

		   
				</div>
<!-- End of Sidebar -->


<!-- Begin of Main -->
				<div class="Column ContentColumn" id="Content">
<!-- Breadcrumbs --><div class="BreadcrumbsWrapper"><?php echo smarty_function_breadcrumbs(array(), $this);?>
</div>

				<?php echo smarty_function_asset(array('name' => 'Content'), $this);?>
</div>
<!-- End of Main -->

		</div></div>
	</section>
  
  
<!-- Begin of Foot -->
		<section id="Foot"><h6>foot</h6>
			
		
			<?php echo smarty_function_asset(array('name' => 'Foot'), $this);?>

			
			
		</section>
		
	</section>
   <?php echo smarty_function_event(array('name' => 'AfterBody'), $this);?>

<!-- End of Foot -->
<!-- End of Content -->

   
   
   
<!-- Begin of Footer -->



<div class="row norow footerwidets">



<div class="footerw col-md-6">
<div class="emid">
<h3><?php echo smarty_function_logo(array(), $this);?>
</h3><span> | make vanilla clean and light</span>
</div>
@ 2016 Your Sitename, All rights reserved. Material Design is a design language developed by Google. Material Design makes more liberal use of grid-based layouts and responsive animations and transitions.<br />
<span class="mouc">Powered by <a class="fixf" href="http://vanillaforums.org/">VanillaForums</a>, Designed by <a class="fixf" href="http://www.themesteam.com/" title="ThemeSteam.com">ThemeSteam</a></span></div>



<div class="footerw col-md-3"><h3 class="footitle">Contact us</h3>
<div class="stm-contact">
<i class="fa fa-envelope"></i>
info@example.com <br />
support@example.com

</div>
<div class="stm-contact">
<i class="fa fa-phone"></i>
(800) 3032120

</div>



</div>


<div class="footerw col-md-3"><h3 class="footitle">Get In Touch</h3>
<div class="lbflink">

<a href="#"><i class="fa fa-facebook-square"></i></a>
<a href="#"><i class="fa fa-twitter-square"></i></a>
<a href="#"><i class="fa fa-google-plus-square"></i></a>
<a href="#"><i class="fa fa-pinterest-square"></i></a>


</div></div>



</div>

</div></div>   


<!-- End of Footer -->

<!-- Import Jquery Scripts -->

<?php echo '
<script type="text/javascript">
$(".blackit span").text(function(o,e){return e.replace("Back to Home","Back to Home")}),$(document).ready(function(){$(".SignInPopup").removeClass("SignInPopup").addClass("steamjq")}),$(document).ready(function(){$(".Popup").removeClass("Popup").addClass("steamjq")});var viewportWidth=$(window).width(),viewportHeight=$(window).height();$(window).resize(function(){}),$(".resmobifulix").on("click",function(){$(".thecover").addClass("theblock"),$(\'ul[class="reiva"]\').addClass("blockdit"),$(\'ul[class="ramnova"]\').addClass("coloritd")}),$(".thecover").on("click",function(){$(".thecover").removeClass("theblock"),$(".reiva").removeClass("blockdit"),$(".ramnova").removeClass("coloritd")});
</script>
'; ?>



<!-- End of Import Jquery Scripts -->

</div>
</body>
</html>