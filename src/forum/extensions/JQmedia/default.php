<?php
/*
Extension Name: JQmedia
Extension Url: http://lussumo.com/addons/index.php?PostBackAction=AddOn&AddOnID=265
Description: Enable online video service like youtube to work in comments. Requires jQuery
Version: 0.6.3
Author: Ziyad (MySchizoBuddy) Saeed
Author Url: http://www.myschizobuddy.com
Support:

*  Audio Usage:
*  ------------
*  $j('a[@href$=".mp3"]').jqmedia('mp3');
*
*  The sample script above will turn an anchor like this:
*  <a href="song.mp3">Play my song!</a>
*
*  into a div like this:
*  <div class="media mp3"><embed ...   />Play my song!/div>
*
*  Video Usage:
*  ------------
*  $j('a[@href^="http://www.youtube.com/"]').jqmedia('youtube');
*	$j('a[@href^="http://video.google."]').jqmedia('google');
*	$j('a[@href^="http://vids.myspace.com/"]').jqmedia('myspace');
*	$j('a[@href^="http://www.ifilm.com/"]').jqmedia('ifilm');
*
*  Options:
*  -------
*  w:200          //width of video or mp3player or podcast player
*  h:200          //height of video or podcast player
*  bc:0x000       //backcolor of mp3player and podcast player
*  fc:0xFFF       //frontcolor of mp3player and podcast player
*  lc:0xCCC       //highlight color of mp3player and podcast player
*  autostart:true //autoplay the podcast player
*  shuffle:true   //shuffle music for the podcast player
*  tn:true        //Albumart thumbnail for the podcast player use <image> tag in the playlist.xml file
*  <a class="w:200 h:200 ....." href=""></a>
*/

$Context->SetDefinition('ExtensionOptions', 'Extension Options');
$Context->SetDefinition('JQmedia', 'jQmedia');
$Context->SetDefinition('JQmediaSettings', 'jQmedia Settings');
$Context->SetDefinition('JQmediaNotes', 'These settings are specific to jQmedia. With this extension you can allow embedded online video services like Youtube etc. The user just enters a url to the youtube video and this extension will make it live, so you can watch it right inside the comment');
$Context->SetDefinition('JQmediaVideo', 'Select online video services to embed');
$Context->SetDefinition('JQmediaYoutube', 'Youtube video');
$Context->SetDefinition('JQmediaVimeo', 'Vimeo video');
$Context->SetDefinition('JQmediaDailymotion', 'Dailymotion video');
$Context->SetDefinition('JQmediaGoogle', 'Google video');
$Context->SetDefinition('JQmediaMyspace', 'Myspace video');
$Context->SetDefinition('JQmediaIfilm', 'Spike (powered by Ifilm)');
$Context->SetDefinition('JQmediaBrightcove', 'Brightcove');
$Context->SetDefinition('JQmediaStage6', 'Stage6 (powered by Divx)');
$Context->SetDefinition('JQmediaRevver', 'Revver');
$Context->SetDefinition('JQmediaMp3', 'Allow embedding of mp3 player');

//Settings
if( !array_key_exists('JQMEDIA_ALLOW_MP3', $Configuration)) {AddConfigurationSetting($Context, 'JQMEDIA_ALLOW_MP3', '0');}
if( !array_key_exists('JQMEDIA_ALLOW_YOUTUBE', $Configuration)) {AddConfigurationSetting($Context, 'JQMEDIA_ALLOW_YOUTUBE', '0');}
if( !array_key_exists('JQMEDIA_ALLOW_VIMEO', $Configuration)) {AddConfigurationSetting($Context, 'JQMEDIA_ALLOW_VIMEO', '0');}
if( !array_key_exists('JQMEDIA_ALLOW_DAILYMOTION', $Configuration)) {AddConfigurationSetting($Context, 'JQMEDIA_ALLOW_DAILYMOTION', '0');}
if( !array_key_exists('JQMEDIA_ALLOW_GOOGLE', $Configuration)) {AddConfigurationSetting($Context, 'JQMEDIA_ALLOW_GOOGLE', '0');}
if( !array_key_exists('JQMEDIA_ALLOW_MYSPACE', $Configuration)) {AddConfigurationSetting($Context, 'JQMEDIA_ALLOW_MYSPACE', '0');}
if( !array_key_exists('JQMEDIA_ALLOW_IFILM', $Configuration)) {AddConfigurationSetting($Context, 'JQMEDIA_ALLOW_IFILM', '0');}
if( !array_key_exists('JQMEDIA_ALLOW_BRIGHTCOVE', $Configuration)) {AddConfigurationSetting($Context, 'JQMEDIA_ALLOW_BRIGHTCOVE', '0');}
if( !array_key_exists('JQMEDIA_ALLOW_STAGE6', $Configuration)) {AddConfigurationSetting($Context, 'JQMEDIA_ALLOW_STAGE6', '0');}
if( !array_key_exists('JQMEDIA_ALLOW_REVVER', $Configuration)) {AddConfigurationSetting($Context, 'JQMEDIA_ALLOW_REVVER', '0');}


