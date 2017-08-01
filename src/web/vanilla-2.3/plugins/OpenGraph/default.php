<?php if (!defined('APPLICATION')) exit();

$PluginInfo['OpenGraph'] = array(
	'Name' => 'OpenGraph',
	'Description' => 'Adds the Open Graph Protocol meta tags to the &lt;head&gt; section of Vanilla.',
	'Version' => '0.1.3',
	'RequiredTheme' => FALSE, 
	'RequiredPlugins' => FALSE,
	'HasLocale' => FALSE,
    'MobileFriendly' => TRUE,
    'SettingsUrl' => '/settings/opengraph',
    'SettingsPermission' => 'Garden.AdminUser.Only',
	'RegisterPermissions' => array('Plugins.OpenGraph.Manage'),
	'Author' => "Mischa Frank",
	'AuthorEmail' => 'mfrank@raute.de',
	'AuthorUrl' => 'http://vanillaforums.org/profile/HalfCat',
	'RequiredApplications' => array('Vanilla' => '2.0.18')
);

class OpenGraph extends Gdn_Plugin {

	public function Base_Render_Before($Sender) {
		// og:title
		$Title = $Sender->Head->Title();
		$Sender->Head->AddTag('meta', array('property' => 'og:title', 'content'=>$Title));
		
		// og:site_name
		$SiteName = C('Garden.Title');
		$Sender->Head->AddTag('meta', array('property' => 'og:site_name', 'content'=>$SiteName));
		
		// og:type
		if(strlen($Sender->Discussion->Body) < 1){
			$Sender->Head->AddTag('meta', array('property' => 'og:type', 'content'=>'website'));
		} else {
			$Sender->Head->AddTag('meta', array('property' => 'og:type', 'content'=>'article'));
		}
		
		// og:locale
		$Locale = C('Garden.Locale');
		$Locale = str_replace('-','_',$Locale);
		$Sender->Head->AddTag('meta', array('property' => 'og:locale', 'content'=>$Locale));

		// og:description
		$Descriptionlimit = C('Plugins.OpenGraph.DescriptionLimit');
		if ($Descriptionlimit == '' || !is_numeric($Descriptionlimit)) $Descriptionlimit = 20;
  		$DefaultDescription= C('Plugins.OpenGraph.DefaultDescription');
		$Description = $Sender->Discussion->Body;
		if(strlen($Description) > 0 || strlen($DefaultDescription) > 0){
			if(strlen($Description) < 1){
				$Description = $DefaultDescription;
			}
			$Description = explode(' ', strip_tags($Description));
			$Description = array_slice($Description,0,$Descriptionlimit);
			$Description = implode(' ',$Description);
			$Description = str_replace(array("\r\n", "\r"),'',$Description);
			$Description = str_replace('\n',' ',$Description);
			$Description = preg_replace('/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i', '', $Description);
			$Sender->Head->AddTag('meta', array('property' => 'og:description', 'content'=>$Description));
		}

		// og:url
		$URL = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		$Sender->Head->AddTag('meta', array('property' => 'og:url', 'content'=>$URL));
		
		// og:image (only if specified by user)
		$ImageURL = C('Plugins.OpenGraph.Image');
		if ((strlen($ImageURL) > 0) && (filter_var($ImageURL, FILTER_VALIDATE_URL))) {
			$Sender->Head->AddTag('meta', array('property' => 'og:image', 'content'=>$ImageURL));
		}
	}
	
   public function Setup() {
   }
   
   // Plugin Options
   public function SettingsController_OpenGraph_Create($Sender, $Args = array()) {
      $Sender->SetData('Title', T('OpenGraph Settings'));

      $Cf = new ConfigurationModule($Sender);
      $Cf->Initialize(array(
			  'Plugins.OpenGraph.DescriptionLimit' => array('Description' => 'Max. amount of words in the description. Should not be more than 50. This only affects the description that is generated from the posts in the discussions. Default description remains unaffected.<br />If left empty, limit is set to 20.', 'Control' => 'TextBox', 'Options' => array('class' => 'SmallInput')),
			  'Plugins.OpenGraph.DefaultDescription' => array('Description' => 'This description will be displayed if no discussion is selected. Should not be more than 50 words.', 'Control' => 'TextBox', 'Options' => array('MultiLine' => TRUE)),
			  'Plugins.OpenGraph.Image' => array('Description' => 'Enter the URL for an image you want to include (e.g. your logo). Your image must be at least 200px of width and height. Remember to input the full URL like so: http://i.imgur.com/62bAC.jpg<br />If left empty, tag will not be set.')
          ));

      $Sender->AddSideMenu('dashboard/settings/plugins');
      $Cf->RenderAll();
   }
}
