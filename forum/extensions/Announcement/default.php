<?php
/*
Extension Name: Announcement
Extension Url: http://lussumo.com/addons
Description: Adds a customizable announcement box on the discussion index page
Version: 1.2
Author: Alex Marshall
Author Url: http://www.iambigred.com/
*/

// Many thanks to Tibor Balogh for some suggested improvements

// Dictionary Values
$Context->Dictionary['AnnouncementSettings'] = 'Announcement Settings';
$Context->Dictionary['AnnouncementMessageTitle'] = 'Announcement Message';
$Context->Dictionary['AnnouncementNotes'] = 'Simply enter the Announcement which will be displayed on the discussion index page. HTML is accepted.';
$Context->Dictionary['AnnouncementLineColor'] = 'Line Color';
$Context->Dictionary['AnnouncementBGcolorTitle'] = 'Background Color';
$Context->Dictionary['AnnouncementTextColor'] = 'Text Color';
$Context->Dictionary['AnnouncementDisappear'] = 'Fade Announcement Away?';
$Context->Dictionary['AnnouncementTime'] = 'Length to show Announcement';
$Context->Dictionary['AnnouncementSeconds'] = 'seconds';
$Context->Dictionary['AnnouncementLineHeight'] = 'Line Spacing of Announcement Text';
$Context->Dictionary['AnnouncementLineSize'] = 'Line Height';
$Context->Dictionary['AnnouncementFontSize'] = 'Font Size of Announcement Text';
$Context->Dictionary['AnnouncementMargin'] = 'Margin below Announcement';
$Context->Dictionary['AnnouncementAlign'] = 'Announcement Text Alignment';
$Context->Dictionary['AnnouncementPixels'] = 'pixels';
$Context->Dictionary['AnnouncementPercent'] = '%';
$Context->Dictionary['AnnouncementDefaultMessage'] = 'Default Announcement Message';

class announcement extends Control {
        var $ConfigurationManager;

        function announcement(&$Context) {
                $this->Name = "Announcement";
                $this->Control($Context);

                $SettingsFile = $this->Context->Configuration['APPLICATION_PATH'].'conf/settings.php';
                $this->ConfigurationManager = $this->Context->ObjectFactory->NewContextObject($this->Context,'ConfigurationManager');
        }

        function Render() {
                $text = $this->ConfigurationManager->GetSetting('ANNOUNCE_MESSAGE', true);
                if ($text && $text !== 'ANNOUNCE_MESSAGE'){
                        echo '<div id="announcement">'.$text.'</div>';
                }
        }
}

// Initialise Settings
if (!array_key_exists('ANNOUNCE_SETUP', $Configuration)) {
        AddConfigurationSetting($Context, 'ANNOUNCE_SETUP', '1');
        AddConfigurationSetting($Context, 'ANNOUNCE_MESSAGE', $Context->GetDefinition('AnnouncementDefaultMessage'));
        AddConfigurationSetting($Context, 'ANNOUNCE_DISAPPEAR', '1');
        AddConfigurationSetting($Context, 'ANNOUNCE_TIME', '8');
        AddConfigurationSetting($Context, 'ANNOUNCE_TEXT', '060');
        AddConfigurationSetting($Context, 'ANNOUNCE_LINE', '9C9');
        AddConfigurationSetting($Context, 'ANNOUNCE_BGCOLOR', 'E2F9E3');
        AddConfigurationSetting($Context, 'ANNOUNCE_ALIGN', 'center');
        AddConfigurationSetting($Context, 'ANNOUNCE_LINESIZE', '1');
        AddConfigurationSetting($Context, 'ANNOUNCE_LINEHEIGHT', '200');
        AddConfigurationSetting($Context, 'ANNOUNCE_MARGIN', '10');
        AddConfigurationSetting($Context, 'ANNOUNCE_FONTSIZE', '13'); }

if (!array_key_exists('ANNOUNCE_LINESIZE', $Configuration)) {
        AddConfigurationSetting($Context, 'ANNOUNCE_ALIGN', 'center');
        AddConfigurationSetting($Context, 'ANNOUNCE_LINESIZE', '1');
        AddConfigurationSetting($Context, 'ANNOUNCE_LINEHEIGHT', '200');
        AddConfigurationSetting($Context, 'ANNOUNCE_MARGIN', '10');
        AddConfigurationSetting($Context, 'ANNOUNCE_FONTSIZE', '13'); }

