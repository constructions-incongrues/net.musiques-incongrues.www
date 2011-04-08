<?php
/*
Extension Name: Vanillacons
Extension Url: http://www.krijtenberg.nl/
Description: A huge categorized library of insertable smilies for your posts
Version: 1.1
Author: Maurice Krijtenberg
Author Url: http://www.krijtenberg.nl/

Changes

Version 1.2 - 20.06.2006, Jazzman
- Changed Vanillacons to work with Friendly Urls
*/

$Configuration["SMILIES_PATH"] = 'extensions/Vanillacons/smilies/';
$Configuration["SMILIES_DEFAULT"] = 'otro - fruits';

$Context->Dictionary['Vanillacons'] = 'Vanillacons';
$Context->Dictionary['VanillaconsNotes'] = 'Vanillacons allows users to add smilies to their posts. You can upload unlimited smilies to the smilies directory, but you will have to rebuild the <strong>smilies.js</strong> and <strong>smilies.php</strong> file by clicking the rebuild button.';
$Context->Dictionary['RebuildVanillacons'] = 'Rebuild Vanillacons';
$Context->Dictionary['VanillaconsRebuilded'] = 'The smilies have succesfully been rebuilded and are now ready to use.';
$Context->Dictionary['SmiliesFound'] = 'Smilies found.';

if (in_array($Context->SelfUrl, array("post.php", "comments.php", "settings.php"))) {
	if( file_exists( dirname(__FILE__) . '/smilies.php' )) {
		include(dirname(__FILE__) . '/smilies.php');
	}

	class Vanillacons {
		var $Name;
		var $Context;

		function Vanillacons(&$Context) {
			$this->Name = "Vanillacons";
			$this->Context = &$Context;
		}

		function GetSmilieUrl($filename) {
			$link = explode('.', $filename);
			return $link[0];
		}

		function GetSmilieImg($Category, $filename) {
			return $this->Context->Configuration["WEB_ROOT"] . $this->Context->Configuration["SMILIES_PATH"] . $Category . '/' . $filename;
		}

		function GetSmilieLink($Category, $filename) {
			$link   = $this->GetSmilieUrl($filename);
			$imgurl = $this->GetSmilieImg($Category, $filename);
			return "<span onclick=\"insertSmilie('".$link."');\" class=\"VanillaconsLink\"><img src=\"".$imgurl."\" alt=\"\" /></span>";
		}

		function Rebuild() {
			$count = 0;
			$dircount = 0;
			$Smilies = "<?php\n";
			$Output  = "var arrSmilies = new Array();\n";
			$SmiliesDir = $this->Context->Configuration["APPLICATION_PATH"] . $this->Context->Configuration["SMILIES_PATH"];
			foreach( glob($SmiliesDir.'*', GLOB_ONLYDIR) as $dir ) {
				$dircount++;
				$Output .= "\narrSmilies[\"".basename($dir)."\"] = new Array();\n";
				$i = 0;
				foreach( glob($dir.'/*') as $file ) {
					$link     = $this->GetSmilieUrl(basename($file));
					$imgurl   = $this->GetSmilieImg(basename($dir), basename($file));
					$Output  .= "arrSmilies[\"".basename($dir)."\"][".$i."] = '";
					$Output  .= "<span onclick=\"insertSmilie(\'".$link."\');\" class=\"VanillaconsLink\"><img src=\"".$imgurl."\" alt=\"\" /></span>";
					$Output  .= "';\n";
					$Smilies .= "\$Smilies[\"".$this->GetSmilieUrl(basename($file))."\"] = \"".$this->GetSmilieImg(basename($dir), basename($file))."\";\n";
					$i++;
					$count++;
				}
			}
			$Smilies .= "\$Configuration['SMILIES_CATEGORIES'] = \"".$dircount."\";\n";
			$Smilies .= "?>";
			file_put_contents(dirname(__FILE__).'/smilies.js', $Output);
			file_put_contents(dirname(__FILE__).'/smilies.php', $Smilies);

			return $count;
		}

		function GetStandardSmilies() {
			$sReturn = "<ul>";
			$SmiliesDir = $this->Context->Configuration["APPLICATION_PATH"] . $this->Context->Configuration["SMILIES_PATH"];
			$SmiliesDir .= $this->Context->Configuration["SMILIES_DEFAULT"];
			foreach( glob($SmiliesDir.'/*') as $file ) {
				$sReturn .= "<li>".$this->GetSmilieLink(basename($SmiliesDir), basename($file))."</li>";
			}
			$sReturn .= "</ul>";
			return $sReturn;
		}
	}


	class VanillaconsFormatter extends StringFormatter {
		var $token;
		var $tokenValue;

		function LoadSmilies($Smilies) {
			$counter = 0;
			if( isset($Smilies) ) {
				foreach( $Smilies as $Key => $Value ) {
					$this->token[$counter] = '/'.preg_quote(":".$Key.":").'/';
					$this->tokenValue[$counter] = "<img src=\"".$Value."\" alt=\":".$Key.":\" title=\":".$Key.":\" />";
					$counter++;
				}
			}
		}

		function Parse ($String, $Object, $FormatPurpose) {
			$sReturn = $String;
			// Only format plain text strings if they are being displayed (save in database as is)
			if ($FormatPurpose == FORMAT_STRING_FOR_DISPLAY) {
				$sReturn = preg_replace($this->token, $this->tokenValue, $sReturn);
			}
			return $sReturn;
		}
	}
	

	function CommentForm_Vanillacons(&$CommentForm) {
		$SmiliesDir  = $CommentForm->Context->Configuration["APPLICATION_PATH"] . $CommentForm->Context->Configuration["SMILIES_PATH"];

		$Vanillacons = $CommentForm->Context->ObjectFactory->NewContextObject($CommentForm->Context, "Vanillacons");
		$DirSelect   = $CommentForm->Context->ObjectFactory->NewObject($CommentForm->Context, "Select");

		$DirSelect->Name = "VanillaconsDir";

		$DirSelect->SelectedValue = ForceIncomingString("VanillaconsDir", 'otro - fruits');

		$DirSelect->Attributes = 'id="smilies-select" onchange="changeSmilies(this.options[this.selectedIndex].value);" onfocus="changeSmilies(this.options[this.selectedIndex].value);" id="VanillaconsComboBox"';
		foreach( glob($SmiliesDir.'*', GLOB_ONLYDIR) as $dir ) {
			$DirSelect->AddOption(basename($dir), basename($dir));
		}
		echo '<div id="Vanillacons">';
		if( $CommentForm->Context->Configuration["SMILIES_CATEGORIES"] > 1 ) 
		{
		  echo '<label for="smilies-select" style="display: inline;">&nbsp;Des smileys tout plein :</label>';
		  $DirSelect->Write();
		}
		echo '<div id="VanillaconsSmilies">'.$Vanillacons->GetStandardSmilies().'</div></div>';
		echo '<script>document.getElementById("smilies-select").focus();</script>';
	}


	// Add Stylesheet and JavaScript to Head Control
	$Head->AddStyleSheet("extensions/Vanillacons/style.css");
	$Head->AddScript('extensions/Vanillacons/functions.js');
	$Head->AddScript('extensions/Vanillacons/smilies.js');

	
	// Create delegates
	if( $Context->Session->UserID > 0) {
		$Context->AddToDelegate("DiscussionForm", "CommentForm_PreButtonsRender", "CommentForm_Vanillacons");
		$Context->AddToDelegate("DiscussionForm", "DiscussionForm_PreButtonsRender", "CommentForm_Vanillacons");
	}
	
	include(dirname(__FILE__) . '/smilies.php');
	
	// Global StringFormatter
	$VanillaconsFormatter = $Context->ObjectFactory->NewObject($Context, "VanillaconsFormatter");
	$VanillaconsFormatter->LoadSmilies($Smilies);
	$Context->StringManipulator->AddGlobalManipulator("VanillaconsFormatter", $VanillaconsFormatter);
}


