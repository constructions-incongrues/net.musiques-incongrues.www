<?php
/*
Extension Name: Discussion Overview
Extension Url: http://www.krijtenberg.nl
Description: Displays last discussions for each category in an overview kinda way :P
Version: 1.2
Author: Maurice Krijtenberg
Author Url: http://www.krijtenberg.nl

Changes

Version 1.1 - 08.10.2006, Jazzman
- Add check for Menu control, people doesn't have it which resulted in errors or a blank page
- Added "Show All" link at the bottom of each category
- Displaying Discussion Overview in a seperate tab is now a user preference

Version 1.2 - 25.01.2007, Jazzman
- Added fix for "breaks RSS and Atom extensions" bug (thanks to pascalvanhecke)
*/

$Context->Dictionary['DiscussionOverview'] = 'Discussion Overview';
$Context->Dictionary['DiscussionOverviewAsTab'] = 'Display Discussion Overview in a seperate tab';
$Context->Configuration['DISCUSSIONOVERVIEW_SHOW_AS_TAB'] = '0';
$Context->Configuration['DISCUSSIONOVERVIEW_DISCUSSIONS'] = '10';
$Context->Configuration['TAB_POSITION_DISCUSSIONOVERVIEW'] = '1';

if ($Context->Session->User->Preference('DiscussionOverviewAsTab') && isset($Menu))  {
	$Menu->AddTab($Context->GetDefinition('DiscussionOverview'), 'DiscussionOverview', GetUrl($Configuration, 'extension.php', '', '', '', '',  'PostBackAction=DiscussionOverview'), '', $Configuration['TAB_POSITION_DISCUSSIONOVERVIEW']);
}