if (in_array($Context->SelfUrl, array("index.php"))) {

        $AddStyle  = "
                                #announcement {
                                border-top: {linesize}px solid #{line};
                                border-bottom: {linesize}px solid #{line};
                                background: #{BGcolor};
                                color: #{text} !important;
                                display: block;
                                line-height: {lineheight}%;
                                text-align: {align};
                                font-size: {fontsize}px;
                                margin-bottom: {margin}px !important;
                                }
                                ";
        $AddStyle = str_replace('{BGcolor}',$Context->Configuration['ANNOUNCE_BGCOLOR'],$AddStyle);
        $AddStyle = str_replace('{text}',$Context->Configuration['ANNOUNCE_TEXT'],$AddStyle);
        $AddStyle = str_replace('{line}',$Context->Configuration['ANNOUNCE_LINE'],$AddStyle);
        $AddStyle = str_replace('{align}',$Context->Configuration['ANNOUNCE_ALIGN'],$AddStyle);
        $AddStyle = str_replace('{lineheight}',$Context->Configuration['ANNOUNCE_LINEHEIGHT'],$AddStyle);
        $AddStyle = str_replace('{linesize}',$Context->Configuration['ANNOUNCE_LINESIZE'],$AddStyle);
        $AddStyle = str_replace('{margin}',$Context->Configuration['ANNOUNCE_MARGIN'],$AddStyle);
        $AddStyle = str_replace('{fontsize}',$Context->Configuration['ANNOUNCE_FONTSIZE'],$AddStyle);
        $Head->AddString("\n<style>".$AddStyle."</style>\n");

        if ($Context->Configuration['ANNOUNCE_DISAPPEAR']) {
                // Inserts magic Javascript if disappear is enabled
                $Head->AddScript('extensions/Announcement/functions.js');
                $addJavaScript = "
                  <script type=\"text/javascript\">
                         var EffectTimer;
                         var Height = -1;
                         setTimeout(\"ExecuteEffect('announcement','HideEffect', 9);\", {time}000);
                  </script>
                ";

                $addJavaScript = str_replace('{time}',$Context->Configuration['ANNOUNCE_TIME'],$addJavaScript);
                $Head->AddString($addJavaScript);
        }

        $announcement = new announcement($Context);
        $Page->AddRenderControl($announcement,$Configuration["CONTROL_POSITION_BODY_ITEM"]-1);
}

