<?php if (!defined('APPLICATION')) exit();
//  SidePanelLinks Module
//	Specify the target (the side panel)
class SidePanelLinksModule extends Gdn_Module {
	public function AssetTarget() {
		return 'Panel';
	}
 	///////////////////////////////////////////////////
	public function ToString() {
		//$Debug = false;
		//$DebugParm = $_GET['debugit'];
		//if ($DebugParm == 'yes') $Debug = true;
		$String="";
		$Title = C('Plugins.SidePanelLinks.Paneltitle',t('Useful Links'));
		$Enddiv='';
		if ($Title != "") {
			$String = '<div class="Box SidePanelLinks"><h4><center>'.$Title.'</center></h4>';
			$Enddiv='</div>';
		}
		/***/
		echo $String;
		for ($x = 1; $x <= 20; $x++) {
			$Active = C('Plugins.SidePanelLinks.Active'.$x,false);
			//echo "<br>$x:".$x." Active:".$Active;
			if ($Active) {
				$Permission = C('Plugins.SidePanelLinks.Permission'.$x,"");
				/*if ($Debug) {
					echo "<br>$x:".$x." Permission:".$Permission;
					if ($Permission != '') echo '<br>P result:'.CheckPermission($Permission);
				}
				*/
				if ($Permission == '' || (CheckPermission($Permission))) {
					$UrlTitle = C('Plugins.SidePanelLinks.Title'.$x,"");
					$Url = C('Plugins.SidePanelLinks.Url'.$x,"");
					//if ($Debug) echo "<br>$x:".$x." UrlTitle:".$UrlTitle;
					if ($UrlTitle != '' && $Url != '') {
						if ($First) {
							$First = false;
							echo ('<span class="PanelInfo">');
						}
						//////
						echo ('<span class="SidePanelLinksItem" rel="');  Echo(Url($Url)) ; 
						echo('">');
						echo Anchor($UrlTitle, "$Url").'<br>';	 
						echo '</span>';
						//////
					}
				}
			}
		}
		return($Enddiv);
		//echo '<canvas id="SidePanelLinksFace" class="'.$this->GetCoolClockClass().'"></canvas>';
		//echo '</div>';
		
		/***/
  }
   	///////////////////////////////////////////////////
}