if (($Context->SelfUrl == 'index.php' || $Context->SelfUrl == 'extension.php') && !ForceIncomingString('Feed', '')) { 

	class DiscussionOverview extends Control {
		
		function DiscussionOverview(&$Context) {
			$this->Name = 'DiscussionOverview';
			$this->Control($Context);
			$this->CategoryData = $this->GetNonBlockedCategories(1);
			$this->CallDelegate('Constructor');
		}

		function GetNonBlockedCategories($IncludeCount = '0', $OrderByPreference = '0', $ForceRoleBlock = '1') {
			$CategoryManager = $this->Context->ObjectFactory->NewContextObject($this->Context, 'CategoryManager');
			$OrderByPreference = ForceBool($OrderByPreference, 0);
			$s = $CategoryManager->GetCategoryBuilder($IncludeCount, $ForceRoleBlock);
			if ($this->Context->Session->UserID > 0) {
				$s->AddJoin('CategoryBlock', 'cb', 'CategoryID', 'c', 'CategoryID', 'left join', ' and cb.'.$this->Context->DatabaseColumns['CategoryBlock']['UserID'].' = '.$this->Context->Session->UserID);
				// This coalesce seems to be slowing things down
				// $s->AddWhere('coalesce(cb.Blocked,0)', 1, '<>');			
				$s->AddWhere('cb', 'Blocked', '', '0', '=', 'and', '', 1, 1);
				$s->AddWhere('cb', 'Blocked', '', '0', '=', 'or', '', 0, 0);
				$s->AddWhere('cb', 'Blocked', '', 'null', 'is', 'or', '', 0, 0);
				$s->EndWhereGroup();
			}
			if ($OrderByPreference && $this->Context->Session->UserID > 0) {
				// Order by the user's preference (unblocked categories first)
				$s->AddOrderBy('Blocked', 'b', 'asc');
			}

			$s->AddOrderBy('Priority', 'c', 'asc');
			return $this->Context->Database->Select($s, $this->Name, 'GetCategories', 'An error occurred while retrieving categories.');
		}
		
		function Render() {
			$this->CallDelegate('PreRender');

			$DiscussionOverview = '';
			
			$Category = $this->Context->ObjectFactory->NewObject($this->Context, 'Category');
			while ($Row = $this->Context->Database->GetRow($this->CategoryData)) {
				$Category->Clear();
				$Category->GetPropertiesFromDataSet($Row);
				$Category->FormatPropertiesForDisplay();
				$DiscussionOverview .= '
				<ol id="Categories">
					<li id="Category_'.$Category->CategoryID.'" class="Category'.($Category->Blocked?' BlockedCategory':' UnblockedCategory').' Category_'.$Category->CategoryID.'">
					<ul>
					<li class="CategoryName">
						<span>'.$this->Context->GetDefinition('Category').'</span> <a href="'.GetUrl($this->Context->Configuration, 'index.php', '', 'CategoryID', $Category->CategoryID).'">'.$Category->Name.'</a>
					</li>
					<li class="CategoryDescription">
						<span>'.$this->Context->GetDefinition('CategoryDescription').'</span> '.$Category->Description.'
					</li>
					<li class="CategoryDiscussionCount">
						<span>'.$this->Context->GetDefinition('Discussions').'</span> '.$Category->DiscussionCount.'
					</li>';
				if ($this->Context->Session->UserID > 0) {
					$DiscussionOverview .= '
					<li class="CategoryOptions">
						<span>'.$this->Context->GetDefinition('Options').'</span> ';
					if ($Category->Blocked) {
						$DiscussionOverview .= '<a id="BlockCategory'.$Category->CategoryID.'" onclick="ToggleCategoryBlock('."'".$this->Context->Configuration['WEB_ROOT']."ajax/blockcategory.php', ".$Category->CategoryID.", 0, 'BlockCategory".$Category->CategoryID."');\">".$this->Context->GetDefinition('UnblockCategory').'</a>';
					} else {
						$DiscussionOverview .= '<a id="BlockCategory'.$Category->CategoryID.'" onclick="ToggleCategoryBlock('."'".$this->Context->Configuration['WEB_ROOT']."ajax/blockcategory.php', ".$Category->CategoryID.", 1, 'BlockCategory".$Category->CategoryID."');\">".$this->Context->GetDefinition('BlockCategory').'</a>';
					}
					$DiscussionOverview .= '</li>
					';
				}
				$DiscussionOverview .= '
					</ul>
				</ol>
				<ol id="Discussions">';

				$DiscussionManager = $this->Context->ObjectFactory->NewContextObject($this->Context, 'DiscussionManager');
				$DiscussionData = $DiscussionManager->GetDiscussionList($this->Context->Configuration['DISCUSSIONOVERVIEW_DISCUSSIONS'], 1, $Category->CategoryID);

				$Discussion = $this->Context->ObjectFactory->NewContextObject($this->Context, 'Discussion');
				$FirstRow = 1;
				$CurrentUserJumpToLastCommentPref = $this->Context->Session->User->Preference('JumpToLastReadComment');
				$DiscussionList = '';
				$ThemeFilePath = ThemeFilePath($this->Context->Configuration, 'discussion.php');
				$Alternate = 0;
				while ($Row = $this->Context->Database->GetRow($DiscussionData)) {
					$Discussion->Clear();
					$Discussion->GetPropertiesFromDataSet($Row, $this->Context->Configuration);
					$Discussion->FormatPropertiesForDisplay();
					
					// Prefix the discussion name with the whispered-to username if this is a whisper
					if ($Discussion->WhisperUserID > 0) {
						$Discussion->Name = @$Discussion->WhisperUsername.': '.$Discussion->Name;
					}

					// Discussion search results are identical to regular discussion listings, so include the discussion search results template here.
					include($ThemeFilePath);

					$FirstRow = 0;
					$Alternate = FlipBool($Alternate);
				}
				$DiscussionOverview .= $DiscussionList .'
				</ol>
				<div class="ShowAllLink">
					<a href="'.GetUrl($this->Context->Configuration, 'index.php', '', 'CategoryID', $Category->CategoryID).'">'.$this->Context->GetDefinition("ShowAll").'</a>
				</div>
				';
			}
			echo '
				<div class="ContentInfo Top">
					<h1>'.$this->Context->PageTitle.'</h1>
				</div>
				<div id="ContentBody" class="DiscussionOverview">
					'.$DiscussionOverview.'
				</div>';
			$this->CallDelegate('PostRender');
		}
	}


	// Replace default discussion grid with discussion overview control
	if (ForceIncomingInt('CategoryID', 0) == 0 && 
		ForceIncomingInt('page', 0) == 0 && 
		ForceIncomingString('View', '') == '' && 
		!$Context->Session->User->Preference('DiscussionOverviewAsTab')) {
		
		$Head->AddStyleSheet('extensions/DiscussionOverview/style.css');
		$Context->PageTitle = $Context->Dictionary['DiscussionOverview'];
		$Context->ObjectFactory->SetReference('DiscussionGrid', 'DiscussionOverview');
	}

	if ($Context->SelfUrl == 'extension.php' && 
		$Context->Session->User->Preference('DiscussionOverviewAsTab') && 
		ForceIncomingString('PostBackAction', '') == 'DiscussionOverview') {
		
		$Head->AddStyleSheet('extensions/DiscussionOverview/style.css');
		$Head->BodyId = 'DiscussionsPage';
		$Menu->CurrentTab = 'DiscussionOverview';
		$Context->PageTitle = $Context->Dictionary['DiscussionOverview'];

		$DiscussionOverview = $Context->ObjectFactory->NewContextObject($Context, 'DiscussionOverview');
		$Page->AddRenderControl($DiscussionOverview, $Configuration['CONTROL_POSITION_BODY_ITEM']);
	}
}

if ($Context->SelfUrl == 'account.php') {
	$Context->AddToDelegate('PreferencesForm', 'PreRender', 'DiscussionOverview_PreferencesForm');
	function DiscussionOverview_PreferencesForm(&$PreferencesForm) {
		$PreferencesForm->AddPreference($PreferencesForm->Context->GetDefinition('DiscussionOverview'), 'DiscussionOverviewAsTab', 'DiscussionOverviewAsTab');
	}
}
?>