if ($Context->SelfUrl == "settings.php" && $Context->Session->User->Permission('PERMISSION_CHANGE_APPLICATION_SETTINGS')
&& array_key_exists('ANNOUNCE_SETUP', $Configuration)) {
        class AnnouncementSettingsForm extends PostBackControl {
                var $ConfigurationManager;

                function AnnouncementSettingsForm(&$Context) {
                        $this->Name = 'AnnouncementSettingsForm';
                        $this->ValidActions = array('Announcement', 'ProcessAnnouncement');
                        $this->Constructor($Context);
                        if (!$this->Context->Session->User->Permission('PERMISSION_CHANGE_APPLICATION_SETTINGS'))
{
                                $this->IsPostBack = 0;
								} elseif( $this->IsPostBack ) {
                                $SettingsFile = $this->Context->Configuration['APPLICATION_PATH'].'conf/settings.php';
                                $this->ConfigurationManager = $this->Context->ObjectFactory->NewContextObject($this->Context,'ConfigurationManager');
                                if ($this->PostBackAction == 'ProcessAnnouncement') {
									$this->ConfigurationManager->GetSettingsFromForm($SettingsFile);
                                    // Forces checkbox result and saves all submitted fields
									$this->ConfigurationManager->DefineSetting('ANNOUNCE_DISAPPEAR',ForceIncomingBool('ANNOUNCE_DISAPPEAR', 0), 0);
									if($this->ConfigurationManager->SaveSettingsToFile($SettingsFile)) {
                                                header('location:'.GetUrl($this->Context->Configuration, 'settings.php', '', '', '', '', 'PostBackAction=Announcement&Success=1'));
                                        } else {
                                                $this->PostBackAction = 'Announcement';
                                        }
                                }
                        }
                        $this->CallDelegate('Constructor');
                }

                function Render() {
                        if ($this->IsPostBack) {
                                $this->CallDelegate('PreRender');
                                $this->PostBackParams->Clear();

                                if ($this->PostBackAction == "Announcement") {
										// Set up the left/right/center tabs
                                        $this->TabSelect = $this->Context->ObjectFactory->NewObject($this->Context, 'Select');
                                        $this->TabSelect->Name = 'ANNOUNCE_ALIGN';
                                        $this->TabSelect->CssClass = 'SmallInput';
                                        $this->TabSelect->Attributes = 'id="tabselect"';
										$this->TabSelect->AddOption('left',$this->Context->GetDefinition('Left'));
										$this->TabSelect->AddOption('center',$this->Context->GetDefinition('Center'));
										$this->TabSelect->AddOption('right',$this->Context->GetDefinition('Right'));
										// Set the default selected tab
										$this->TabSelect->SelectedValue = $this->ConfigurationManager->GetSetting('ANNOUNCE_ALIGN');
										
										$this->PostBackParams->Set('PostBackAction', "ProcessAnnouncement");
                                        echo '
                                        <div id="Form" class="Account AnnouncementSettings">
                                        <fieldset>
										<legend>'.$this->Context->GetDefinition("AnnouncementSettings").'</legend>
                                                '.$this->Get_Warnings().'
												'.$this->Get_PostBackForm('frmAnnouncement').'
												<p>'.$this->Context->GetDefinition("AnnouncementNotes").'</p>
												<li>
												<label for="txtMessage">'
												.$this->Context->GetDefinition("AnnouncementMessageTitle") . '</label>
												<textarea name="ANNOUNCE_MESSAGE" id="txtMessage">' .
												$this->ConfigurationManager->GetSetting('ANNOUNCE_MESSAGE') .
												'</textarea>
												</li>
												<li>
												<label for="txtBGcolor">'.
												$this->Context->GetDefinition("AnnouncementBGcolorTitle") . '</label>
												<input type="text" name="ANNOUNCE_BGCOLOR" id="txtBGcolor"  value="' .
												$this->ConfigurationManager->GetSetting('ANNOUNCE_BGCOLOR') . ' "maxlength="6" class="SmallInput" />
												</li>
												<li>
                                                <label for="txtText">'.$this->Context->GetDefinition("AnnouncementTextColor") . '</label>
												<input type="text" name="ANNOUNCE_TEXT" id="txtLine"  value="' .
												$this->ConfigurationManager->GetSetting('ANNOUNCE_TEXT') . ' "maxlength="6" class="SmallInput" />
												</li>
												<li>
												<label for="txtLine">'.$this->Context->GetDefinition("AnnouncementLineColor") . '</label>
												<input type="text" name="ANNOUNCE_LINE" id="txtLine"  value="' .
												$this->ConfigurationManager->GetSetting('ANNOUNCE_LINE') . ' "maxlength="6" class="SmallInput" />
												</li>
												<li>
												<label for="txtLineSize">'.
												$this->Context->GetDefinition("AnnouncementLineSize") . '</label>
												<input type="text" name="ANNOUNCE_LINESIZE" id="txtLineSize"  value="' .
												$this->ConfigurationManager->GetSetting('ANNOUNCE_LINESIZE') . ' "maxlength="2" class="SmallInput" />
												'.$this->Context->GetDefinition('AnnouncementPixels').'
												</li>
												<li>
												<label for="txtHeight">'.
												$this->Context->GetDefinition("AnnouncementLineHeight") . '</label>
												<input type="text" name="ANNOUNCE_LINEHEIGHT" id="txtHeight"  value="' .
												$this->ConfigurationManager->GetSetting('ANNOUNCE_LINEHEIGHT') . ' "maxlength="3" class="SmallInput" /> %
												</li>
												<li>
												<label for="txtMargin">'. $this->Context->GetDefinition("AnnouncementMargin") . '</label>
												<input type="text" name="ANNOUNCE_MARGIN" id="txtMargin"  value="' .
												$this->ConfigurationManager->GetSetting('ANNOUNCE_MARGIN') . ' "maxlength="2" class="SmallInput" />
												'.$this->Context->GetDefinition('AnnouncementPixels').'
												</li>
												<li>
												<label for="txtFontSize">'.
												$this->Context->GetDefinition("AnnouncementFontSize") . '</label>
												<input type="text" name="ANNOUNCE_FONTSIZE" id="txtFontSize"  value="' . 
												$this->ConfigurationManager->GetSetting('ANNOUNCE_FONTSIZE') . ' "maxlength="2" class="SmallInput" />
												'.$this->Context->GetDefinition('AnnouncementPixels').'
												</li>
												<li>
												<label for="tabselect">'.$this->Context->GetDefinition('AnnouncementAlign').'</label>'.
												$this->TabSelect->Get() . '
												</li>
												<li>' .
												GetDynamicCheckBox('ANNOUNCE_DISAPPEAR', 1,$this->ConfigurationManager->GetSetting('ANNOUNCE_DISAPPEAR'),'',$this->Context->GetDefinition("AnnouncementDisappear"))
												. '</li>
												<li>
												<label for="txtTime">'. $this->Context->GetDefinition("AnnouncementTime") . '</label>
												<input type="text" name="ANNOUNCE_TIME" id="txtTime"  value="' . $this->ConfigurationManager->GetSetting('ANNOUNCE_TIME') . ' "maxlength="6" class="SmallInput" /> ' . $this->Context->GetDefinition("AnnouncementSeconds") . '
												</li>
												</ul>
                                                <div class="Submit">
                                                        <input type="submit" name="btnSave" value="'.$this->Context->GetDefinition('Save').'" class="Button SubmitButton" />
                                                        <a href="'.GetUrl($this->Context->Configuration,$this->Context->SelfUrl).'"class="CancelButton">'.$this->Context->GetDefinition('Cancel').'</a>
                                                </div>
                                                </form>
                                        </fieldset>
                                        </div>';
                                }
                        }
                        $this->CallDelegate('PostRender');
                }
        }

        $AnnouncementSettingsForm = $Context->ObjectFactory->NewContextObject($Context,'AnnouncementSettingsForm');
        $Page->AddRenderControl($AnnouncementSettingsForm,$Configuration["CONTROL_POSITION_BODY_ITEM"] + 1);

        $AdministrativeOptions = $Context->GetDefinition("AdministrativeOptions");
        $Panel->AddList($AdministrativeOptions, 10);
        $Panel->AddListItem($AdministrativeOptions,$Context->GetDefinition("AnnouncementSettings"),GetUrl($Context->Configuration, 'settings.php', '', '', '', '', 'PostBackAction=Announcement')); }

// The End :)
?>
