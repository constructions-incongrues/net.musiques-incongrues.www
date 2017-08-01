<!DOCTYPE html>
<html>
<head>
<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">
{asset name="Head"}
<meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body id="{$BodyID}" class="{$BodyClass}">
<div class="container whitespace">

<!-- Begin of Header -->
	<header>
		
			<div class="row norow">

			
<!-- Begin of Logo -->
					<div class="col-md-3 logocol">
					<a href="{link path="/"}">{logo}</a>
					</div>
<!-- End of Logo -->


<!-- Begin of Menu -->
					<div id="cssmenu" class="respcfulix">
					<ul>
					
					{home_link}
					{discussions_link}
					{categories_link}
					{activity_link}
					
					{if $User.SignedIn}
					<li class="active has-sub"><a class="demarket" href="#">{t c="More"}</a>
					<ul>
					{dashboard_link}
					{inbox_link}
					{profile_link}	
					{signinout_link}
					</li>
					</ul>
					</li>
					{/if}
					
					</ul>
					</div>
<!-- End of Menu -->



<!-- Begin of Mobile Menu -->
					<div class="thecover"></div>
					<div id="cssmenumob" class="resmobifulix">
					<ul class="ramnova">
					<li class="active hassub"><a class="demarket"></a>
					<ul class="reiva">
		  
					{dashboard_link}
					{discussions_link}
					{categories_link}
					{activity_link}
					{inbox_link}
					{profile_link}		
					{signinout_link}
					
					{if !$User.SignedIn}
					<li><a href="{link path="entry/register"}" rel="nofollow">{t c="Register"}</a></li>
					{/if}

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

{if !$User.SignedIn}	
<div class="Box GuestBox">			
	<h4>{t c="Howdy, Stranger!"}</h4>		
	<p>{t c="It looks like you're new here. If you want to get involved, click one of these buttons!"}</p>	

<a class="Button Primary SignInPopup black" href="{link path="entry/signin"}" rel="nofollow" class="">{t c="Sign In"}</a>
<a class="Button Primary SignInPopupi gray" href="{link path="entry/register"}" rel="nofollow">{t c="Register"}</a>
</div>
{/if}	
	
				{module name="MeModule"}
				<!-- Search Input --><div class="searchio">{searchbox}<i class="pe-7s-search"></i></div>
				<div class="steam-panel">{asset name="Panel"}</div>

<!-- Sidebar Widgets -->

		   
				</div>
<!-- End of Sidebar -->


<!-- Begin of Main -->
				<div class="Column ContentColumn" id="Content">
<!-- Breadcrumbs --><div class="BreadcrumbsWrapper">{breadcrumbs}</div>

				{asset name="Content"}</div>
<!-- End of Main -->

		</div></div>
	</section>
  
  
<!-- Begin of Foot -->
		<section id="Foot"><h6>foot</h6>
			
		
			{asset name="Foot"}
			
			
		</section>
		
	</section>
   {event name="AfterBody"}
<!-- End of Foot -->
<!-- End of Content -->

   
   
   
<!-- Begin of Footer -->



<div class="row norow footerwidets">



<div class="footerw col-md-6">
<div class="emid">
<h3>{logo}</h3><span> | make vanilla clean and light</span>
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

{literal}
<script type="text/javascript">
$(".blackit span").text(function(o,e){return e.replace("Back to Home","Back to Home")}),$(document).ready(function(){$(".SignInPopup").removeClass("SignInPopup").addClass("steamjq")}),$(document).ready(function(){$(".Popup").removeClass("Popup").addClass("steamjq")});var viewportWidth=$(window).width(),viewportHeight=$(window).height();$(window).resize(function(){}),$(".resmobifulix").on("click",function(){$(".thecover").addClass("theblock"),$('ul[class="reiva"]').addClass("blockdit"),$('ul[class="ramnova"]').addClass("coloritd")}),$(".thecover").on("click",function(){$(".thecover").removeClass("theblock"),$(".reiva").removeClass("blockdit"),$(".ramnova").removeClass("coloritd")});
</script>
{/literal}


<!-- End of Import Jquery Scripts -->

</div>
</body>
</html>