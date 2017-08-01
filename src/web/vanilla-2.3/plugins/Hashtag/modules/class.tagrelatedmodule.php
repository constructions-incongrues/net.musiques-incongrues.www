<?php if (!defined('APPLICATION')) exit();
//  Module to add related discussions to the side panel.
class TagRelatedModule extends Gdn_Module {

 protected $ConversationString;

/////////////////////////////////////
  public function toString ($Sender,$Args) {
	global $ConversationString ;
	//$Msg = __FUNCTION__.' '.__LINE__.' Called by: ' . debug_backtrace()[1]['function'].debug_backtrace()[0]['line'].' ---> '. 	debug_backtrace()[0]['function'];
	//return $Msg;
	return $ConversationString;
  }
/////////////////////////////////////
  public function GetRelated($DiscussionID, $Limit = 10, $Debug = false) {
	global $ConversationString ;
	//This function will return the discussions that have the same set of tags as the current discussion
	//The SQL to do so is a bit tricky, having the general form of:
	// SELECT * FROM TagDiscussion WHERE `TagID` IN (--list-of-tag-numbers-to-search-) 
	// GROUP BY `DiscussionID` 
	// HAVING COUNT(*) = number of items in the --list-of-tag-numbers-to-search-
	//  Example:
	//SELECT * FROM TagDiscussion WHERE `TagID` IN (2,4,5) GROUP BY `DiscussionID` HAVING COUNT(3)
	//Note: The code below further refines the SQL query.
	if ($Debug) {
			$Msg = __FUNCTION__.' '.__LINE__.' Called by: ' . debug_backtrace()[1]['function'].debug_backtrace()[0]['line'].' ---> '. debug_backtrace()[0]['function'];
			//Gdn::controller()->informMessage($Msg);
			echo Wrap($Msg,'br');
	}
	//
	$ConversationString = '';
	$DiscussionModel = new DiscussionModel();
	$Discussion = $DiscussionModel->getID($DiscussionID);
	if (!$Discussion) {
		$ConversationString = 'no discussion';
		return;
	}
	//First get the tag IDs for the current discussion
	//
	$Sqlfields = 't.TagID, t.Name, t.FullName, td.DiscussionID, td.TagID';
	if ($Debug) {
		$Sqlfields = 't.TagID, t.Name, t.FullName, t.CountDiscussions, t.Dateinserted tDateinserted, td.Dateinserted tdDateinserted, td.DiscussionID, td.TagID';
		$this->Showdata($DiscussionID,__LINE__.'---DiscussionID---','',0,' ',true);
		$this->Showdata($Discussion->Name,__LINE__.'---Name---','',0,' ',true);
	}	
	//
	$Hashtag = '#';
	$TagSql = clone Gdn::sql();	//Don't interfere with any other sql process
	$TagSql->Reset();			//Clean slate
	$Taglist = $TagSql		//Get expanded tag info for this discussion
		->select($Sqlfields)
		->from('TagDiscussion td')
		->join('Tag t', 't.TagID = td.TagID')
		->where('td.discussionID', $DiscussionID)
		->get()->ResultArray();
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
	//$Alltags = rtrim($Taglist.','.$Tags,', ');
	if ($Debug) $this->Showdata($ResultArray,__LINE__.'---ResultArray---','',0,' ',true);
	//
	// Scan the current tags for this discussion
	$Intaglist = array();
	$Tagcount = 0;
	$Tagnamelist = '';
	foreach ($Taglist as $Outerkey => $Outervalue) {
		//echo '<br>'.__LINE__.' Outerkey:'.$Outerkey.' Outervalue:'.$Outervalue;
		//if ($Debug) echo '<br>'.__LINE__.' TagID:'.$Outervalue['TagID'].' FullName:'.$Outervalue['FullName'].' Name:'.$Outervalue['Name'];
		$Tagnamelist = $Tagnamelist . ' ' . $Outervalue['FullName'];
		$Tagcount = $Tagcount+1;
		$Intaglist[$Tagcount] = $Outervalue['TagID']; 
	}
	//
	if ($Debug) echo '<br>'.__LINE__.' Key:'.$Key.' Value:'.$Value.' Tagcount:'.$Tagcount.' Intaglist:';
	if ($Debug) var_dump($Intaglist);
	////////////////////
	if ($Tagcount == 0) return;
	//
	$TagSql->Reset();			//Clean slate
	//
	$Uselimit = $Limit + 1;
	$Discussionlist = $TagSql
		->select('td.TagID, td.DiscussionID, t.FullName')
		->from('TagDiscussion td')
		->join('Tag t', 't.TagID = td.TagID')
		->wherein('td.TagID', $Intaglist)
		->where('td.DiscussionID <>',$DiscussionID)
		->where('SUBSTR(t.FullName,1,1)', $Hashtag)
		->groupBy('td.DiscussionID')
		->having('count(*) =', $Tagcount,true,false)
		->orderBy('td.DiscussionID', 'desc')
		->limit($Uselimit)
		->get();//->getSelect();;
		
	//
	$Rowcount = count($Discussionlist);
	if ($Debug) echo '<br>'.__LINE__.' Rowcount:'.$Rowcount;
	if ($Debug) $this->Showdata($Discussionlist,__LINE__.'---Discussionlist---','',0,' ',true);
	$Panelhead = c('Plugins.Hashtag.Panelhead');
	if (trim($Panelhead) =='') $Panelhead = t('Similar Hashtag Set');
	if ($Rowcount == 0) {
		if (c('Plugins.Hashtag.HideEmptyPanel',true)) return;
		$ConversationString = wrap(panelHeading($Panelhead.' - '.t('None Found')),
		'DIV class="Box BoxCategories" Title="'.t('Nothing else has this set of hashtags:').$Tagnamelist.'"');
		return;
	}
	$More = '';
	if ($Rowcount >  $Limit) {
		$More = wrap(t('There are more').'...','li class=HashTagsMore ');
	}
	//
	//Get the cetegory ids the user is allowed to see 
	$Categories = CategoryModel::getByPermission();
	$Categories = array_column($Categories, 'Name', 'CategoryID');
	//$this->Showdata($Categories,__LINE__.'---Categories---','',0,' ',true);
	$Categorycount = 0;
	foreach ($Categories as $CategoryID  => $CategoryName) {
		//$this->Showdata($CategoryID,__LINE__.'---CategoryID---','',0,' ',true);
		//$this->Showdata($CategoryName,__LINE__.'---CategoryName---','',0,' ',true);
		$Categorycount = $Categorycount + 1;
		$Categorylist[$Categorycount] = $CategoryID;
	}
	//$this->Showdata($Categorylist,__LINE__.'---Categorylist---','',0,' ',true);
	$Listcount = 0;
	foreach($Discussionlist as $Entry){
		if ($Debug) $this->Showdata($Entry,__LINE__.'---Entry---','',0,' ',true);
		$DiscussionID = $Entry->DiscussionID;	
		$Discussion = $DiscussionModel->getID($DiscussionID);
		$CategoryID = $Discussion->CategoryID;
		if (in_array($CategoryID,$Categorylist)) {	//Support Vanilla permission model -verify user can see discussions in the category
			$Tag = $Entry->FullName;
			//if ($Debug) echo '<br>'.__LINE__.' ConversationString:'.$ConversationString; 
			$Anchor = wrap(Anchor(SliceString($Discussion->Name,40),'/discussion/'.$DiscussionID.'/?Tag='.$Tagnamelist,'RelatedHashtagItemLink '),'li class=Discussions ');
			$ConversationString =  ' ' . $ConversationString . ' ' . $Anchor;
			$Listcount = $Listcount + 1;
		//} else {
		//	echo '<br>'.__LINE__.'Not allowed- CategoryID:'.$CategoryID.' DiscussionID:'.$DiscussionID; 
		}
	}
	if (!$Listcount) {
		if (c('Plugins.Hashtag.HideEmptyPanel',true)) return;
		$ConversationString = wrap(panelHeading($Panelhead.' - '.t('None Found')),
			'DIV class="Box BoxCategories"  title="'.t('Nothing else has this set of hashtags:').$Tagnamelist.'"');
		return;
	}
	$ConversationString =  $ConversationString . ' ' . $More; 
	$ConversationString =	wrap(panelHeading($Panelhead).
							wrap($ConversationString,'ul class="PanelInfo PanelCategories" title="'.t('click to view discussion').'"'),
								'DIV class="Box BoxCategories"  title="'.t('Discussions with these hashtags:').$Tagnamelist.'"');
	//echo ($ConversationString);
	return false;		
  }
  /////////////////////////////////////////
  public function assetTarget() {
	return 'Panel';
  }
  ///////////////////////////////////////
	// Display data for debugging
	public function Showdata($Data, $Message, $Find, $Nest=0, $BR='<br>', $Echo = true) {
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
	///////////////////////////////////////////////
}
