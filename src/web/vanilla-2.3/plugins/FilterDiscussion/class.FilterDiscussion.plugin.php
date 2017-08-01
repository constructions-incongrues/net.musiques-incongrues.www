<?php if(!defined('APPLICATION')) exit();
$PluginInfo['FilterDiscussion'] = array(
    'Name' => 'FilterDiscussion',
	'Description' => "FilterDiscussion - A plugin that dynamically creates custom filtered discussion list based on URL parameters.",
    'Version' => '1.3',
    'RequiredApplications' => array('Vanilla' => '2.1.13'),       		/*This is what I tested it on...*/
    'RequiredTheme' => FALSE,
	'SettingsPermission' => 'Garden.Settings.Manage',
	'SettingsUrl' => '/dashboard/plugin/FilterDiscussion', 				/*'/settings/FilterDiscussion',*/
	'RegisterPermissions' => array('Plugins.FilterDiscussion.View'),  	/*Permission to filter on fields*/
	'Author' => 'Ron Brahmson based on FilterbyPrefix',
    'MobileFriendly' => TRUE,
    'HasLocale' => TRUE,
    'License' => 'GPLv3'
);
/*
The plugin filters the discussion list based on the value of the fields in the discussions database. The filtered view is flexible as the filters are specified via URL parameters using flexible syntax. For example, filtering by the CategoryID field is equivalent to display discussions for a particular category. A combination of fields is allowed and the list of supported fields is specified in the administration dashboard. 

Combination field names can be specified to create refined filters.  If the Discussion table in the database has been extended with additional fields, then these can be used as well. For example, the PrefixDiscussion plugin adds a field called "Prefix", so it is possible to filter with the "Prefix=" parameter. Example: /discussions/filterdiscussion&Prefix=EQ:Video&InsertUserID=EQ:13&CategoryID=EQ:6

The title of the resulting filtered screen can also be specified via the &!msg= parameter. Some html tags can be specified (at your risk).
Example: /discussions/filterdiscussion&Prefix=EQ:Video&!msg=<span%20style="color:white;background-color:blue">Highlighted%20Videos</span>

Named filters can be globally saved via the plugin settings through the admin dashboard. Saved filters provide the ability to apply filters
without exposing to the end user the actual parameters being used.   
For example, assume a saved filter named "AlertedVideos" is defined as "&Prefix=EQ:Video&Alert=NN&!msg=Videos of interest"
To invoke that view the following url is needed "/discussions/filterdiscussion&!filter=AlertedVideos
(The above example assumed that both PrefixDiscussion and DicsussionAlert plugins are installed 
	-- they add the Prefix and Alert fields to the discussion table).


There are three types of use cases for this plugin:
1. For administrators  - to check on content without having to go to the SQL database
2. For developers - to link to filtered views from web pages (e.g. The userid field in a discussion can link to a filtered view that shows discussions by that userid) 
3. For administrators - to add menu options for specialized views 

Special permission must be set to allow users to use the plugin.  After enabling the plugin see "Roles & Permission" in the admin dashboard.

Note: You may have other plugins that use URL parameters and you will need to define them in the "ignore list" on 
the dashboard setting for FilterDiscussion plugin so that it will ignore them and won't throw an error for undefined parameter.   This ignore list has been added in Version 1.2.

Change log:
Version 1.2 - Added ignore list (ignored url parameters that may be used by other plugins/applications)
Version 1.3 - Added saved filters (to use a named parameter to invoke multiple filters while hiding the filters from the user)

No warranty whatsoever is implied by releasing this plugin to the Vanilla community.
*/

class FilterDiscussion extends Gdn_Plugin {

