<?php
/*
Extension Name: Page Manager
Extension Url: http://lussumo.com/addons
Description: Allows administrators to create/edit/delete and arrange the order of tabs and pages, in addition to assigning which roles can view them.
Version: 2.5.3
Author: SirNotAppearingOnThisForum
Author Url: N/A
*/

define('PAGE_LIST_LOC',		0); //location on side panel; set to 0 to disable list

/*
Sub-Array Construction:
	'tab' - Tab Name
	'page' - Page Name (value)
	'url' - Url (if custom page and url is empty, uses index.php?Page=[page name] offset 4
	'class' - Class (CSS)
	'html' - Page HTML, if applicable
	'custom' - if not set, tab is one of the system tabs
	'attribs' - extra attributes
	'roles' - users that can see the tab
	'hidden' - can be seen or not (added by jimw)

Will format $Menu->Tabs based off array
*/

if(!defined('PAGEMNG_FOLDER')) define('PAGEMNG_FOLDER', 'PageMng');

if(isset($Menu) || defined('PAGEMNG_ISAJAX'))
{
	$Context->Dictionary['PageMng_NoInputValue'] = 'You must enter a value for the Tab Name input.';
	$Context->Dictionary['PageMng_AlreadyCreated'] = 'A tab of that identifier has already been created.';
	$Context->Dictionary['PageMng_FileError'] = 'PAGEMANAGER ERROR: An error occured in attempting to save your tabs.  '.
		'Please check that your file permissions are correct and verify that PageManager::CustomPageFile (%s)'.
		' is a valid file name.';
	$Context->Dictionary['PageMng_NoRoles'] = 'PAGEMANAGER ERROR: No roles were found when attempting to build tab file.  If the current '.
		'page is not settings.php please go there now to properly create the file.  If so, then, well, something\'s wrong...';
	
	class PageManagement
	{
		var $CustomPageFile = 'CustomPages.php';
		var $Tabs = 0;
		var $CurPage = -1;
		var $Roles = array();
		var $SentPageID, $SentPageIndex = -1;
		var $TabBoxes = '';
		
		function PageManagement(&$Context)
		{
			if(!defined('PAGEMNG_ISAJAX')) $this->CustomPageFile = './extensions/'.PAGEMNG_FOLDER.'/'.$this->CustomPageFile;
			$this->Context = &$Context;
			$this->Tabs = $this->GetTabList();
			if(defined('PAGEMNG_ISAJAX')) return;
			
			$this->SentPageID = ForceIncomingString('PageID', '');
			if(!empty($this->SentPageID) && is_array($this->Tabs))
			{
				for($i = 0; $i < count($this->Tabs); $i++)
					if($this->Tabs[$i]['page'] == $this->SentPageID) {$this->SentPageIndex = $i; break;}
			}
			
			$Page = ForceIncomingString('Page', '');
			if(is_array($this->Tabs))
			{
				$f = 0;
				while(list($ky, $value) = each($this->Tabs))
				{
					//url?
					if($value['url']) $f = !strcmp('http://'.@$_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI'], $value['url']);
					
					//custom page?
					else $f = $value['page'] == $Page && $Context->SelfUrl == 'index.php';
					
					if($f) break;
				}
				
				reset($this->Tabs);
				if($f) $this->CurPage = $ky;
			}
		}
		
		function FormatArray($Arr)
		{
			$NewArr = array();
			while(list($Index, $Value) = each($Arr))
			{
				$NewElement = (is_numeric($Index) ? $Index : ('\''.str_replace("'", "\\'", $Index).'\'')).' => ';
				
				if(is_array($Value)) $NewElement .= $this->FormatArray($Value);
				else $NewElement .= (is_numeric($Value) ? $Value : ('\''.str_replace("'", "\\'", $Value).'\''));
				
				$NewArr[] = $NewElement;
			}
			$NewArr = 'array('.implode(', ', $NewArr).')';
			
			return $NewArr;
		}
		
		function SaveTabs()
		{
			$fd = @fopen($this->CustomPageFile, 'wb');
			
			$r = 1;
			if($fd)
			{
				$Buffer = '<?php return '.$this->FormatArray($this->Tabs).'; ?>';
				
				for($i = 0; !flock($fd, LOCK_EX) && $i <= 3; $i++) sleep(1);
				if($i <= 3)
				{
					if(@fwrite($fd, $Buffer) !== FALSE) $r = 0;
					flock($fd, LOCK_UN);
					fclose($fd);
				}
			}
			
			if($r)
			{
				//whoops...
				if(file_exists($this->CustomPageFile)) unlink($this->CustomPageFile);
				echo('<span style="font-weight: bold; color: #f00;">'.
					sprintf($this->Context->GetDefinition('PageMng_FileError'), $this->CustomPageFile).
					'</span>');
			}
			
			return;
		}
		
		function MyReadFile($path)
		{
			$fd = @fopen($path, 'rb');
			$buffer = '';
			
			if($fd)
			{
				$buffer = @fread($fd, filesize($path));
				fclose($fd);
			}
			
			return $buffer;
		}
		
		function GetTabList()
		{
			if(file_exists($this->CustomPageFile)) return include($this->CustomPageFile);
			else return 0;
		}
		
		function RecreateTabs()
		{
			global $Menu;
			
			$NewMenu = array();
			
			for($i = 0, $Cur = 10; $i < count($this->Tabs); $i++)
			{
				if((int)@$this->Tabs[$i]['hidden']) continue;
				
				if(!$this->Tabs[$i]['custom']) //so we won't display tabs that are normally disabled
				{
					$f = 0;
					while(list(, $Value) = each($Menu->Tabs))
					{
						if($Value['Value'] == $this->Tabs[$i]['page'])
						{
							$f = 1;
							break;
						}
					}
					reset($Menu->Tabs);
				}
				else $f = 1;
				if($f && in_array($this->Context->Session->User->RoleID, $this->Tabs[$i]['roles']))
				{
					$NewMenu[$Cur] = array(
						'Text' => $this->Tabs[$i]['custom'] ? $this->Tabs[$i]['tab'] : $this->Context->GetDefinition($this->Tabs[$i]['tab']), 
						'Value' => $this->Tabs[$i]['page'], 
						'Url' => $this->Tabs[$i]['url'] ? $this->Tabs[$i]['url'] : 
							($this->Context->Configuration['URL_BUILDING_METHOD'] == 'mod_rewrite' ?
							($this->Context->Configuration['BASE_URL'].'page/'.$this->Tabs[$i]['page']) :
							($this->Context->Configuration['BASE_URL'].'?Page='.$this->Tabs[$i]['page'])),
						'Attributes' => $this->Tabs[$i]['attribs']
					);
					$Cur += 10;
				}
			}
			$Menu->Tabs = $NewMenu;
			
			return;
		}
		
		function ReorganizeOrder()
		{
			$Order = ForceIncomingArray('PageID', array());
			$NewArr = array();
			
			$Order = array_unique($Order);
			$OrderCount = count($Order);
			if($OrderCount != count($this->Tabs)) return;
			
			for($i = 0; $i < $OrderCount; $i++)
			{
				if(!isset($this->Tabs[(int)$Order[$i] - 1])) return;
				$NewArr[] = $this->Tabs[(int)$Order[$i] - 1];
			}
			$this->Tabs = $NewArr;
			
			$this->SaveTabs();
		}
		
		function RemoveTab()
		{
			if(isset($this->Tabs[$this->SentPageIndex]))
			{
				unset($this->Tabs[$this->SentPageIndex]);
				$this->Tabs = array_values($this->Tabs);
				$this->SaveTabs();
			}
		}
		
		function SanitizePageValue($Value)
		{
			return str_replace(array(' ', "\t", "\r", "\n", '&'), array('_', '_', '', '', 'and'), strtolower($Value));
		}
		
		function CreateTab(&$PageSettings)
		{
			$Name = ForceIncomingString('Name', '');
			$URL = ForceIncomingString('URL', '');
			$HTML = ForceIncomingString('HTML', '');
			$Page = ForceIncomingString('Value', '');
			$Attribs = ForceIncomingString('Attribs', '');
			$RoleArr = ForceIncomingArray('AllowedRoles', array());
			$Hidden = isset($_POST['Hidden']);
			
			//so the user won't lose any info on error
			$PageSettings->TabName = htmlspecialchars($Name);
			$PageSettings->TabHTML = htmlspecialchars($HTML);
			$PageSettings->TabURL = htmlspecialchars($URL);
			$PageSettings->TabValue = htmlspecialchars($Page);
			$PageSettings->TabAttribs = htmlspecialchars($Attribs);
			$PageSettings->TabRoles = $RoleArr;
			$PageSettings->TabHidden = $Hidden;
			
			if(!strlen($Name))
			{
				$this->Context->WarningCollector->Add($this->Context->GetDefinition('PageMng_NoInputValue'));
				return 1;
			}
			
			if($Page == '') $Page = $this->SanitizePageValue($Name);
			for($i = $f = 0; $i < count($this->Tabs); $i++)
			{
				if($this->Tabs[$i]['page'] == $Page && $i != $this->SentPageIndex)
				{
					$f = 1;
					break;
				}
			}
			if($f)
			{
				$this->Context->WarningCollector->Add($this->Context->GetDefinition('PageMng_AlreadyCreated'));
				return 1;
			}
			
			$NewTab = array(
				'tab' => $Name, 
				'page' => $Page, 
				'url' => strlen($URL) ? $URL : 0, 
				'html' => strlen($URL) ? 0 : $HTML, 
				'custom' => isset($this->Tabs[$this->SentPageIndex]) ? $this->Tabs[$this->SentPageIndex]['custom'] : 1, 
				'attribs' => $Attribs, 
				'roles' => $RoleArr,
				'hidden' => $Hidden
			);
			
			if(isset($this->Tabs[$this->SentPageIndex])) $this->Tabs[$this->SentPageIndex] = $NewTab;
			else $this->Tabs[count($this->Tabs)] = $NewTab;
			
			$this->SaveTabs();
			
			return 0;
		}
		
		function AssignRoleTabs($Role, $NoRemove = 1, $ReplacementRole = 0)
		{
			$AllowedTabs = ForceIncomingArray('AllowedTabs', array());
			
			if($NoRemove)
			{
				for($i = 0; $i < count($this->Tabs); $i++)
				{
					if(in_array($i, $AllowedTabs))
					{
						if(!in_array($Role, $this->Tabs[$i]['roles'])) $this->Tabs[$i]['roles'][] = $Role;
					}
					else if(in_array($Role, $this->Tabs[$i]['roles']))
					{
						for($j = 0; $j < count($this->Tabs[$i]['roles']); $j++)
						{
							if($this->Tabs[$i]['roles'][$j] == $Role)
							{
								unset($this->Tabs[$i]['roles'][$j]);
								break;
							}
						}
						
						$this->Tabs[$i]['roles'] = array_values($this->Tabs[$i]['roles']);
					}
				}
			}
			else
			{
				for($i = 0; $i < count($this->Tabs); $i++)
				{
					if(in_array($Role, $this->Tabs[$i]['roles']))
					{
						for($j = 0; $j < count($this->Tabs[$i]['roles']); $j++)
						{
							if($this->Tabs[$i]['roles'][$j] == $Role)
							{
								$this->Tabs[$i]['roles'][$j] = $ReplacementRole;
								break;
							}
						}
					}
				}
			}
			
			$this->SaveTabs();
		}
		
		function ResyncSystemTabs()
		{
			global $Menu;
			
			$RoleArr = array();
			for($i = 0; $i < count($this->Roles); $i++) $RoleArr[] = $this->Roles[$i]['ID'];
			
			$TabLen = count($this->Tabs);
			for($i = 0; $i < $TabLen; $i++) 
				if(!$this->Tabs[$i]['custom']) unset($this->Tabs[$i]);
			$this->Tabs = array_values($this->Tabs);
			
			$SystemTabs = array();
			while(list(, $Value) = each($Menu->Tabs))
			{
				$SystemTabs[] = array(
					'tab' => $Value['Text'], 
					'page' => $Value['Value'], 
					'url' => $Value['Url'], 
					'html' => 0, 
					'custom' => 0, 
					'attribs' => $Value['Attributes'], 
					'roles' => $RoleArr, 
					'hidden' => 0
				);
			}
			reset($Menu->Tabs);
			$this->Tabs = array_merge($SystemTabs, $this->Tabs);
			
			$this->SaveTabs();
		}
		
		function BuildFile()
		{
			global $Menu;
			
			$RoleArr = array();
			for($i = 0; $i < count($this->Roles); $i++) $RoleArr[] = $this->Roles[$i]['ID'];
			
			if(!count($RoleArr))
			{
				//99.9% chance something's gone wrong, so we're not risking anything...
				echo('<span style="color: #f00; font-weight: bold;">'.$this->Context->GetDefinition('PageMng_NoRoles').'</span>');
				return;
			}
			
			$this->Tabs = array();
			while(list(, $Value) = each($Menu->Tabs))
			{
				$this->Tabs[] = array(
					'tab' => $Value['Text'], 
					'page' => $Value['Value'], 
					'url' => $Value['Url'], 
					'html' => 0, 
					'custom' => 0, 
					'attribs' => $Value['Attributes'], 
					'roles' => $RoleArr, 
					'hidden' => 0
				);
			}
			reset($Menu->Tabs);
			
			$this->SaveTabs();
		}
	}
	
	$PageMng = $Context->ObjectFactory->NewContextObject($Context, 'PageManagement');
	if(!defined('PAGEMNG_ISAJAX'))
	{
		//cover all our bases
		$Menu->AddToDelegate('PreHeadRender', 'PageMng_ReorderTabs');
		$Context->AddToDelegate('Menu', 'PreHeadRender', 'PageMng_ReorderTabs');
	}
	
	function PageMng_ReorderTabs(&$Menu)
	{
		global $PageMng;
		
		if($Menu->Context->SelfUrl == 'settings.php')
		{
			if(!is_array($PageMng->Tabs)) $PageMng->BuildFile();
			else if(ForceIncomingString('PostBackAction', '') == 'ResyncPages') $PageMng->ResyncSystemTabs();
		}
		
		if(!is_array($PageMng->Tabs)) return;
		$PageMng->RecreateTabs();
		
		//php4 dosn't like it when I set Menu->Tabs from the globals array
		$Menu->Tabs = $GLOBALS['Menu']->Tabs;
		if($PageMng->CurPage >= 0) $Menu->CurrentTab = $PageMng->Tabs[$PageMng->CurPage]['page'];
	}
}

if(($Context->SelfUrl == 'settings.php') && $Context->Session->User->Permission('PERMISSION_CHANGE_APPLICATION_SETTINGS'))
{
	$Context->Dictionary['RoleTabsNotes'] = 'Select the tabs this Role can view, and, if applicable, access.';
	$Context->Dictionary['CreateANewPage'] = 'Create a New Page';
	$Context->Dictionary['PageManagement'] = 'Page Management';
	$Context->Dictionary['ResyncTabs'] = 'Resync Tabs';
	$Context->Dictionary['ResyncTabsNotes'] = 'This will completely revert all system tabs back to the default, are you sure you wish to continue?';
	$Context->Dictionary['ResyncTabsSaved'] = 'All system tabs have been restored.';
	$Context->Dictionary['DefineNewPage'] = 'Define New Page';
	$Context->Dictionary['ModifyThePage'] = 'Modify the Page/Tab';
	$Context->Dictionary['SelectPage'] = 'Select Page/Tab to Edit';
	$Context->Dictionary['TabName'] = 'Tab Name';
	$Context->Dictionary['TabNameNotes'] = 'Name is the text which will appear on it.';
	$Context->Dictionary['TabIdentifier'] = 'Tab Identifier';
	$Context->Dictionary['TabIdentifierNotes'] = 'It is highly recommended that you leave the identifier field blank or how it originally was; it is only here for compatibility with other extensions.';
	$Context->Dictionary['TabAttributes'] = 'Tab Attributes';
	$Context->Dictionary['TabAttributesNotes'] = 'Extra HTML attributes for the tab anchor tag, such as the access key if Quick Keys is turned on (eg. accesskey="m"), or a title.';
	$Context->Dictionary['TabURL'] = 'Tab Url';
	$Context->Dictionary['TabURLNotes'] = 'The tab can either point to a URL or an HTML page.  If the URL is filled in in the above input, then it will be a hyperlink to that resource, if not, it will be a page with the below content. (note that urls must be complete, i.e. no relative urls)';
	$Context->Dictionary['PageHTML'] = 'Page HTML';
	$Context->Dictionary['PageHTMLNotes'] = 'PHP can also be included into the page HTML.';
	$Context->Dictionary['PageRoleNotes'] = 'The user roles which are allowed to view the tab and, if applicable, access the page it represents.  Please note, if a user, by default, cannot view a certain system tab, nothing in this will change that (eg. a guest will not be able to view the \'Settings\' tab just because it\'s set so here), and this will not prevent a user from accessing a link a hidden tab might point to.';
	$Context->Dictionary['PageReorderNotes'] = 'Drag and drop the pages below to reorder them.  Although their order will be saved automatically, you will need to refresh to see your changes.  Also note that tab order does not determine the default index page.';
	$Context->Dictionary['RoleTabs'] = 'Viewable Tabs/Pages';
	$Context->Dictionary['RoleTabsNotes'] = 'Select the tabs/custom pages the role is able to view/access';
	$Context->Dictionary['TabHidden'] = 'Tab Visibility';
	$Context->Dictionary['TabHiddenNotes'] = 'Whether or not a tab is displayed on the navigational bar';
	$Context->Dictionary['HiddenQ'] = 'Tab hidden from navigation';
	
	class PageForm extends PostBackControl
	{
		var $TabSelect;
		var $TabName = '', $TabHTML = '', $TabURL = '', $TabValue = '', $TabAttribs = '', $TabRoles = 0, $TabHidden = 0;
		var $RoleCheckboxes = '';
		
		function PageForm(&$Context)
		{
			global $PageMng;
			
			$this->ValidActions = array('Page', 'Pages', 'ProcessPages', 'ProcessPage', 'RemovePage', 'ProcessRemovePage', 'ResyncPages');
			$this->Constructor($Context);
			
			if(in_array($this->PostBackAction, array('ProcessPage', 'Page', 'ResyncPages')) || !is_array($PageMng->Tabs))
			{
				//get the role data
				$RoleMng = $this->Context->ObjectFactory->NewContextObject($Context, 'RoleManager');
				$RoleData = $RoleMng->GetRoles();
				if($RoleData)
				{
					$PageMng->Roles[] = array('ID' => 0, 'Name' => $this->Context->GetDefinition('Unathenticated'));
					while($Row = $this->Context->Database->GetRow($RoleData))
						$PageMng->Roles[] = array('ID' => $Row['RoleID'], 'Name' => FormatStringForDisplay($Row['Name']));
				}
			}
			
			$GLOBALS['Head']->AddScript('js/prototype.js');
			$GLOBALS['Head']->AddScript('js/scriptaculous.js');
			$GLOBALS['Head']->AddStyleSheet('extensions/'.PAGEMNG_FOLDER.'/PageMng.css');
			
			if($this->IsPostBack)
			{
				if($this->PostBackAction == 'ProcessPage')
				{
					if(!$PageMng->CreateTab($this)) 
						header('Location: '.$this->Context->Configuration['WEB_ROOT'].'settings.php?PostBackAction=Pages');
				}
				else if($this->PostBackAction == 'ProcessRemovePage')
				{
					$PageMng->RemoveTab();
					header('Location: '.$this->Context->Configuration['WEB_ROOT'].'settings.php?PostBackAction=Pages');
				}
				
				if(in_array($this->PostBackAction, array('ProcessPage', 'Page', 'RemovePage')))
				{
					$this->TabSelect = $this->Context->ObjectFactory->NewObject($this->Context, 'Select');
					$this->TabSelect->Name = 'PageID';
					$this->TabSelect->CssClass = 'SmallInput';
					$this->TabSelect->Attributes = 'id="tabselect"';
					$this->TabSelect->Hidden = 'id="tabhidden"';
					if($this->PostBackAction != 'RemovePage') $this->TabSelect->AddOption('', $this->Context->GetDefinition('[Create Page]'));
					for($i = 0; $i < count($PageMng->Tabs); $i++) 
						$this->TabSelect->AddOption($PageMng->Tabs[$i]['page'], $PageMng->Tabs[$i]['tab']);
				}
				
				if($this->PostBackAction == 'ProcessPage' || $this->PostBackAction == 'Page')
				{
					$IsValid = isset($PageMng->Tabs[$GLOBALS['PageMng']->SentPageIndex]) || $this->PostBackAction == 'ProcessPage';
					if($IsValid && $this->PostBackAction == 'Page') $this->TabRoles = $PageMng->Tabs[$GLOBALS['PageMng']->SentPageIndex]['roles'];
					
					for($i = 0; $i < count($PageMng->Roles); $i++)
					{
						$this->RoleCheckboxes .= "<li><p><span>".GetDynamicCheckBox("AllowedRoles[]", $PageMng->Roles[$i]['ID'], 
							$IsValid ? in_array($PageMng->Roles[$i]['ID'], $this->TabRoles) : 1, "", $PageMng->Roles[$i]['Name'], 
							'', 'id_'.$PageMng->Roles[$i]['ID'])."</span></p></li>\r\n";
					}
				}
			}
		}
		
		function Render()
		{
			global $PageMng;
			
			if($this->IsPostBack)
			{
				$this->PostBackParams->Clear();
				
				//editing or creating a page
				if($this->PostBackAction == 'Page' || $this->PostBackAction == 'ProcessPage')
				{
					if(isset($PageMng->Tabs[$GLOBALS['PageMng']->SentPageIndex]) && $this->PostBackAction == 'Page')
					{
						$this->TabName = htmlspecialchars($PageMng->Tabs[$GLOBALS['PageMng']->SentPageIndex]['tab']);
						$this->TabHTML = $PageMng->Tabs[$GLOBALS['PageMng']->SentPageIndex]['html'];
						if($this->TabHTML) $this->TabHTML = htmlspecialchars($this->TabHTML);
						else $this->TabHTML = '';
						
						$this->TabURL = $PageMng->Tabs[$GLOBALS['PageMng']->SentPageIndex]['url'];
						if($this->TabURL) $this->TabURL = htmlspecialchars($this->TabURL);
						else $this->TabURL = '';
						
						$this->TabValue = htmlspecialchars($PageMng->Tabs[$GLOBALS['PageMng']->SentPageIndex]['page']);
						$this->TabAttribs = htmlspecialchars($PageMng->Tabs[$GLOBALS['PageMng']->SentPageIndex]['attribs']);
						$this->TabHidden = (int)@$PageMng->Tabs[$GLOBALS['PageMng']->SentPageIndex]['hidden'];
					}
					
					$this->PostBackParams->Set('PostBackAction', 'ProcessPage');
					echo('<div id="Form" class="Account RoleEditForm">
						<fieldset>
							<legend>'.$this->Context->GetDefinition("PageManagement").'</legend>');
					if(isset($PageMng->Tabs[$GLOBALS['PageMng']->SentPageIndex]))
					{
						$this->TabSelect->Attributes .= ' onchange="document.location=\''.$this->Context->Configuration['WEB_ROOT'].'settings.php?PostBackAction=Page&PageID=\'+this.options[this.selectedIndex].value;"';
						$this->TabSelect->SelectedValue = $GLOBALS['PageMng']->SentPageID;
						echo('
							'.$this->Get_Warnings().'
							'.$this->Get_PostBackForm('frmPage').'
							<h2>1. '.$this->Context->GetDefinition('SelectPage').'</h2>
							<ul>
								<li><label for="tabselect">'.$this->Context->GetDefinition('Tab').' <small>'.$this->Context->GetDefinition('Required').'</small></label>'.
								$this->TabSelect->Get().'</li>
							</ul>
							<h2>2. '.$this->Context->GetDefinition('ModifyThePage').'</h2>');
					}
					else
					{
						echo('
							'.$this->Get_Warnings().'
							'.$this->Get_PostBackForm('frmPage').'
							<h2>'.$this->Context->GetDefinition('DefineNewPage').'</h2>');
					}
					echo('
						<ul>
							<li>
								<label for="txtTabName">'.$this->Context->GetDefinition('TabName').' <small>'.$this->Context->GetDefinition('Required').'</small></label>
								<input type="text" name="Name" value="'.$this->TabName.'" maxlength="80" class="SmallInput" id="txtTabName" />
								<p class="Description">'.$this->Context->GetDefinition('TabNameNotes').'</p>
							</li>
							<li>
								<label for="txtTabID">'.$this->Context->GetDefinition('TabIdentifier').'</label>
								<input type="text" name="Value" value="'.$this->TabValue.'" maxlength="80" class="SmallInput" id="txtTabID" />
								<p class="Description">'.$this->Context->GetDefinition('TabIdentifierNotes').'</p>
							</li>
							<li>
								<label for="txtTabAttrs">'.$this->Context->GetDefinition('TabAttributes').'</label>
								<input type="text" name="Attribs" value="'.$this->TabAttribs.'" class="SmallInput" id="txtTabAttrs" />
								<p class="Description">'.$this->Context->GetDefinition('TabAttributesNotes').'</p>
							</li>
							<li>
								<label for="txtTabURL">'.$this->Context->GetDefinition('TabURL').'</label>
								<input type="text" name="URL" value="'.$this->TabURL.'" class="SmallInput" id="txtTabURL" />
								<p class="Description">'.$this->Context->GetDefinition('TabURLNotes').'</p>
							</li>
							<li>
								<label for="txtTabHTML">'.$this->Context->GetDefinition('PageHTML').'</label>
								<textarea name="HTML" id="txtTabHTML" rows=20>'.$this->TabHTML.'</textarea>
								<p class="Description">'.$this->Context->GetDefinition('PageHTMLNotes').'</p>
							</li>
							<li>
								<p class="Description"><span>
								<input type="checkbox" name="Hidden" id="chkTabHidden" '.($this->TabHidden ? 'checked' : '').'/>
								'.$this->Context->GetDefinition('HiddenQ').'</span></p>
							</li>
							<li>
								<p class="Description"><strong>'.$this->Context->GetDefinition('Roles').'</strong><br />
								'.$this->Context->GetDefinition('PageRoleNotes').'</p>
							</li>
							'.$this->RoleCheckboxes.'
						</ul>
						<div class="Submit">
							<input type="submit" name="btnSave" value="'.$this->Context->GetDefinition('Save').'" class="Button SubmitButton" />
							<a href="./settings.php?PostBackAction=Pages" class="CancelButton">'.$this->Context->GetDefinition('Cancel').'</a>
						</div>
						</form>
					</fieldset>
					</div>');
				}
				
				//removing a page
				else if($this->PostBackAction == 'RemovePage')
				{
					$this->PostBackParams->Set('PostBackAction', 'ProcessRemovePage');
					$this->TabSelect->Attributes .= ' onchange="document.location=\'?PostBackAction=RemovePage&PageID=\'+this.options[this.selectedIndex].value;"';
					$this->TabSelect->SelectedValue = $GLOBALS['PageMng']->SentPageID;
					echo('<div id="Form" class="Account RoleRemoveForm"><fieldset>
						<legend>'.$this->Context->GetDefinition('Page Management').'</legend>
						'.$this->Get_PostBackForm('frmRemoveTab').'
						<h2>'.$this->Context->GetDefinition('Select Tab/Page to Remove').'</h2>
						<ul>
							<li><label for="tabselect">'.$this->Context->GetDefinition('Tabs/Pages').' <small>'.$this->Context->GetDefinition('Required').'</small></label>'.
							$this->TabSelect->Get().'</li>
						</ul>
						<div class="Submit">
							<input type="submit" name="btnSave" value="'.$this->Context->GetDefinition('Remove').'" class="Button SubmitButton RoleRemoveButton" />
							<a href="./settings.php?PostBackAction=Pages" class="CancelButton">'.$this->Context->GetDefinition('Cancel').'</a>
						</div>
						</form>
						</fieldset>
					</div>');
				}
				
				//resyncing
				else if($this->PostBackAction == 'ResyncPages')
				{
					echo('<div id="Form" class="Account Roles"><fieldset>
						<legend>'.$this->Context->GetDefinition('PageManagement').'</legend>
							<form>
							<ul>
								<li>
									<p class="Description">'.$this->Context->GetDefinition('ResyncTabsSaved').'<br /><br />
									<a href="./settings.php?PostBackAction=Pages">'.$this->Context->GetDefinition('ClickHereToContinue').'</a></p>
								</li>
							</ul>
							</form>
						</fieldset>
					</div>');
				}
				
				//list them all
				else
				{
					echo('<div id="Form" class="Account Roles"><fieldset>
						<legend>'.$this->Context->GetDefinition('PageManagement').'</legend>
						<form method="get" action="'.GetUrl($this->Context->Configuration, $this->Context->SelfUrl).'">
      					<input type="hidden" name="PostBackAction" value="Page" />
				    	<p>'.$this->Context->GetDefinition('PageReorderNotes').'</p>
				    	<div class="SortList" id="SortPages">');
				    
				    for($i = 0; $i < count($PageMng->Tabs); $i++)
				    {
		               echo('<div class="SortListItem MovableSortListItem" id="item_'.($i+1).'">
		               	   <div class="SortListOptions">
								<a class="SortEdit" href="'.$this->Context->Configuration['WEB_ROOT'].'settings.php?PostBackAction=Page&PageID='.$GLOBALS['PageMng']->Tabs[$i]['page'].'">'.$this->Context->GetDefinition('Edit').'</a>
								<a class="SortRemove" href="'.$this->Context->Configuration['WEB_ROOT'].'settings.php?PostBackAction=RemovePage&PageID='.$GLOBALS['PageMng']->Tabs[$i]['page'].'">&nbsp;</a>
							</div>
							'.$PageMng->Tabs[$i]['tab'].'
						</div>');
				    }
				    
				    echo('</div>
				    	<script type="text/javascript" language="javascript">
				         // <![CDATA[
				            Sortable.create(\'SortPages\', 
				    			{
				    				dropOnEmpty: true, 
				    				tag: \'div\', 
				    				constraint: \'vertical\', 
				    				ghosting: false, 
				    				onUpdate: function()
				    				{
				    					new Ajax.Updater(
				    						\'SortResult\', \''.$this->Context->Configuration['WEB_ROOT'].'extensions/'.PAGEMNG_FOLDER.'/ajax.php\', 
				    						{
				    							onComplete: function(request)
				    							{
				    								new Effect.Highlight(\'SortPages\', {startcolor:\'#ffff99\'});
				    							},
				    							parameters: Sortable.serialize(\'SortPages\', {tag:\'div\', name:\'PageID\'}), 
				    							evalScripts: true, 
				    							asynchronous: true
				    						}
				    					)
				    				}
				    			}
				    		);
				         // ]]>
				         </script>
						<div class="Submit">
							<input type="submit" name="btnSave" value="'.$this->Context->GetDefinition('CreateANewPage').'" class="Button SubmitButton NewRoleButton" />
							<a href="'.$this->Context->Configuration['WEB_ROOT'].'settings.php?PostBackAction=ResyncPages" 
							  onclick="if(confirm(\''.$this->Context->GetDefinition('ResyncTabsNotes').'\')) window.location=\''.$this->Context->Configuration['WEB_ROOT'].'settings.php?PostBackAction=ResyncPages\'; else return false;">'.$this->Context->GetDefinition('ResyncTabs').'</a>
							<a href="'.GetUrl($this->Context->Configuration, $this->Context->SelfUrl).'" class="CancelButton">'.$this->Context->GetDefinition('Cancel').'</a>
						</div>
					</form>
					</fieldset>
					</div>');
				}
			}
		}
	}
	
	//need another delegate for pre-button render of role form
	$Context->AddToDelegate('RoleForm', 'Constructor', 'PageMng_RoleFormConstruct');
	$Context->AddToDelegate('RoleForm', 'PreSubmitButton', 'PageMng_PreSubmitButton');
	
	function PageMng_RoleFormConstruct(&$RoleForm)
	{
		global $PageMng;
		
		
		if($RoleForm->PostBackAction == 'ProcessRoleRemove')
		{
			$ReplacementRoleID = ForceIncomingInt('ReplacementRoleID', 0);
				if($ReplacementRoleID == 1) $ReplacementRoleID = 0;
			$IncomingRoleID = ForceIncomingInt('RoleID', 0);
				if($IncomingRoleID == 1) $IncomingRoleID = 0;
			
			$PageMng->AssignRoleTabs($IncomingRoleID, 0, $ReplacementRoleID);
		}
		else if($RoleForm->PostBackAction == 'Role')
		{
			$RoleID = $RoleForm->Role->RoleID == 1 ? 0 : $RoleForm->Role->RoleID;
			
			//can't use ::PostBackAction as any ProcessX will have been turned into X
			if(ForceIncomingString('PostBackAction', '') == 'ProcessRole') 
				$PageMng->AssignRoleTabs($RoleID);
			
			$AllowedTabs = ForceIncomingArray('AllowedTabs', array());
			if($RoleForm->PostBackAction == 'Role' && !count($AllowedTabs))
			{
				for($i = 0; $i < count($PageMng->Tabs); $i++)
				{
					if(in_array($RoleID, $PageMng->Tabs[$i]['roles'])) $AllowedTabs[] = $i;
				}
			}
			//else error of some sort (redisplay options)
			for($i = 0; $i < count($PageMng->Tabs); $i++)
			{
				$PageMng->TabBoxes .= '
					<li><p><span><label for="ID_AllowedTabs['.$i.']"><input type="checkbox" name="AllowedTabs[]" value="'.$i.'"'.
					(in_array($i, $AllowedTabs) ? ' checked="checked"' : '').' id="ID_AllowedTabs['.$i.']" /> '.
					$PageMng->Tabs[$i]['tab'].'</label></span></p></li>';
			}
		}
	}
	
	function PageMng_PreSubmitButton(&$RoleForm)
	{
		global $PageMng;
		
		if($PageMng->TabBoxes != '')
			echo('
			<li>
				<p class="Description">
					<strong>'.$RoleForm->Context->GetDefinition('RoleTabs').'</strong>
					<br />'.$RoleForm->Context->GetDefinition('RoleTabsNotes').'
				</p>
			</li>'
			.$PageMng->TabBoxes);
	}
	
	$PageForm = $Context->ObjectFactory->NewContextObject($Context, 'PageForm');
	$Page->AddRenderControl($PageForm, $Configuration["CONTROL_POSITION_BODY_ITEM"]);
	$Panel->AddListItem($Context->GetDefinition('AdministrativeOptions'), $Context->GetDefinition('Page Management'), 'settings.php?PostBackAction=Pages', '', '');
} //end settings

if($Context->SelfUrl == 'index.php')
{
	$Context->Dictionary['PageLinks'] = 'Page Links';
	if($PageMng->CurPage >= 0 && !$PageMng->Tabs[$PageMng->CurPage]['url'] && 
		in_array($Context->Session->User->RoleID, $PageMng->Tabs[$PageMng->CurPage]['roles']))
	{
		class CustomPage
		{
			function CustomPage(&$Context)
			{
				$this->Context = &$Context;
			}
			
			function Render()
			{
				global $PageMng;
				
				$MatchCount = preg_match_all("/<\?php(.*?)\?>/si", $PageMng->Tabs[$PageMng->CurPage]['html'], $Matches);
				$HTML = preg_split("/<\?php(.*?)\?>/si", $PageMng->Tabs[$PageMng->CurPage]['html']);
				$FullString = '';
				for($i = 0; $i < $MatchCount; $i++)
				{
					list(, $CurHTML) = each($HTML);
					ob_start();
					eval($Matches[1][$i]);
					$FullString .= $CurHTML . ob_get_contents();
					ob_end_clean();
				}
				list(, $CurHTML) = each($HTML);
				$FullString .= $CurHTML;
				
				echo($FullString);
			}
		}
		
		//include the other extensions before we exit
		$CurFile = PAGEMNG_FOLDER.'/default.php';
		$ExtensionFile = $PageMng->MyReadFile($Context->Configuration['APPLICATION_PATH'].'conf/extensions.php');
		if(strstr($ExtensionFile, 'include($Configuration[\'EXTENSIONS_PATH\']."'.$CurFile.'");') != FALSE)
		{
			$ExtensionFile = explode('include($Configuration[\'EXTENSIONS_PATH\']."'.$CurFile.'");', $ExtensionFile);
			eval($ExtensionFile[1]);
		}
		
		//remove items from panel
		$BadPanelItems = array($Context->GetDefinition('Feeds'), $Context->GetDefinition('DiscussionFilters'));
		while(list($ky, $v) = each($Panel->PanelElements))
			if(in_array($v['Key'], $BadPanelItems)) unset($Panel->PanelElements[$ky]);
		reset($Panel->PanelElements);
		
		$Panel->AddString($Context->GetDefinition('PanelFooter'), 500);
		
		$Head->GetDelegatesFromContext();
		$Menu->GetDelegatesFromContext();
		$Panel->GetDelegatesFromContext();
		$NoticeCollector->GetDelegatesFromContext();
		$Foot->GetDelegatesFromContext();
		$PageEnd->GetDelegatesFromContext();
		
		//$Context->Session->Check($Configuration);
		$CustomPage = $Context->ObjectFactory->NewContextObject($Context, 'CustomPage');
		
		$Context->PageTitle = $PageMng->Tabs[$PageMng->CurPage]['tab'];
		//$Menu->CurrentTab = $PageMng->Tabs[$PageMng->CurPage]['page'];
		$Panel->CssClass = 'DiscussionPanel';
		$Body->CssClass = 'Discussions';
		$Head->BodyId = $PageMng->Tabs[$PageMng->CurPage]['page'];
		
		$Page->AddRenderControl($Head, $Configuration['CONTROL_POSITION_HEAD']);
		$Page->AddRenderControl($Menu, $Configuration['CONTROL_POSITION_MENU']);
		$Page->AddRenderControl($Panel, $Configuration['CONTROL_POSITION_PANEL']);
		$Page->AddRenderControl($NoticeCollector, $Configuration['CONTROL_POSITION_NOTICES']);
		$Page->AddRenderControl($CustomPage, $Configuration['CONTROL_POSITION_BODY_ITEM']);
		$Page->AddRenderControl($Foot, $Configuration['CONTROL_POSITION_FOOT']);
		$Page->AddRenderControl($PageEnd, $Configuration['CONTROL_POSITION_PAGE_END']);
		
		$Page->FireEvents();
		exit();
	}
	
	if(PAGE_LIST_LOC)
	{
		$links = array();
		for($i = 0; $i < count($PageMng->Tabs); $i++)
		{
			if(@$PageMng->Tabs[$i]['hidden'] && !$PageMng->Tabs[$i]['url'] && 
				in_array($Context->Session->User->RoleID, $PageMng->Tabs[$i]['roles'])) 
					$links[] = $i;
		}
		
		if(count($links))
		{
			$Panel->AddList($Context->GetDefinition('PageLinks'), PAGE_LIST_LOC);
			
			for($i = 0; $i < count($links); $i++) 
				$Panel->AddListItem(
					$Context->GetDefinition('PageLinks'), 
					$PageMng->Tabs[$links[$i]]['tab'], 
					($Configuration['URL_BUILDING_METHOD'] == 'mod_rewrite' ?
						($Configuration['BASE_URL'].'page/'.$PageMng->Tabs[$links[$i]]['page']) :
						($Configuration['BASE_URL'].'?Page='.$PageMng->Tabs[$links[$i]]['page']))
				);
		}
		
	}
}

?>