//JQmedia Folder
$Configuration["JQMEDIA_PATH"] = 'extensions/JQmedia/';

if (in_array($Context->SelfUrl, array('comments.php','post.php')) && $Context->Session->User->Preference("HtmlOn") == 1) {
	includeJQuery();
   $Head->AddStylesheet($Context->Configuration['JQMEDIA_PATH'].'jqmedia.css');
   $Head->AddScript($Context->Configuration['JQMEDIA_PATH'].'jqmedia.js');
   $VideoScript = '<script>var $j=jQuery.noConflict();
				$j(document).ready(function(){';
	if ($Context->Configuration['JQMEDIA_ALLOW_YOUTUBE'] == '1') {
	    	$VideoScript .= '$j(\'#ContentBody .CommentBody a[@href*="youtube.com/"]\').jqmedia(\'youtube\');';}
	if ($Context->Configuration['JQMEDIA_ALLOW_VIMEO'] == '1') {
	    	$VideoScript .= '$j(\'#ContentBody .CommentBody a[@href*="vimeo.com/"]\').jqmedia(\'vimeo\');';}
	if ($Context->Configuration['JQMEDIA_ALLOW_DAILYMOTION'] == '1') {
	    	$VideoScript .= '$j(\'#ContentBody .CommentBody a[@href*="dailymotion.com/"]\').jqmedia(\'dailymotion\');';}
	if ($Context->Configuration['JQMEDIA_ALLOW_GOOGLE'] == '1') {
		$VideoScript .= '$j(\'#ContentBody .CommentBody a[@href*="http://video.google."]\').jqmedia(\'google\');';}
	if ($Context->Configuration['JQMEDIA_ALLOW_MYSPACE'] == '1') {
		$VideoScript .= '$j(\'#ContentBody .CommentBody a[@href*="myspacetv.com/"],#ContentBody .CommentBody a[@href^="http://vids.myspace.com/"]\').jqmedia(\'myspace\');';}
	if ($Context->Configuration['JQMEDIA_ALLOW_IFILM'] == '1') {
		$VideoScript .= '$j(\'#ContentBody .CommentBody a[@href*="ifilm.com/"]\').jqmedia(\'ifilm\');';}
	 if ($Context->Configuration['JQMEDIA_ALLOW_BRIGHTCOVE'] == '1') {
		$VideoScript .= '$j(\'#ContentBody .CommentBody a[@href*="brightcove.tv/"]\').jqmedia(\'brightcove\');';}
	 if ($Context->Configuration['JQMEDIA_ALLOW_REVVER'] == '1') {
		$VideoScript .= '$j(\'#ContentBody .CommentBody a[@href^="http://one.revver.com"]\').jqmedia(\'revver\');';}
         if ($Context->Configuration['JQMEDIA_ALLOW_STAGE6'] == '1') {
		$VideoScript .= '$j(\'#ContentBody .CommentBody a[@href^="http://stage6.divx.com/"]\').jqmedia(\'stage6\');';}
	if ($Context->Configuration['JQMEDIA_ALLOW_MP3'] == '1') {
		$VideoScript .= '$j(\'#ContentBody .CommentBody a[@href$=".mp3"],#ContentBody .CommentBody a[@href$=".MP3"]\').jqmedia(\'mp3\');';}
		$VideoScript .= '});</script>';

   // Add the form to the panel
   $Head->AddString($VideoScript);
  }

if ($Context->SelfUrl == "settings.php") {

	class JQmediaForm extends PostBackControl {
		var $ConfigurationManager;

		function JQmediaForm(&$Context) {
			$this->Name = 'JQmediaForm';
			$this->ValidActions = array('JQmedia', 'ProcessJQmedia');
			$this->Constructor($Context);
			if( $this->IsPostBack ) {
				$SettingsFile = $this->Context->Configuration['APPLICATION_PATH'].'conf/settings.php';
				$this->ConfigurationManager = $this->Context->ObjectFactory->NewContextObject($this->Context, 'ConfigurationManager');
				if ($this->PostBackAction == 'ProcessJQmedia') {
					$this->ConfigurationManager->GetSettingsFromForm($SettingsFile);
					$this->ConfigurationManager->DefineSetting('JQMEDIA_ALLOW_MP3', ForceIncomingBool('JQMEDIA_ALLOW_MP3', 0), 0);
					$this->ConfigurationManager->DefineSetting('JQMEDIA_ALLOW_YOUTUBE', ForceIncomingBool('JQMEDIA_ALLOW_YOUTUBE', 0), 0);
					$this->ConfigurationManager->DefineSetting('JQMEDIA_ALLOW_VIMEO', ForceIncomingBool('JQMEDIA_ALLOW_VIMEO', 0), 0);
					$this->ConfigurationManager->DefineSetting('JQMEDIA_ALLOW_DAILYMOTION', ForceIncomingBool('JQMEDIA_ALLOW_DAILYMOTION', 0), 0);
					$this->ConfigurationManager->DefineSetting('JQMEDIA_ALLOW_GOOGLE', ForceIncomingBool('JQMEDIA_ALLOW_GOOGLE', 0), 0);
					$this->ConfigurationManager->DefineSetting('JQMEDIA_ALLOW_MYSPACE', ForceIncomingBool('JQMEDIA_ALLOW_MYSPACE', 0), 0);
					$this->ConfigurationManager->DefineSetting('JQMEDIA_ALLOW_IFILM', ForceIncomingBool('JQMEDIA_ALLOW_IFILM', 0), 0);
					$this->ConfigurationManager->DefineSetting('JQMEDIA_ALLOW_BRIGHTCOVE', ForceIncomingBool('JQMEDIA_ALLOW_BRIGHTCOVE', 0), 0);
					$this->ConfigurationManager->DefineSetting('JQMEDIA_ALLOW_STAGE6', ForceIncomingBool('JQMEDIA_ALLOW_STAGE6', 0), 0);
					$this->ConfigurationManager->DefineSetting('JQMEDIA_ALLOW_REVVER', ForceIncomingBool('JQMEDIA_ALLOW_REVVER', 0), 0);


					if ($this->ConfigurationManager->SaveSettingsToFile($SettingsFile)) {
						header('Location: '.GetUrl($this->Context->Configuration, 'settings.php', '', '', '', '', 'PostBackAction=JQmedia&Success=1'));
					} else {
						$this->PostBackAction = 'JQmedia';
					}
				}
			}
			$this->CallDelegate('Constructor');
		}

		function Render() {
			if ($this->IsPostBack) {
				$this->CallDelegate('PreRender');
				$this->PostBackParams->Clear();
				if ($this->PostBackAction == 'JQmedia') {
					$this->PostBackParams->Set('PostBackAction', 'ProcessJQmedia');
					echo '
					<div id="Form" class="Account JQmediaSettings">';
					if (ForceIncomingInt('Success', 0)) echo '<div id="Success">'.$this->Context->GetDefinition('ChangesSaved').'</div>';
					echo '
						<fieldset>
							<legend>'.$this->Context->GetDefinition("JQmediaSettings").'</legend>
							'.$this->Get_Warnings().'
							'.$this->Get_PostBackForm('frmJQmedia').'
							<p>'.$this->Context->GetDefinition("JQmediaNotes").'</p>
							<ul>

								<li>
									<p><span>'.GetDynamicCheckBox('JQMEDIA_ALLOW_MP3', 1, $this->ConfigurationManager->GetSetting('JQMEDIA_ALLOW_MP3'), '', $this->Context->GetDefinition('JQmediaMp3')).'</span></p>
								</li>
								</ul>
								<br /><p><span>'.$this->Context->GetDefinition('JQmediaVideo').'</span></p>
								<ul>
								<li>
									<p><span>'.GetDynamicCheckBox('JQMEDIA_ALLOW_YOUTUBE', 1, $this->ConfigurationManager->GetSetting('JQMEDIA_ALLOW_YOUTUBE'), '', $this->Context->GetDefinition('JQmediaYoutube')).'</span></p>
								</li>
								<li>
									<p><span>'.GetDynamicCheckBox('JQMEDIA_ALLOW_VIMEO', 1, $this->ConfigurationManager->GetSetting('JQMEDIA_ALLOW_VIMEO'), '', $this->Context->GetDefinition('JQmediaVimeo')).'</span></p>
								</li>
								<li>
									<p><span>'.GetDynamicCheckBox('JQMEDIA_ALLOW_DAILYMOTION', 1, $this->ConfigurationManager->GetSetting('JQMEDIA_ALLOW_DAILYMOTION'), '', $this->Context->GetDefinition('JQmediaDailymotion')).'</span></p>
								</li>
								<li>
									<p><span>'.GetDynamicCheckBox('JQMEDIA_ALLOW_GOOGLE', 1, $this->ConfigurationManager->GetSetting('JQMEDIA_ALLOW_GOOGLE'), '', $this->Context->GetDefinition('JQmediaGoogle')).'</span></p>
								</li>
								<li>
									<p><span>'.GetDynamicCheckBox('JQMEDIA_ALLOW_MYSPACE', 1, $this->ConfigurationManager->GetSetting('JQMEDIA_ALLOW_MYSPACE'), '', $this->Context->GetDefinition('JQmediaMyspace')).'</span></p>
								</li>
								<li>
									<p><span>'.GetDynamicCheckBox('JQMEDIA_ALLOW_IFILM', 1, $this->ConfigurationManager->GetSetting('JQMEDIA_ALLOW_IFILM'), '', $this->Context->GetDefinition('JQmediaIfilm')).'</span></p>
								</li>
								<li>
									<p><span>'.GetDynamicCheckBox('JQMEDIA_ALLOW_BRIGHTCOVE', 1, $this->ConfigurationManager->GetSetting('JQMEDIA_ALLOW_BRIGHTCOVE'), '', $this->Context->GetDefinition('JQmediaBrightcove')).'</span></p>
								</li>
                                                               <li>
									<p><span>'.GetDynamicCheckBox('JQMEDIA_ALLOW_STAGE6', 1, $this->ConfigurationManager->GetSetting('JQMEDIA_ALLOW_STAGE6'), '', $this->Context->GetDefinition('JQmediaStage6')).'</span></p>
								</li>
                                                                <li>
									<p><span>'.GetDynamicCheckBox('JQMEDIA_ALLOW_REVVER', 1, $this->ConfigurationManager->GetSetting('JQMEDIA_ALLOW_REVVER'), '', $this->Context->GetDefinition('JQmediaRevver')).'</span></p>
								</li>
							</ul>
							<div class="Submit">
								<input type="submit" name="btnSave" value="'.$this->Context->GetDefinition('Save').'" class="Button SubmitButton" />
								<a href="'.GetUrl($this->Context->Configuration, $this->Context->SelfUrl).'" class="CancelButton">'.$this->Context->GetDefinition('Cancel').'</a>
							</div>
							</form>
						</fieldset>
					</div>
					';
				}
				$this->CallDelegate('PostRender');
			}
		}
	}

	$JQmediaForm = $Context->ObjectFactory->NewContextObject($Context, 'JQmediaForm');
	$Page->AddRenderControl($JQmediaForm, $Configuration["CONTROL_POSITION_BODY_ITEM"]);

	$ExtensionOptions = $Context->GetDefinition('ExtensionOptions');
	$Panel->AddList($ExtensionOptions, 20);
	$Panel->AddListItem($ExtensionOptions, $Context->GetDefinition('JQmedia'), GetUrl($Context->Configuration, 'settings.php', '', '', '', '', 'PostBackAction=JQmedia'));
}
?>