  // Indicates whether or not we are in the Filtered view
  private $CustomView = FALSE;
  ///////////////////////////////////////////////
  // Pagination support
  public function DiscussionsController_FilterDiscussion_Create($Sender, $Args = array()) {
	$Page = '{Page}';
	if (!CheckPermission('Plugins.FilterDiscussion.View')) {	
		$this->SevereMessage(t('Not Authorized'));
		Gdn::Controller()->Title(t('Recent Discussions'));
		return;
	}
	$this->CustomView = TRUE;
    $Sender->View = 'Index';
	$Parameters = '';
	foreach ($_GET as $key => $value) {		
		if ($key == "!msg") {			
			Gdn::Controller()->Title(t($value));
		}
		$Parameters=$Parameters."&".trim($key)."=".trim($value);
	}
	$Sender->SetData('_PagerUrl', 'discussions/FilterDiscussion/'.$Page.$Parameters);
    $Sender->Index(GetValue(0, $Args, 'p1'));
  }
  ///////////////////////////////////////////////
  // Set the count to the cache value. This will use a few more pages unless caching is enabled.
  public function DiscussionsController_Render_Before($Sender) {
    if($this->CustomView) {
       $Sender->SetData('CountDiscussions', Gdn::Cache()->Get('FilterDiscussion-Count'));
    }
  }
  ///////////////////////////////////////////////
  // Main processing of the custom view
	public function DiscussionModel_BeforeGet_Handler($Sender) {
		$Debug = false;
		if($this->CustomView != TRUE)  return;
		if (!CheckPermission('Plugins.FilterDiscussion.View')) {		//Validate that the user has permission to filtered views
			$Title = t('Not allowed.  Unrecognized parameters:');       //If not, don't accept the parameters
			foreach ($_GET as $key => $value) {
				$Title=$Title.$key."=".$value." ";
			}
			Gdn::Controller()->Title($Title);
			return;														// and return without any custom view
		}
		// Validate tha passed column named are prelisted in the administrator configuration screen 
		if	(!c('Plugins.FilterDiscussion.Fieldnames')) {  				//Field names defined??
			$this->SevereMessage(t('List of acceptable fields not defined. The admin needs to set them first'));
			return;
		}
		$ValidFields = explode(',',trim(Gdn::config('Plugins.FilterDiscussion.Fieldnames',',')));
		$Ignoreparms = explode(',',trim(Gdn::config('Plugins.FilterDiscussion.Ignoreparms',' ')));
		//
		/*---------------------
		Search arguments are specified as &column=operand:value where:
			column - the column name (e.g. CategoryID)
			operand - the search type.  One of: 
				EQ - Equal, NE - Not Equal, NL - NULL, NN - Not NULL 	//Future development: LK - Like search, NK - not like search,

			Examples:  	&Prefix=NE:Help  					- Search for entries that do not have "Help" in the Prefix column
						&Prefix=NL,&Alert=NN,&InsertID=12	- Search for entries without a prefix (NULL), with an Alert (not NULL) created by userid 12
		----------------------*/
		if (empty($_GET)) {
			$this->HelpMessage(t('Missing Parameters'),$ValidFields);
			return;
		}
		$Title = '';
		$Titlemsg = '';
		$Urlparms = $_GET;
		$Gotsavedfilter = false;
		//$Likeop="Like";										//Future Development
		//
		while ($entry = each ($Urlparms)) {
			$key = $entry[0];
			$value = $entry[1];
			if ($Debug) echo '<BR> P0.key:'.$key.' Value='.$value;
			if ($key == "!debug") {								// For debugging
				if ($value == 'Y') {$Debug = true;} else {$Debug = false;}
				next($urlparms);
				echo "<br> Ignoreparms:".$Ignoreparms."<br>";
				$this->Showdata($Ignoreparms,'Ignoreparms','');
				continue;
			}
			elseif ($key == "!filter") {							//Calling a saved filter name?	
				if ($Debug) {
					echo '<BR> Filter parm 1.key:'.$key.' Value='.$value.' Valuearray:<br>';
					var_dump($Valuearray);
				}	
				if (trim($value) == trim(Gdn::config('Plugins.FilterDiscussion.SavedName1',' '))) {
					$Addfilter = trim(Gdn::config('Plugins.FilterDiscussion.SavedFilter1',' '));
				} elseif (trim($value) == trim(Gdn::config('Plugins.FilterDiscussion.SavedName2',' '))) {
					$Addfilter = trim(Gdn::config('Plugins.FilterDiscussion.SavedFilter2',' '));
				} elseif (trim($value) == trim(Gdn::config('Plugins.FilterDiscussion.SavedName3',' '))) {
					$Addfilter = trim(Gdn::config('Plugins.FilterDiscussion.SavedFilter3',' '));
				} elseif (trim($value) == trim(Gdn::config('Plugins.FilterDiscussion.SavedName4',' '))) {
					$Addfilter = trim(Gdn::config('Plugins.FilterDiscussion.SavedFilter4',' '));
				} elseif (trim($value) == trim(Gdn::config('Plugins.FilterDiscussion.SavedName5',' '))) {
					$Addfilter = trim(Gdn::config('Plugins.FilterDiscussion.SavedFilter5',' '));
				} elseif (trim($value) == trim(Gdn::config('Plugins.FilterDiscussion.SavedName6',' '))) {
					$Addfilter = trim(Gdn::config('Plugins.FilterDiscussion.SavedFilter6',' '));
				} elseif (trim($value) == trim(Gdn::config('Plugins.FilterDiscussion.SavedName7',' '))) {
					$Addfilter = trim(Gdn::config('Plugins.FilterDiscussion.SavedFilter7',' '));
				} elseif (trim($value) == trim(Gdn::config('Plugins.FilterDiscussion.SavedName8',' '))) {
					$Addfilter = trim(Gdn::config('Plugins.FilterDiscussion.SavedFilter8',' '));
				}
				if ($Debug) {
					echo '<BR> Filter parm 2.key:'.$key.' Addfilter='.$Addfilter.' Urlparms:<br>';
					var_dump($Urlparms);
					echo '<br> SavedName1:'.Gdn::config('Plugins.FilterDiscussion.SavedName1','What???');
					echo '<br> SavedFilter1:'.Gdn::config('Plugins.FilterDiscussion.SavedFilter1','What???');
				}
				if ($Addfilter == "") {
					$this->HelpMessage(t('Named Filter "').$value. '" '.t('Not defined'));  //Throw error as well as some help
					return;
				} elseif ($Gotsavedfilter) {
					$this->HelpMessage(t('Only one named filter is allowed. "').$value. '" '.t('Not allowed'));  //Throw error as well as some help
					return;
				} else {
					$Gotsavedfilter = true;
					parse_str($Addfilter,$Urlparms);
					reset($urlparms);
					if ($Debug) {echo "<br> assigning filter. New Urlparms=";var_dump($Urlparms);}
					//$Urlparms = $Addfilter;
				}
				if ($Debug) {
					echo '<BR> Filter parm 3.key:'.$key.' value:'.$value.' Addfilter='.$Addfilter.' Urlparms:<br>';
					var_dump($Urlparms);
				}
				continue;
			}
			elseif (in_array($key, $Ignoreparms)) {					//Defined as ignored parameter?
				if ($Debug) $this->Showdata($Ignoreparms,'Ignored parameter','');
				next($urlparms);
				continue;
			}
			// Special parameter to create the title for the result screen (may include some HTML)
			// e.g. discussions/filterdiscussion&Alert=NN&!msg=<span%20style="color:white;background-color:blue">Alerts</span>
			elseif ($key == "!msg") {				
				$Titlemsg = $value;
				next($urlparms);
				continue;
			} /*elseif ($key == "!likeop") {				//Future development
				$Likeop = $value;
				next($urlparms);
				continue;
			}*/
			elseif (!in_array($key, $ValidFields)) {					//Not in the list?
				$this->HelpMessage($key. " ".t('Not allowed'),$ValidFields);  //THrow error as well as some help
				return;
			}
			$Searchcolumn = 'd.'.$key;									//For now just the discussion table ;-)
			$Valuearray = explode(":",$value);							
			if ($Debug) {
				echo '<BR> P1.key:'.$key.' Value='.$value.' Valuearray:<br>';
				var_dump($Valuearray);
			}
			$action = $Valuearray[0];									//First parameter is the operand
			$searchvalue= $Valuearray[1];								//Second (if any) is the compare value
			if ($Debug) echo '<BR> P2.action:'.$action.' searchvalue='.$searchvalue.'<br>';
			switch  ($action) {											//Build the SQL and title based on the operation
				case "NL":
					$Sender->SQL->Where('d.'.$key,NULL);			
					$Title=$Title.$key." is NULL ";
					break;
				case "NN":
					$Sender->SQL->Where($Searchcolumn.' >', false);		//Not NULL  
					$Title=$Title.$key." > 0 ";
					break;
				case "EQ":
					$Sender->SQL->Where($Searchcolumn,$searchvalue);
					$Title=$Title.$key." = ".$searchvalue."  ";
					if ($Debug) echo '<BR> EQ.  Title='.$Title.'<br>';
					break;
				case "NE":
					$Sender->SQL->Where($Searchcolumn.' <> ',$searchvalue);	 
					$Title=$Title.$key." <> ".$searchvalue."  ";
					break;
				case "GT":
					$Sender->SQL->Where($Searchcolumn.' > ', $searchvalue); 		
					$Title=$Title.$key." > ".$searchvalue."  ";
					break;
				case "LT":
					$Sender->SQL->Where($Searchcolumn.' < ', $searchvalue); 		 
					$Title=$Title.$key." < ".$searchvalue."  ";
					break;
				case "help":
					$Title = "Valid operators: EQ, NE, LT, GT, NL, NN"; //, LK, NK";
					echo $Title;
					Gdn::Controller()->Title($Title);
					return;
			/*	case "LK":															//Future development 
					$Sender->SQL->Where($Searchcolumn.' LIKE ', $searchvalue); 		//LIKE search
					break;
				case "NK":	
					if ($Debug) $this->Showdata($Likeop,'Before setting Where for like','');
					$Sender->SQL->Where($Searchcolumn." ".$Likeop , $searchvalue); 		// NOT LIKE search
					break;
				*/
				default:
					$this->HelpMessage(t("Invalid operator:").$action,$ValidFields);
					return;
				break;
			}
		}
	/*	if ($Debug) {
			echo "<BR>....SQL:<BR>";
			$MySQL=$Sender->SQL;
			$Where=$Sender->SQL->Where;
			var_dump($Where);
			//$this->Showdata($MySQL,'Before Select setting','');
			var_dump($MySQL);
		}
	*/
		if ($Titlemsg) $Title = $Titlemsg;
		Gdn::Controller()->Title($Title);
		//That's all folks!
	}
	///////////////////////////////////////////////
	// Display data for debugging
	public function Showdata($Data, $Message, $Find, $Nest=0, $BR='<br>') {
		//var_dump($Data);
		echo "<br>".str_repeat(".",$Nest*4)."<B>(".($Nest).") ".$Message."<n>";
		$Nest +=1;
		if ($Nest > 10) return;	
		if  (is_object($Data) || is_array($Data)) {
			foreach ($Data as $key => $value) {
				if  (is_object($value)) {
					$this->Showdata($value,'oooo '.gettype($value).'=>key:'.$key.' value =>','',$Nest,'<n>');
				} elseif (is_array($value)) {
					$this->Showdata($value,'aaaa '.gettype($value).'=>key:'.$key.' value[]:','',$Nest,'<n>');
				} else {
					$this->Showdata($value,'ssss '.gettype($value).'=>key:'.$key.'   value:','',$Nest,'<n>');
				}
			}
		} else {
			var_dump($Data);
		}
	}
	///////////////////////////////////////////////
   	// Display help messages
	public function HelpMessage($Message,$Fields) {
		if (!$Message == "") echo "<P><H1><B>FilterDiscussion Plugin Message:".$Message."<N></H1></P>";
		echo "<BR>Syntax: &" . "fieldname=operator:value,...(can specify several field name combinations)";
		echo "<BR>Defined fields: " . implode(", ", $Fields);
		//$this->Showdata($Fields,'Defined fields:','');
		echo "<BR>Valid operators: EQ, NE, GT, LT, NL, NN ";//, LK, NK";
		echo "<BR>Special parameters: (1) &!msg=  followed by the text of the title of the filtered view. ";
		echo "<BR>Special parameters: (2) &!filter=  followed by the name of a saved filter (set by the administrator)";
		echo "<BR>Example: discussions/FilterDiscussion/&InsertUserID=EQ:6&CategoryID=GT:9&!msg=This is a filtered list";
		echo "<BR>Example: discussions/FilterDiscussion/&!filter=Imagefilter";
	}
  	///////////////////////////////////////////////
	// Plugin Setup 
	public function Setup() {
		// Initialize plugin defaults
		if (!c('Plugins.FilterDiscussion.Fieldnames')) {
			saveToConfig('Plugins.FilterDiscussion.Fieldnames', 
			'DiscussionID,CategoryID,InsertUserID,UpdateUserID,Name,Body,FirstCommentID,LastCommentID');  //Set few default fields
        }
		if (!c('Plugins.FilterDiscussion.Ignoreparms')) {
			saveToConfig('Plugins.FilterDiscussion.Ignoreparms', 
			'');  //Set few default fields
        }
	}
	///////////////////////////////////////////////
	// Dashboard settings
	public function PluginController_FilterDiscussion_Create($Sender) {
	//public function settingsController_FilterDiscussion_Create($Sender) {
		$Sender->Title('FilterDiscussion '.t('Settings'));
        $Sender->AddSideMenu('plugin/FIlterDiscussion');
        $Sender->Permission('Garden.Settings.Manage');
        $Sender->Form = new Gdn_Form();
        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->SetField(array(
            'Plugins.FilterDiscussion.Fieldnames',
			'Plugins.FilterDiscussion.Ignoreparms',
			'Plugins.FilterDiscussion.SavedName1',
            'Plugins.FilterDiscussion.SavedFilter1',
			'Plugins.FilterDiscussion.SavedName2',
			'Plugins.FilterDiscussion.SavedFilter2',
			'Plugins.FilterDiscussion.SavedName3',
			'Plugins.FilterDiscussion.SavedFilter3',
			'Plugins.FilterDiscussion.SavedName4',
			'Plugins.FilterDiscussion.SavedFilter4',
			'Plugins.FilterDiscussion.SavedName5',
			'Plugins.FilterDiscussion.SavedFilter5',
			'Plugins.FilterDiscussion.SavedName6',
			'Plugins.FilterDiscussion.SavedFilter6',
			'Plugins.FilterDiscussion.SavedName7',
			'Plugins.FilterDiscussion.SavedFilter7',
			'Plugins.FilterDiscussion.SavedName8',
			'Plugins.FilterDiscussion.SavedFilter8',
        ));
        $Sender->Form->SetModel($ConfigurationModel);


        if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
            $Sender->Form->SetData($ConfigurationModel->Data);
        } else {
            $Data = $Sender->Form->FormValues();

            if ($Sender->Form->Save() !== FALSE)
                $Sender->StatusMessage = T("Your settings have been saved.");
        }

        $Sender->Render($this->GetView('filterdiscussionsettings.php'));
	}
	///////////////////////////////////////////////
        /*-------------------------------------------------------------------------------
		Below is a list of the Discussion table fields with several plugins enabled (and those may have added fields that won't necessarily
		exist in every installation):
			DiscussionID,Type,ForeignID,CategoryID,InsertUserID,UpdateUserID,FirstCommentID,LastCommentID,Name,Body,Format,Tags,CountComments,CountBookmarks, CountViews,Closed,Announce,Sink,DateInserted,DateUpdated,InsertIPAddress,UpdateIPAddress,DateLastComment,LastCommentUserID,Score,Attributes, RegardingID,Scheduled,ScheduleTime,Discussants,QnA,DateAccepted,DateOfAnswer,EventCalendarDate ,DiscussionEventDate,Resolved,DateResolved,ResolvedUserID 
		-------------------------------------------------------------------------------*/
    
	///////////////////////////////////////////////
   	// Throw with a severe message
	public function SevereMessage($Message) {
		echo "<P><H1><B>FilterDiscussion Plugin Message:".$Message."<N></H1></P>";
		//$Sender->InformMessage($Message);
		//throw new Gdn_UserException($Message);
	}
	///////////////////////////////////////////////
}
