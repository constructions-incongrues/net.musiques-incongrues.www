<?php
/**
 * SidePanelLinks plugin.
 *
 */

$PluginInfo['SidePanelLinks'] = array(
    'Name' => 'Side Panel Links',
    'Description' => 'Add links to the side panel via the Admin Settings and using fleible rules',
    'Version' => '1.2',
    'RequiredApplications' => array('Vanilla' => '2.1.13'),             /*This is what I tested it */
    'HasLocale' => true,
    'MobileFriendly' => true,
    'SettingsUrl' => '/dashboard/plugin//SidePanelLinks',
    'RequiredTheme' => false,
    'SettingsPermission' => 'Garden.Settings.Manage',
    'RegisterPermissions' => array('Plugins.PanelLinks.View'),      /*Permission to view panel*/
    'Author' => 'Ron Brahmson',
    'GitHub' => "https://github.com/rbrahmson/VanillaPlugins/tree/master/SidePanelLinks",
    'License' => 'GPLv3'
);
/**
* Plugin to add links to the side panel via the Admin Settings and using fleible rules.
*/
class SidePanelLinksplugin extends Gdn_Plugin {
/**
 * Plugin setup.
 *
 *  @return  n/a
 */
    public function setup() {
    }
/**
* Add module to side panel.
*
* @param Standard $Sender Standard
*
*  @return boolean n/a
*/
    public function base_render_before($Sender) {
        if (!CheckPermission('Plugins.PanelLinks.View')) {
            return;
        }
        //
        if ($Sender->MasterView != 'admin') {
            $Sender->AddCSSFile($this->GetResource('design/sidepanellinks.css', false, false));
            //
            $Controller = $Sender->ControllerName;                      //Current Controller
            $Controllerlist = C('Plugins.SidePanelLinks.Controllers', "");   //List of controllers to show side panel
            //
            $Array = explode(",", $Controllerlist);
            // Check whether the current controller is in the defined list
            if (!InArrayI($Controller, $Array)) {
                return;
            }
            // Add to the side panel
            if (GetValue('Panel', $Sender->Assets)) {
                $SidePanelLinksModule = new SidePanelLinksModule($Sender);
                $Sender->AddModule($SidePanelLinksModule, 'Panel');
            }
        }
    }
/**
* Plugin Settings.
*
* @param Standard $Sender Standard
*
* @return boolean n/a
*/
    public function plugincontroller_sidepanellinks_create($Sender) {
        $Sender->Title('SidePanelLinks '.t('Settings'));
        $Sender->AddSideMenu('plugin/SidePanelLinks');
        $Sender->Permission('Garden.Settings.Manage');
        $Sender->Form = new Gdn_Form();
        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->SetField(array(
            'Plugins.SidePanelLinks.Controllers',
            'Plugins.SidePanelLinks.Paneltitle',
            'Plugins.SidePanelLinks.Modules',
            ));
        for ($x = 1; $x <= 20; $x++) {
            $ConfigurationModel->SetField(array(
                'Plugins.SidePanelLinks.Active'.$x,
                'Plugins.SidePanelLinks.Permission'.$x,
                'Plugins.SidePanelLinks.Title'.$x,
                'Plugins.SidePanelLinks.Url'.$x,
            ));
        }
        $Sender->Form->SetModel($ConfigurationModel);

        if ($Sender->Form->AuthenticatedPostBack() == false) {
            $Sender->Form->SetData($ConfigurationModel->Data);
        } else {
            $Data = $Sender->Form->FormValues();
            $ConfigurationModel->Validation->ApplyRule('Plugins.SidePanelLinks.Controllers', array('Required'));
            if ($Sender->Form->Save() != false) {
                $Sender->StatusMessage = T('Your settings have been saved.');
            }
        }

        $Sender->Render($this->GetView('SidePanelLinksSettings.php'));
    }
}
