<?php if (!defined('APPLICATION')) exit();
/**
* SidePanelLinks Module.
*/
class SidePanelLinksModule extends Gdn_Module {
/**
 * Set the target as the side panel.
 *
 *  @return  n/a
 */
    public function assettarget() {
        return 'Panel';
    }
/**
 * Standard vanilla panel content returning function.
 *
 *  @return  n/a
 */
    public function tostring() {
        $String="";
        $Title = C('Plugins.SidePanelLinks.Paneltitle', t('Useful Links'));
        $Enddiv='';
        if ($Title != "") {
            $String = '<div class="Box SidePanelLinks"><h4><center>'.$Title.'</center></h4>';
            $Enddiv='</div>';
        }
        /***/
        echo $String;
        for ($x = 1; $x <= 20; $x++) {
            $Active = C('Plugins.SidePanelLinks.Active'.$x, false);
            if ($Active) {
                $Permission = C('Plugins.SidePanelLinks.Permission'.$x, "");
                if ($Permission == '' || (CheckPermission($Permission))) {
                    $UrlTitle = C('Plugins.SidePanelLinks.Title'.$x, "");
                    $Url = C('Plugins.SidePanelLinks.Url'.$x, "");
                    //
                    if ($UrlTitle != '' && $Url != '') {
                        if ($First) {
                            $First = false;
                            echo ('<span class="PanelInfo">');
                        }
                        //
                        echo ('<span class="SidePanelLinksItem" rel="');
                        echo (Url($Url));
                        echo ('">');
                        echo Anchor($UrlTitle, "$Url").'<br>';
                        echo '</span>';
                        //
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
