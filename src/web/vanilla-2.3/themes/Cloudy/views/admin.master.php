<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-ca">
<head>
   <?php $this->RenderAsset('Head'); ?>
   <!-- Robots should not see the dashboard, but tell them not to index it just in case. -->
   <meta name="robots" content="noindex,nofollow" />
<link rel="stylesheet" href="http://cdn.linearicons.com/free/1.0.0/icon-font.min.css">
<style> 
#Head h1 {margin-right:10px;}
#Head {background:#333;padding-top:30px;padding-bottom:35px;}
#Head div.User a, #Head h1 a span,#Head div.User a.Profile,.steam-rivera,.steam-rivera2 {border:none;background: rgba(255, 255, 255, 0.15);transition: all 0.3s;opacity:1.0; padding: 5px 10px; font-size: 13px; border-radius: 3px;text-shadow: none;}
#Head div.User a:hover, #Head h1 a span:hover,#Head div.User a.Profile:hover,.steam-rivera:hover,.steam-rivera2:hover{ border:none;padding: 5px 10px;opacity:1;background: rgba(255, 255, 255, 0.3);}
.steam-tiva span {display:none;}
.steam-riva {font-size:0px;}
.steam-riva span {background:none !important;font-size:13px;}

.steam-rivera  {display:none;cursor:pointer;margin-top:2px;float:left;margin-left:10px;color:#fff;background:#E00000;}

.steam-rivera2  {margin-top:2px;float:left;display:inline-block;margin-left:10px;color:#fff;cursor:pointer;background: rgba(255, 255, 255, 0.15);margin-left:5px;}

    

.ffol div {width:800px; margin:auto;}
.xfixed {display:none;background: #00BDD9;padding-top:50px;position:absolute;top:0px;bottom:0px;right:0px;left:0px;z-index:1000;height:100%;width:100%;}
.closeuk{ background: rgb(59, 59, 59); border-bottom: none; padding: 2px 7px; border-radius: 4px; font-size: 12px; color: #ffffff; font-weight: 400; z-index: 2000; transition: all 0.2s; position: absolute; top: 10px; right: 10px; } .closeuk:hover {cursor: pointer;opacity:0.8;}
.blockit {display:block;}



.gr2{background:#2DD4BC;position:relative;padding-left:35px;}
.gr2:hover{background:#33E0C8;padding-left:35px;}
.gr2 i {position:absolute;top:5px;left:9px;font-size:19px;}
#Head h1 a {text-transform: uppercase;}

</style>
<script type="text/javascript"> $(function() { $('#rem').click(function() { $('.xfixed').addClass('nonei'); $('.xfixed').fadeOut(200); }); }); $(function() { $('.closeuk2').click(function() { $('.xfixed').addClass('nonei'); $('.xfixed').fadeOut(200); }); }); $(function() { $('.butm').click(function() { $('.nolax').addClass('noneix'); $('.closeuk').addClass('mwneix'); $('.ffol').addClass('blockfol'); }); }); </script> <script type="text/javascript"> $(function() { $('.xfixedo').click(function() { $('.xfixedo').addClass('nonei'); $('.xfixedo').fadeOut(200); }); }); </script>


<script type="text/javascript">
$(function() {
    $('.steam-rivera').click(function() {
        $('.xfixed').addClass('blockit');
    });
});
$(function() {
    $('.closeuk').click(function() {
        $('.xfixed').removeClass('blockit');
    });
});
</script>
<?php
$str = '123456789';
$shuffled = str_shuffle($str);
?>


<link rel="stylesheet" type="text/css" href="http://themesteam.com/cdn/cube/license/check.css?<?php echo $shuffled; ?>" media="all" />

</head>
<body id="<?php echo $BodyIdentifier; ?>" class="<?php echo $this->CssClass; ?>">




   <div id="Frame">
      <div id="Head">
			<h1 class="steam-tiva"><?php echo Anchor(C('Garden.Title').' '.Wrap(T('')), '/'); ?></h1>
			
			<a href="http://www.themesteam.com/downloads/cloudy-fully-responsive-vanilla-2-theme/#purchase-steam" target="_blank"><span class="steam-rivera2 gr2"><i class="lnr lnr-star"></i> Buy Premium + Responsive and Thumbnails</span></a>
			<a href="http://www.themesteam.com/downloads/cloudy-fully-responsive-vanilla-2-theme/#shopcomments" target="_blank"><span class="steam-rivera2"><i class="lnr lnr-bubble"></i> Support via Comments</span></a>

			
         <div class="User steam-riva">
		 <?php echo Anchor(C('').' '.Wrap(T('Visit Site')), '/'); ?>
            <?php
			      $Session = Gdn::Session();
					if ($Session->IsValid()) {
						$this->FireEvent('BeforeUserOptionsMenu');
						
						$Name = $Session->User->Name;
						$CountNotifications = $Session->User->CountNotifications;
						if (is_numeric($CountNotifications) && $CountNotifications > 0)
							$Name .= Wrap($CountNotifications);
							
						echo Anchor($Name, UserUrl($Session->User), 'Profile');
						echo Anchor(T('Sign Out'), SignOutUrl(), 'Leave');
					}
				?>
         </div>
      </div>
      <div id="Body">
         <div id="Panel">
            <?php
            $this->RenderAsset('Panel');
            ?>
         </div>
         <div id="Content"><?php $this->RenderAsset('Content'); ?></div>
      </div>
      <div id="Foot">
			<?php
				$this->RenderAsset('Foot');
				echo '<div class="Version">Version ', APPLICATION_VERSION, '</div>';
				echo Wrap(Anchor(Img('/applications/dashboard/design/images/logo_footer.png', array('alt' => 'Vanilla Forums')), C('Garden.VanillaUrl')), 'div');
			?>
		</div>
   </div>
	<?php $this->FireEvent('AfterBody'); ?>
	
	
<div class="xfixed"><div class="closeuk">Close</div>
<div class="ffol"><div>
<iframe src="http://themesteam.com/contacts/?p=1" id="" width="800" height="800" scrolling="no" ></iframe>
</div></div></div>	
	
</body>
</html>