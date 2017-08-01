<?php
$PluginInfo['Hashtag'] = array(
    'Name' => 'Hashtag',
	'Description' => 'Provides #Hashtag support (Automatic creation of Vanilla Tags, side panel display, auto-links and meta area display).',
    'Version' => '2.1.2',
    'RequiredApplications' => array('Vanilla' => '2.2'),
    'RequiredTheme' => false,
	'RequiredPlugins' => array('Tagging' => '1.8.2'),
	'MobileFriendly' => true,
    'HasLocale' => true,
	'SettingsUrl' => '/settings//Hashtag',
    'SettingsPermission' => 'Garden.Settings.Manage',
	'RegisterPermissions' => array('Plugins.Hashtag.View','Plugins.Hashtag.Add'),
    'Author' => "Roger Brahmson",
	'GitHub' => "https://github.com/rbrahmson/VanillaPlugins/tree/master/Hashtag",
	'License' => "GNU GPL3"
);
	/////////////////////////////////////////////////////////

class HashtagPlugin extends Gdn_Plugin {
	/////////////////////////////////////////////////////////
	// Set the CSS
	public function assetModel_styleCss_handler($Sender) {
        $Sender->addCssFile('hashtag.css', 'plugins/Hashtag');	
    }
	/////////////////////////////////////////////////////////
	//Plugin Settings
    public function settingsController_Hashtag_create ($Sender, $Args) {
        $Sender->permission('Garden.Settings.Manage');
		$Debug = false;
		$Sender->addCssFile('hashtag.css', 'plugins/Hashtag');
        $Sender->setData('Title', t('Settings for the Hashtag Plugin'));
        $Sender->addSideMenu('dashboard/settings/plugins');
		$this->SettingDefaults($Sender,'settingsController');
		$Goterror =false;
		$TopWarning = '';
		$FieldErrors = '';
		//
		$ConfigurationModule = new ConfigurationModule($Sender);
		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$Sender->Form->SetModel($ConfigurationModel);
		//
		if ($Sender->Form->authenticatedPostBack()) {
			$Saved = $Sender->Form->showErrors();
			$Saved = $Sender->Form->Save();
			$FormPostValues = $Sender->Form->formValues();
			$Sender->Form->SetData($FormPostValues);
			$Validation = new Gdn_Validation();
			$Data = $Sender->Form->formValues();
			//		Flag to also check body for hashtags
			$SearchBody = intval(getvalue('Plugins.Hashtag.SearchBody',$Data));
			//		Flag to link hashtags
			$EmbedLinks = getvalue('Plugins.Hashtag.EmbedLinks',$Data);
			//  Minimum number of letters in an #Hashtag
			$Minletters = getvalue('Plugins.Hashtag.Minletters',$Data);
			$Showrelated = getvalue('Plugins.Hashtag.Showrelated',$Data);
			$Panelhead = getvalue('Plugins.Hashtag.Maxletters',$Data);
			$Panelsize = getvalue('Plugins.Hashtag.Panelsize',$Data);
			$Panelontop = getvalue('Plugins.Hashtag.Panelontop',$Data);
			//
			$FieldErrors = $this->CheckField($Sender,$Minletters,
							Array('Integer'=>'?','Required'=> 'Integer','Min'=> 2,'Max'=>10),
							'Minimum number of letters in a #hashtag','Plugins.Hashtag.Minletters');
			//  Maximum number of letters in an #Hashtag
			$Maxletters = getvalue('Plugins.Hashtag.Maxletters',$Data);
			if ($FieldErrors == '') $FieldErrors = $this->CheckField($Sender,$Maxletters,
							Array('Integer'=>'?','Required'=> 'Integer','Min'=> 4,'Max'=>140),
							'Maximum number of letters in a #hashtag','Plugins.Hashtag.Maxletters');
			if ($FieldErrors == '') {
				if ($Minletters >= $Maxletters) {
					$FieldErrors = wrap('Maximum number of letters should be bigger than the minimum number of letters.',
										'span class=SettingError');
					$addError = $Sender->Form->addError($FieldErrors,'Plugins.Hashtag.Maxletters');
				}
			}
			//
			if ($Showrelated)  {
				if ($Panelhead == '') {
					$Panelhead = t('Similar Hashtag Set');
					SaveToConfig('Plugins.Hashtag.Panelhead',$Panelhead);
				}
				//Verifying translation doesn't break the requirement
				$FieldErrors = $this->CheckField($Sender,$Panelhead,
								Array('Required'),
								'Title for sidepanel. ','Plugins.Hashtag.Panelhead');	   
			
				$Panelsize = getvalue('Plugins.Hashtag.Panelsize',$Data);
				if ($FieldErrors == '') {
					$FieldErrors = $this->CheckField($Sender,$Panelsize,
							Array('Required'=> 'Integer','Min'=> 1,'Max'=>20),
							'Side panel size can be between 1 and 20','Plugins.Hashtag.Panelsize');
				}
				//Handle Order of side panel
				if ($FieldErrors == '') {	
					$Sidepanelname = 'TagRelatedModule';
					$Panelorder =  c('Modules.Vanilla.Panel');
					$Entrynum = array_search($Sidepanelname, $Panelorder);
					if ($Panelontop) {
						if ($Entrynum == '' | $Entrynum != 0) {						
							array_unshift($Panelorder, $Sidepanelname);
							$Panelorder = array_unique($Panelorder);
						}
					} else {
						if ($Panelorder[0] == $Sidepanelname) {
							unset($Panelorder[0]);
							$Panelorder = array_values($Panelorder);
						}
					}
					SaveToConfig('Modules.Vanilla.Panel',$Panelorder);
				}
			}
			// Validate flags	
			if ($FieldErrors == '' && $EmbedLinks) {
				if (!$SearchBody) {
					$FieldErrors = wrap('You turned on "Link Embedded #Hashtags" which required the "Check Body for #Hashtags" Option.',
									'span class=SettingWarning');
					$addError = $Sender->Form->addError($FieldErrors,'Plugins.Hashtag.EmbedLinks');
				} elseif (c('Garden.Format.Hashtags')) {
					$FieldErrors = wrap('You turned on "Link Embedded #Hashtags". This will be ignored until you turn off "Garden.Format.Hashtags" in config.php.',
									'span class=SettingWarning');
					$addError = $Sender->Form->addError($FieldErrors,'Plugins.Hashtag.EmbedLinks');
				}
			}
			//
			if ($FieldErrors != '') {
				$Goterror=true;
				$Sender=$Validation->addValidationResult('Plugins.Hashtag.SearchBody', ' ');
				$TopWarning = t('Errors need to be corrected. Incomplete settings saved');
				Gdn::controller()->informMessage($TopWarning);//,'DoNotDismiss');
			}
			if (!$Validation->validate($FormPostValues)) $Goterror=true;
			if ($Goterror) {
				$Sender=$Validation->addValidationResult('Plugins.Hashtag.SearchBody', ' ');
				SaveToConfig('Plugins.Hashtag.IncompleteSetup',true);
			} else {
				SaveToConfig('Plugins.Hashtag.IncompleteSetup',false);
			}
		// NOT POSTBACK
		} else {
			if (c('Plugins.Hashtag.IncompleteSetup')) 
				$TopWarning = 'Previously saved settings are incomplete/invalid.  Review and save correct values.';
			if (!c('EnabledPlugins.Tagging',false)) 
				$TopWarning = 'The Tagging plugin must be enabled for the Hashtag plugin to be operational.';
			$Sender->Form->SetData($ConfigurationModel->Data);
        }
		$Feedbackrray['TopWarning'] = $TopWarning;
		//
		$PluginConfig = $this->SetConfig($Sender,$Feedbackrray,$Debug);// Array('TopWarning' => $TopWarning),$Debug);
		$ConfigurationModule->initialize($PluginConfig);
		$ConfigurationModule->renderAll();
    }
	///////////////////////////////////////////////////////// 
   public function SettingDefaults($Sender,$CallType = '') {
	   //Set default config options
	   $Debug = false;
	   
	   $Minletters = c('Plugins.Hashtag.Minletters',4);
	   SaveToConfig('Plugins.Hashtag.Minletters',intval($Minletters));
	   
	   $Maxletters = c('Plugins.Hashtag.Maxletters',140);
	   SaveToConfig('Plugins.Hashtag.Maxletters',$Maxletters);
	   
	   $SearchBody = c('Plugins.Hashtag.SearchBody',false);
	   SaveToConfig('Plugins.Hashtag.SearchBody',$SearchBody);
	   
	   $EmbedLinks = c('Plugins.Hashtag.EmbedLinks',false);
	   SaveToConfig('Plugins.Hashtag.EmbedLinks',$EmbedLinks);
	  
	   $Showrelated = c('Plugins.Hashtag.Showrelated',false);
	   SaveToConfig('Plugins.Hashtag.Showrelated',intval($Showrelated));	
	   
	   $Panelhead = c('Plugins.Hashtag.Panelhead');
	   if ($Panelhead == '') {
			$Panelhead = t('Similar Hashtag Set');
		}
	   SaveToConfig('Plugins.Hashtag.Panelhead',$Panelhead);	
	   
	   $HideEmptyPanel = c('Plugins.Hashtag.HideEmptyPanel',true);
	   SaveToConfig('Plugins.Hashtag.HideEmptyPanel',$HideEmptyPanel);
	   
	   $Panelsize = c('Plugins.Hashtag.Panelsize',8);
	   SaveToConfig('Plugins.Hashtag.Panelsize',$Panelsize);
	   
	   $Showinline = c('Plugins.Hashtag.Showinline');
	   if ($Showinline == '') {
		   SaveToConfig('Plugins.Hashtag.Showinline',false);
		} else {
			SaveToConfig('Plugins.Hashtag.Showinline',true);
		}
		
	   $Panelontop = c('Plugins.Hashtag.Panelontop');
	   if ($Panelontop == '') {
		   SaveToConfig('Plugins.Hashtag.Panelontop',false);
		}
	}
	/////////////////////////////////////////////////////////
// Set Configiration Array
	public function SetConfig($Sender,$Errors = Array(),$Debug) {
		$Separator = '<span class=SettingSep>&nbsp</span>';
		$Headstyle = '<span class=SettingHead>#&nbsp&nbsp';
		$Subhstyle = '<span class=SettingSubh>';
		$Textstyle = '<span class=SettingText>';
		$Warnstyle = '<span class=SettingWarning>';
		$Errorstyle = '<span class=SettingError>';
		$Squeeze = '<span class=Settingsqueeze> </span>';
		$Notestyle = '<span class=SettingNote>';
		$Topmessage = '';
		if (trim($Errors['TopWarning'])) $Topmessage .= $Warnstyle.$Errors['TopWarning'].'</span>';
		$WarnGarden = '';
		if (c('Garden.Format.Hashtags')) $WarnGarden = '<span class=SettingGardenWarning>'.t('Currently it is <b>not</b> turned off').'</span>';
		if (c('Plugins.Hashtag.Showrelated')) 
			$Showrelatednote = wrap('<br><b>Note:</b>The internal name of the sidepanel is "TagRelatedModule". You may need it for the ModuleSort plugin.',
									'span class="SettingText"');
		//
		$PluginConfig = array(
		/*- Option to search body for #Hashtags-*/
			'Plugins.Hashtag.SearchBody' => array(
			'Control' => 'CheckBox',
			'Description' => 	$Topmessage.
								$Subhstyle.'<b>General Note:</b></span>'.
								$Textstyle.'This plugin auto-creates tags from #Hashtags embedded in the discussion.  This does not override the required Vanilla permissions.'.'</span>'.
								$Textstyle.'For this plugin to work the following two permissions must be set in Roles and Permissions:'.'</span>'.
								$Textstyle.'<b>(1)</b> Plugins.Tagging.Add  and <b>(2)</b> Plugins.Hashtag.Add'.'<br><br></span>'.
								$Textstyle.'Also note that the maximum number of tags (#Hashtags are real tags) is defined by the Tagging plugin.</span>'.
								$Textstyle.'This plugin honors that maximum.  It is currently set to <b>'.c('Plugin.Tagging.Max', 5).'. </b>'.
											'Within a discussion Hashtags beyong that number will be ignored.</span>'.
								$Textstyle.'You can set that value by adding/altering<b> "$Configuration[\'Plugin\'][\'Tagging\'][\'Max\'] = \''.c('Plugin.Tagging.Max', 5).'\';" </b>in Vanilla\'s config.php.</span>'.
								$Separator.
								$Headstyle.'<b>Check Body for #Hashtags'.'</span>',
			'LabelCode' => 		$Textstyle.'Search the discussion and comment bodies for #Hashtags (otherwise only the discussion title is scanned)</span>'.$Squeeze,
			'Default' => true),
		/*- Option to turn off "" in the body (so it won't do search on hashtags (because they might be searchable as tags if autohshtag is enabled on the body)-*/
			'Plugins.Hashtag.EmbedLinks' => array(
			'Control' => 'CheckBox',
			'Description' => wrap('Link Embedded #Hashtags<br>','span class=SettingHead').
							 $Textstyle.'<b>Note:</b> Three conditions must exist for this feature to be active<br>'.
							 '<b>(1)</b> The "Check Body for #Hashtags" above must be checked.'.'</span>'.
							 $Textstyle.'<b>(2)</b> "Garden.Format.Hashtags" must be set to "false" in config.php. '.$WarnGarden.'</span>'.
							 $Textstyle.'<b>(3)</b> Plugins.Hashtag.View must be set in Roles and Permissions'.'</span>',
			'LabelCode' => 	$Textstyle.'Set the embedded #hashtags to link to other discussions tagged with the same hashtags.</span>'.$Squeeze,
			'Default' => true),
		/*- Minimum number of letters in a #word to be considered a Hashtag-*/
			'Plugins.Hashtag.Minletters' => array(
			'Control' => 'textbox',
			'Description' => $Textstyle.'Minimum number of letters in a #word to be considered a Hashtag:<span>'.$Errors['Minletters'],
			'LabelCode' => $Headstyle.'Hashtag Sizing:<span>',
			'Default' => 4),
		/*- Maximum number of letters in a #word to be considered a Hashtag-*/
			'Plugins.Hashtag.Maxletters' => array(
			'Control' => 'textbox',
			'Description' => $Textstyle.'Maximum number of letters in a #word to be considered a Hashtag:<span>'.$Errors['Maxletters'],
			'LabelCode' => '' ,
			'Default' => 140),
		/*- Control whether to show hashtags for each discussion in the discussion list-*/
			'Plugins.Hashtag.Showinline' => array(
			'Control' => 'checkbox',
			'LabelCode' => $Textstyle.'Display the list of hashtags for each discussion in the discussion list<span>',
			'Description' => $Headstyle.'Show the #hashtags assigned to each discussion:',
			'Default' => true),
		);
		//The following defined the settings of the "Similar Hashtags" side panel  functionality
		$SimilarConfig = array(
		/* Control whether a sidepanel of discussions with the same set of tags is to be displayed*/
			'Plugins.Hashtag.Showrelated' => array(
			'Control' => 'checkbox',
			'LabelCode' => $Textstyle.'Display a side panel of other discussions having the same set of hashtags<span>',
			'Description' => $Headstyle.'Show "Similar Hashtag Set" Side Panel:'.$Showrelatednote,
			'Default' => true),
		 /*- Title of the above sidepanel -*/
			'Plugins.Hashtag.Panelhead' => array(
			'Control' => 'textbox',
			'LabelCode' => $Textstyle.'Title of the "Similar Hashtag Set" Side Panel:<br></b>Default is "Similar Hashtag Set"<span>'.$Errors['Panelhead'],
			'Description' => '',
			'Default' => "Similar Hashtag Set"),
		 /*- Control whether to place the sidepanel on top of the list of sidepanels- */
			'Plugins.Hashtag.Panelontop' => array(
			'Control' => 'checkbox',
			'LabelCode' => $Textstyle.'Place the above side panel at the top of the displayed side panels'.
									'<br>(See ModuleSort Plugin for more control over side panels order)<span>',
			'Description' => '',
			'Default' => true),
		 /*- Set the maximum number of the above sidepanel entries- */
			'Plugins.Hashtag.Panelsize' => array(
			'Control' => 'textbox',
			'LabelCode' => $Textstyle.'Maximum number of entries in the side panel (Specify a number less than 20)<span>'.$Errors['Panelsize'],
			'Description' => '',
			'Default' => 8),
		 /*- Control whether a sidepanel of discussions with the same set of tags is to be hidden when there's nothingto show- */
			'Plugins.Hashtag.HideEmptyPanel' => array(
			'Control' => 'checkbox',
			'LabelCode' => $Textstyle.'Hide the above side panel if no other discussions have the same set of hashtags<span>',
			'Description' => $Textstyle.'Hide empty "Similar Hashtag Set" Side Panel:',
			'Default' => true),
			);
		/***** FOR FUTURE RELEASE ****/
	
		/*- Control whether a sidepanel of discussions with the same set of tags is to be displayed-*/
		$XPluginConfig = array_merge($PluginConfig,$SimilarConfig);	
		//if ($Debug) $this->Showdata($XPluginConfig,__LINE__.'---XPluginConfig---','',0,' ',true);
		return $XPluginConfig;
	}
	///////////////////////////////////////////////////////// 
// Check Configuration Settings
	public function CheckSettings($Sender,$Type='All',$Debug) {
		 if ($Debug) echo "<br><b>".__FUNCTION__.' '.__LINE__.' Called by: ' . debug_backtrace()[1]['function'];
		//
		$Warn = '';
		$Error = '';
		//Get the menu filled variables
		$Data = $Sender->Form->formValues();
		$SearchBody = getvalue('Plugins.Hashtag.SearchBody',$Data);
		$EmbedLinks = getvalue('Plugins.Hashtag.EmbedLinks',$Data);
		$Minletters = getvalue('Plugins.Hashtag.Minletters',$Data);
		$Maxletters = getvalue('Plugins.Hashtag.Maxletters',$Data);
		//
		if ($Type == 'All' || $Type == 'Errors') {
			//if ($Debug) echo '<br>'.__LINE__.'Backward:'.$Backward;
			if ($Minletters == '') $Minletters = 4;
			if (!is_numeric($Minletters) || $Minletters < 4 || $Minletters > 10) {
				if (!is_numeric($Minletters)) { 
					$Error  = $Error .'<br>Invalid minimum number of #Hashtag letters: "'.$Minletters.'" is not numeric';
				} else {
					$Error  = $Error .'<br>Invalid minimum number of #Hashtag letters:"'.$Minletters.'". Should be between 4 and 10.';
				}
			}
			//
			if ($Maxletters == '') $Maxletters = 4;
			if (!is_numeric($Maxletters) || $Maxletters < 4 || $Maxletters > 140) {
				if (!is_numeric($Maxletters)) { 
					$Error  = $Error .'<br>Invalid Maximum number of #Hashtag letters: "'.$Maxletters.'" is not numeric';
				} else {
					$Error  = $Error .'<br>Invalid Maximum number of #Hashtag letters:"'.$Maxletters.'". Should be between 4 and 10.';
				}
			}
			//
			if (!($Maxletters > $Minletters)) {
				$Error  = $Error .'<br>Maximum number of letters should be larger than the Minimum number of letters';
			}
			//
		}
		if ($Type == 'All' || $Type == 'Warnings') {
			if ($EmbedLinks && c('Garden.Format.Hashtags')) {
				$Warn  = $Warn .'<br>You turned on "Link Embedded #Hashtags". This will be ignored until you turn off "Garden.Format.Hashtags" in config.php';
			}
			if ($EmbedLinks && !$SearchBody) {
				$Warn  = $Warn .'<br>     You turned on "Link Embedded #Hashtags" which required the "Check Body for #Hashtags" Option. ';
			}
		}

		if ($Type != 'All' && $Type != 'Warnings' && $Type != 'Errors') {
			return 'Error - Parameter '.$Type.' Unaccepted by '.__FUNCTION__;
		}
		//
		//*****************************************//
		if ($Error) {
			$Error = substr($Error,4);
			//$Error = Wrap(substr($Error,4),'span class=SettingError');
		}
		if ($Warn) {
			$Warn = substr($Warn,4);
			$Warn = Wrap(substr($Warn,4),'span class=SettingWarning');
		}
		if ($Debug) 
			echo wrap('...'.__LINE__.' Error:'.$Error.' Warn:'.$Warn,'p class=SettingWarning');
		$Result = $Error.$Warn;
		return $Result;
	}
	///////////////////////////////////////////////////////// 
// Check speicific field, return error message 
	public function CheckField($Sender,$Field=false,$Checks=Array('Required'),$Title = 'Field', $Fieldname = '', $Style = 'span class=SettingError',
							$Debug = false) {
		$Errormsg='';
		foreach ($Checks as $Test => $Value) {
			//echo '<br>'.__line__.$Errormsg;
			if ($Errormsg == '') {
				//echo '<br>'.__LINE__.'Test:'.$Test.' Value:'.$Value.' on:'.$Field;
				if($Test == 'Required') {
					if ($Field == '') {
						$Errormsg='is required';
					} else {
						if ($Value == 'Integer' && !ctype_digit($Field)) {
							$Errormsg='must be an integer';
						} elseif ($Value == 'Numeric' && !is_numeric($Field)) {
							$Errormsg='must be numeric';
						} elseif ($Value == 'Alpha' && preg_match("/[0-9]+/", $Field)) {
							$Errormsg='must be alphabetic';
						}
					}
				} elseif  (($Test == 'Integer' | $Test == 'Min' | $Test == 'Max') && !ctype_digit($Field)) { 
					$Errormsg='must be an integer';
				} elseif  (($Test == 'Numeric' | $Test == 'Min' | $Test == 'Max') && !is_numeric($Field)) { 
					$Errormsg='must be numeric';
				} elseif  ($Test == 'Alpha' && preg_match("/[0-9]+/", $Field)) { 
					$Errormsg='must be alphabetic';
				} elseif  ($Test == 'Min') {
					if ($Field < $Value) $Errormsg='must not be less than '.$Value;
				} elseif ($Test == 'Max') {
					if ($Field > $Value) $Errormsg='must not be greater than '.$Value;
				}
			}
		}
		//echo '<br>'.__line__.$Errormsg;
		if ($Errormsg != '') {
			$Errormsg = wrap(t($Title).' '.t($Errormsg),$Style);
			if ($Fieldname != '') $addError = $Sender->Form->addError($Errormsg, $Fieldname);
		}
		//echo '<br>'.__line__.$Errormsg;
		return $Errormsg;
	}
	/////////////////////////////////////////////////////////
	//Handle auto-linking #hashtags embedded in the body 
	public function DiscussionController_AfterCommentFormat_Handler($Sender) {
		$Debug = false;
		if ($Debug) {
			$Msg = __FUNCTION__.' '.__LINE__.' Called by: ' . debug_backtrace()[1]['function'];
			Gdn::controller()->informMessage($Msg);
			echo Wrap($Msg,'br');
		}
		//  Do this only if Vanilla doesn't format hashtgs and the admin asked for this option
		if (c('Garden.Format.Hashtags')) 		return;  
		if (!c('Plugins.Hashtag.EmbedLinks')) 	return;
		if (!c('Plugins.Hashtag.SearchBody')) 	return;
		if (!Gdn::session()->checkPermission('Plugins.Hashtag.View')) return; //This requires View Hashtags permission
		if (!c('EnabledPlugins.Tagging',false)) return;
		//
        $Object = $Sender->EventArguments['Object'];
		$FormatBody = $Object->FormatBody;
		$CommentID = getValueR('CommentID',$Object);
		$DiscussionID = getValueR('DiscussionID',$Object);
		/*
		if ($Debug) {
			$this->Showdata($DiscussionID,__LINE__.'---DiscussionID---','',0,' ',true);
			$this->Showdata($CommentID,__LINE__.'---CommentID---','',0,' ',true);
			$this->Showdata($Object->FormatBody,__LINE__.'---$Object->FormatBody---','',0,' ',true);
			$this->Showdata($Object,__LINE__.'---Object---','',0,' ',true);
		}
		if (substr($FormatBody.'          ',0,10)  == "**DEBUG*!/")  $Debug = true;
		*/
		// Handle #hashtag embedded in the body 
		// creating links like: /discussions/tagged/hashtag
		$NumTagsMax = c('Plugin.Tagging.Max', 5);
		//
		//Search for hashtags
		$TagPattern = $this->GetTagPattern();
		if ($Debug) $this->Showdata($TagPattern,__LINE__.'---TagPattern---','',0,' ',true);
		$TagAnchor  = anchor('$1$2$3$4$5', '/discussions/tagged/$3$4$5');
		$Mixed = preg_replace($TagPattern, $TagAnchor, $FormatBody,$NumTagsMax,$Replacements); 
		//if ($Debug) $this->Showdata($NumTagsMax,__LINE__.'---NumTagsMax---','',0,' ',true);
		//if ($Debug) $this->Showdata($Replacements,__LINE__.'---Replacements---','',0,' ',true);
		if ($Mixed == NULL) {
			$Mixed = $FormatBody;
			$Msg = __FUNCTION__.' '.__LINE__.' encountered an error in hashtag plugin ';
			Gdn::controller()->informMessage($Msg);
			decho ($Msg);
			return;
		}
		//
		$Sender->EventArguments['Object']->FormatBody = $Mixed;
	}
	/////////////////////////////////////////////////////////
	//Show hashtag sidepanel
	public function DiscussionController_BeforeDiscussionRender_Handler($Sender) {
		$Debug = false;
		if (!c('Plugins.Hashtag.Showrelated',false)) return;
		if (!c('EnabledPlugins.Tagging',false)) return;
        $Limit = c('Plugins.Hashtag.Panelsize',8);
        $ModuleToAdd = new TagRelatedModule($Sender);
		$Sender->AddModule($ModuleToAdd, 'Panel' ,$Sender);
        $ModuleToAdd->GetRelated($Sender->data('Discussion.DiscussionID'), $Limit, $Debug);
	}
	/////////////////////////////////////////////////////////
	public function categoriesController_afterCountMeta_handler($Sender,$Args) {
		$Debug = false;
		//if ($Debug) echo "<br><b>".__FUNCTION__.' '.__LINE__.' Called by: ' . debug_backtrace()[1]['function'];
		if (!c('Plugins.Hashtag.Showinline',false)) return;
		if (!c('EnabledPlugins.Tagging',false)) return;
		$Discussion = $Args['Discussion'];
		$this->Listinline($Sender,$Discussion,$Debug);
	}
	/////////////////////////////////////////////////////////
	public function discussionsController_afterCountMeta_handler($Sender,$Args) {
		$Debug = false;
		//if ($Debug) echo "<br><b>".__FUNCTION__.' '.__LINE__.' Called by: ' . debug_backtrace()[1]['function'];
		if (!c('Plugins.Hashtag.Showinline',false)) return;
		if (!c('EnabledPlugins.Tagging',false)) return;
		$Discussion = $Args['Discussion'];
		$this->Listinline($Sender,$Discussion,1,$Debug);
	}
	public function Listinline($Sender,$Discussion,$Debug = false) {
		//if ($Debug) echo "<br><b>".__FUNCTION__.' '.__LINE__.' Called by: ' . debug_backtrace()[1]['function'];
		//if ($Debug) $this->Showdata($Discussion,__LINE__.'---Discussion---','',0,' ',true);
		$Currenttags = $this->GetCurrentTags($Sender,$Discussion,0,$Debug);
		//if ($Debug) $this->Showdata($Currenttags,__LINE__.'---Currenttags---','',0,' ',true);
		$Taglist = '';
		$Tagcount = 0;
		$Tagseparator = '';
		foreach ($Currenttags as $Key => $Tagentry) {
			//if ($Debug) echo '<br>'.__LINE__.' Key:'.$Key.' Tagentry:'.$Tagentry;
			//if ($Debug) $this->Showdata($Tagentry,__LINE__.'---Tagentry---','',0,' ',true);
			$Tagname = $Tagentry['Name'];
			$Tagfullname = $Tagentry['FullName'];
			if (substr($Tagfullname,0,1) == '#') {
				//if ($Debug) echo '<br>'.__LINE__.' Tagname:'.$Tagname.' Tagfullname:'.$Tagfullname;
				$Tagcount = $Tagcount + 1;
				$Anchor = wrap(Anchor($Tagfullname,'/discussions/tagged/'.$Tagname,'HashTagsLink tag_'.$Tagname),'li  class="HashTagsLI" ');
				$Taglist =  ' ' . $Taglist . $Tagseparator. ' ' . $Anchor;
				$Tagseparator = ',';
			}
		}
		if ($Tagcount) {
			echo 	wrap(t('Hashtagged').':'.wrap($Taglist,'ul class="HashTagsUL" '),'div class="InlineHashTags" ');
		}

	}
	///////////////////////////////////////////////////////// 
	public function GetCurrentTags($Sender,$Discussion,$Debug = false) {
		if ($Debug) {
			$Msg = __FUNCTION__.' '.__LINE__.' Called by: ' . debug_backtrace()[1]['function'].debug_backtrace()[0]['line'].' ---> '. debug_backtrace()[0]['function'];
			Gdn::controller()->informMessage($Msg);
			echo Wrap($Msg,'br');
		}
		//
		$DiscussionID = $Discussion->DiscussionID;
		$Sqlfields = 't.TagID, t.Name, t.FullName, td.DiscussionID, td.TagID';
		if ($Debug) {
			$Sqlfields = 't.TagID, t.Name, t.FullName, t.CountDiscussions, t.Dateinserted tDateinserted, td.Dateinserted tdDateinserted, td.DiscussionID, td.TagID';
			//$this->Showdata($Discussion,__LINE__.'---Discussion---','',0,' ',true);
			//decho($Discussion);
			$this->Showdata($DiscussionID,__LINE__.'---DiscussionID---','',0,' ',true);
			$this->Showdata($Discussion->Name,__LINE__.'---Name---','',0,' ',true);
			$this->Showdata($Discussion->Body,__LINE__.'---Body---','',0,' ',true);
		}	
		//
		$TagSql = clone Gdn::sql();	//Don't interfere with any other sql process
		$TagSql->Reset();			//Clean slate
		$Taglist = $TagSql			//Get expanded tag info for this discussion
			->select($Sqlfields)
            ->from('TagDiscussion td')
			->join('Tag t', 't.TagID = td.TagID')
            ->where('td.discussionID', $DiscussionID)
            ->get()->resultArray();
		if ($Debug) $this->Showdata($Taglist,__LINE__.'---Taglist---','',0,' ',true);
		/*Sample Structure:
			Taglist--- array 
			....(1) array(5):[0]: value: array 
			........(2) _integer:TagID value:"7"
			........(2) string:Name value:"test"
			........(2) string:FullName value:"#test"
			........(2) _integer:CountDiscussions value:"2"
			........(2) string:tDateinserted value:"2015-05-10 13:28:44"
			........(2) string:tdDateinserted value:"2015-05-10 23:39:12"
			........(2) _integer:DiscussionID value:"1627"
			....(1) array(5):[1]: value: array 
			........(2) _integer:TagID value:"10"
			........(2) string:Name value:"self"
			........(2) string:FullName value:"#self"
			........(2) _integer:CountDiscussions value:"1"
			........(2) string:tDateinserted value:"2015-05-10 23:39:12"
			........(2) string:tdDateinserted value:"2015-05-10 23:39:12"
			........(2) _integer:DiscussionID value:"1627"
		*/
		//$Alltags = rtrim($Hashtags.','.$Tags,', ');
		//if ($Debug) $this->Showdata($Alltags,__LINE__.'---Alltags---','',0,' ',true);
		return $Taglist;
	}
	/////////////////////////////////////////////////////////
    public function GetHashTags($Sender,$Name,$Body,$Debug = false) {
		if ($Debug) {
			$Msg = __FUNCTION__.' '.__LINE__.' Called by: ' . debug_backtrace()[1]['function'].debug_backtrace()[0]['line'].' ---> '. debug_backtrace()[0]['function'];
			Gdn::controller()->informMessage($Msg);
			echo Wrap($Msg,'br');
		}
        //$Debug = true;
		if (substr($Body.'          ',0,10)  == "**DEBUG*!/")  $Debug = true;
		//Search for hashtags
		$Body = strip_tags(str_replace("&nbsp;",' ',$Name.' '.$Body.' '),'<br>');		//These html spaces interfere with parsing blanks...
		//
		$TagPattern = $this->GetTagPattern();
		$MatchCount = preg_match_all($TagPattern,$Body, $Matches);
		//if ($Debug) $this->Showdata($Matches,__LINE__.' Matches:','',0,' ',true);
		$Hashtags = '';
		$NumTagsMax = c('Plugin.Tagging.Max', 5);
		$NumTags = 0;
		for ($i = 0; $i <= $MatchCount; $i++) {
			$Tag = strtolower($Matches[0][$i]);
			if ($Matches[1][$i] != '') $Tag = ltrim($Tag,$Matches[1][$i]);
			if ($NumTags >= $NumTagsMax) break;
			$Hashtags =  $Hashtags . ',' . $Tag;
			$NumTags += 1;
		}		
		$Hashtags = trim($Hashtags,', ');
		//if ($Debug) $this->Showdata($Hashtags,__LINE__.' Hashtags:','',0,' ',true);
		//if ($Debug) die(0);
		return $Hashtags;
    }
	/////////////////////////////////////////////////////////
	//This hook handles the saving of comments (but not the initial discussion body).
	public function PostController_AfterCommentSave_Handler($Sender, $Args) {
		$Debug = false;
		if ($Debug) {
			$Msg = __FUNCTION__.' '.__LINE__.' Called by: ' . debug_backtrace()[1]['function'].' ---> '. debug_backtrace()[0]['function'];
			Gdn::controller()->informMessage($Msg);
			echo Wrap($Msg,'br');
		}
		if (!Gdn::session()->checkPermission('Plugins.Tagging.Add')) return; //This required Add Tags permission
		if (!Gdn::session()->checkPermission('Plugins.Hashtag.Add')) return; //This required Add Hashtags permission
		if (!c('EnabledPlugins.Tagging',false)) return;
		if (!c('Plugins.Hashtag.SearchBody')) 	return;		//If not porcessing the body then we're done here
		//
		$Discussion = $Sender->EventArguments['Discussion'];
		$Comment = $Sender->EventArguments['Comment'];
		$Body = $Comment->Body;
		//
		//if ($Debug) {
			//$this->Showdata($Discussion,__LINE__.'---Discussion---','',0,' ',true);
			//decho($Discussion);
			//$this->Showdata($Discussion->DiscussionID,__LINE__.'---DiscussionID---','',0,' ',true);
			//$this->Showdata($Discussion->CategoryID,__LINE__.'---CategoryID---','',0,' ',true);
			//$this->Showdata($Discussion->Name,__LINE__.'---Name---','',0,' ',true);
			//$this->Showdata($Body,__LINE__.'---Body---','',0,' ',true);
		//}	
		//Get the hashtags embedded in the comment.
		$Hashtags = $this->GetHashTags($Sender,'',$Body,0,false,0,$Debug);		//Get the content embedded hashtags
		//if ($Debug) $this->Showdata($Hashtags,__LINE__.'---Hashtags---','',0,' ',true);
		//
		//if (trim($Hashtags) == '') return;
		// Now add the hashtags to this discussion
		$TagModel = new TagModel;
		$Types = array();
		TagModel::instance()->saveDiscussion($Discussion->DiscussionID, $Hashtags, $Types, $Discussion->CategoryID);
	}
	/////////////////////////////////////////////////////////	
	//Get the tags pattern for the regex expressions
	private function GetTagPattern() {
		$Minletters = c('Plugins.Hashtag.Minletters',4);
		$Maxletters = c('Plugins.Hashtag.Maxletters',140);
		$Range = '{' . ($Minletters-2) . ',' . ($Maxletters-2) . '}';		//Account for the hashtag and the first letter in the regex catch groups
		$Pattern =	'/(^|[\b]|[;*\/\s,\.>])(\#){1}([a-zA-Z]{1})([a-zA-Z0-9]' . $Range . ')(?!\#)([\w\-])(?=[&\s,\.!?;"<]|$)/im';
		return $Pattern;
	}
	/////////////////////////////////////////////////////////	
	//This hook handles the saving of the initial discussion body (but not comments).
	public function TaggingPlugin_SaveDiscussion_handler($Sender,$Args) {
		$Debug = false;
		if ($Debug) {
			$Msg = 'Saving Discussion... '. __FUNCTION__.' '.__LINE__.' Called by: ' . debug_backtrace()[1]['function'].' ---> '. debug_backtrace()[0]['function'];;
			Gdn::controller()->informMessage($Msg);
			echo Wrap($Msg,'br');
		}
		// Verify user can auto-add hashtags
		if (!Gdn::session()->checkPermission('Plugins.Tagging.Add')) return; //This required Add Tags permission
		if (!Gdn::session()->checkPermission('Plugins.Hashtag.Add')) return; //This required Add Hashtags permission
		if (!c('EnabledPlugins.Tagging',false)) return;
		//
		$FormPostValues = val('Data', $Sender->EventArguments, array());
		$Tags = val('Tags', $Sender->EventArguments,0);
		$CategoryID = val('CategoryID', $Sender->EventArguments,0);
		//
		$DiscussionID = val('DiscussionID', $FormPostValues, 0);
		$CommentID = val('CommentID', $FormPostValues, 0);
		$Tags = val('Tags', $FormPostValues, '');
		$Name = val('Name', $FormPostValues, '');
		$Body = val('Body', $FormPostValues, '');
		
		if ($Debug) {
			//echo "<br>DiscussionID:".$DiscussionID.'<br>';
			//echo "<br>CommentID:".$CommentID.'<br>';
			//echo "<br>Name:".$Name.'<br>';
			//echo "<br>Body:".$Body.'<br>';
			//echo "<br>Tags:".$Tags.'<br>';
			//decho ($FormPostValues);
			$this->Showdata($DiscussionID,'---DiscussionID---','',0,' ',true);
			$this->Showdata($CommentID,'---CommentID---','',0,' ',true);
			$this->Showdata($Name,'---Name---','',0,' ',true);
			$this->Showdata($Body,'---Body---','',0,' ',true);
			$this->Showdata($Tags,'---Tags---','',0,' ',true);
			$this->Showdata($FormPostValues,'---FormPostValues---','',0,' ',true);
			//die(0);
		}
		
		$SearchBody = c('Plugins.Hashtag.SearchBody',false);
		if (!$SearchBody) {									//Automatic Hashtags only set on the discussion title?
			if ($CommentID) return;							//Then if this is a comment there is nothing more to do
			$Body = '';										//Don't look at the body
		}
		$Hashtags = $this->GetHashTags($Sender,$Name,$Body,$Debug);		//Get the content embedded hashtags
		if ($Debug) $this->Showdata($Hashtags,__LINE__.'---Hashtags---','',0,' ',true);
		$Alltags = rtrim($Hashtags.','.$Tags,', ');
		if ($Debug) $this->Showdata($Alltags,__LINE__.'---Alltags---','',0,' ',true);
		$Sender->EventArguments['Tags'] = $Alltags;			//and add them to the list of tags on the form
		//die(0);
	}
	/////////////////////////////////////////////////////////
	// Display data for debugging
	private function Showdata($Data, $Message, $Find, $Nest=0, $BR='<br>', $Echo = true) {
		//var_dump($Data);
		$Line = "<br>".str_repeat(".",$Nest*4)."<B>(".($Nest).") ".$Message."</B>";
		if ($Echo) echo $Line;
		else Gdn::controller()->informMessage($Line);
		
		$Nest +=1;
		if ($Nest > 20) {
			echo wrap('****Nesting Truncated****','h1');
			return;	
		}
		if ($Message == 'DUMP') echo '<br> Type:'.gettype($Data).'<br>';//var_dump($Data);
		if  (is_object($Data) || is_array($Data)) {
			echo ' '.gettype($Data).' ';
			if (is_array($Data) && !count($Data)) echo '....Debug:'.$Data[0];
			foreach ($Data as $key => $value) {
				if  (is_object($value)) {
					$this->Showdata($value,' '.gettype($value).'('.count($value).'):'.$key.' value:','',$Nest,'<n>');
				} elseif (is_array($value)) {
					$this->Showdata($value,' '.gettype($value).'('.count($value).'):['.$key.']: value:','',$Nest,'<n>');
				} elseif (is_bool($value)) {
					$this->Showdata($value,' '.gettype($value).':'.$key.' value[]:','',$Nest,'<n>');
				} elseif (is_string($value)) {
					$this->Showdata($value,' '.gettype($value).':'.$key.' value:','',$Nest,'<n>');
				} else {
					$this->Showdata($value,'_'.gettype($value).':'.$key.'   value:','',$Nest,'<n>');
				}
			}
		} else {
			if ($Echo) 
				echo wrap('"'.$Data.'"','b');
			else Gdn::controller()->informMessage($Data,'DoNotDismiss');
			//var_dump($Data);
		}
	}
	/////////////////////////////////////////////////////////

}