if( $Context->SelfUrl == 'settings.php' && $Context->Session->User->Permission('PERMISSION_CHANGE_APPLICATION_SETTINGS') ) {

	class VanillaconsForm extends PostBackControl {
		var $Name;
		var $VanillaconsCount;
		
		function VanillaconsForm(&$Context) {
			$this->ValidActions = array('Vanillacons', 'RebuildVanillacons');
			$this->Constructor($Context);
			$this->Name = 'VanillaconsForm';
			if ($this->IsPostBack) {
				if ($this->PostBackAction == 'RebuildVanillacons') {
					$Vanillacons = $this->Context->ObjectFactory->NewContextObject($this->Context, "Vanillacons");
					$this->VanillaconsCount = $Vanillacons->Rebuild();
					$this->PostBackValidated = 1;
				}
			}
		}

		function Render_ValidPostBack() {
			echo '<div id="Form" class="Account VanillaconsForm">
			   <fieldset>
				  <legend>'.$this->Context->GetDefinition('Vanillacons').'</legend>
				  <form id="frmVanillacons" method="post" action="">
				  <p class="Description">'.$this->Context->GetDefinition('VanillaconsRebuilded').'</p>
				  <p class="Description"><strong>'.$this->VanillaconsCount.'</strong> '.$this->Context->GetDefinition('SmiliesFound').'</p>
				  </form>
			   </fieldset>
			</div>';
		}

		function Render_NoPostBack() {
			if ($this->IsPostBack) {
				if ($this->PostBackAction == 'Vanillacons') {
					$this->PostBackParams->Clear();
					$this->PostBackParams->Set('PostBackAction', 'RebuildVanillacons');
					echo '
					<div id="Form" class="Account VanillaconsForm">
						<fieldset>
						<legend>'.$this->Context->GetDefinition('Vanillacons').'</legend>
						'.$this->Get_Warnings().'
						'.$this->Get_PostBackForm('frmVanillacons').'
						<p>'.$this->Context->GetDefinition('VanillaconsNotes').'</p>
						<div class="Submit">
						<input type="submit" name="btnCheck" value="'.$this->Context->GetDefinition('RebuildVanillacons').'" class="Button SubmitButton Update" />
						<a href="'.GetUrl($this->Context->Configuration, 'settings.php').'" class="CancelButton">'.$this->Context->GetDefinition('Cancel').'</a>
						</div>
						</form>
						</fieldset>
					</div>';
				}
			}
		}
	}

	$Url = GetUrl($Context->Configuration, $Context->SelfUrl, '', '', '', '', 'PostBackAction=Vanillacons');
	$VanillaconsForm = $Context->ObjectFactory->NewContextObject($Context, 'VanillaconsForm');
	$Page->AddRenderControl($VanillaconsForm, $Configuration['CONTROL_POSITION_BODY_ITEM'] + 60);
	$Panel->AddListItem($Context->GetDefinition('AdministrativeOptions'), 'Vanillacons', $Url, '', '', 100);
}
?>
