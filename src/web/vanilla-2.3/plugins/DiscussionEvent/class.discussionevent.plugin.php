<?php if (!defined('APPLICATION')) exit();

$PluginInfo['DiscussionEvent'] = array(
	'Name' => 'Discussion Event',
	'Description' => 'Adds an event date field to new discussions and provides a list of upcoming events.',
	'Version' => '0.3',
	'RequiredApplications' => array('Vanilla' => '2.1'),
	'MobileFriendly' => true,
	'HasLocale' => true,
	'SettingsUrl' => '/settings/discussionevent',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'License' => 'MIT',
	'Author' => "Martin Tschirsich",
	'AuthorEmail' => 'm.tschirsich@gmx.de'
);

class DiscussionEventPlugin extends Gdn_Plugin {
	public static $ApplicationFolder = 'plugins/DiscussionEvent';
	
	public function __construct($Sender='') {
		parent::__construct($Sender, self::$ApplicationFolder);
	}
	
	public function Base_Render_before($Sender) {
		if (C('Plugins.DiscussionEvent.DisplayInSidepanel')) {
			// only add the module if we are in the panel asset and NOT in the dashboard
			if (getValue('Panel', $Sender->Assets) && $Sender->MasterView != 'admin') {
				$DiscussionEventModule = new DiscussionEventModule($Sender);
				$Sender->addModule($DiscussionEventModule);
			}
		}
	}
	
	public function SettingsController_DiscussionEvent_create($Sender) {
		$Sender->permission('Garden.Settings.Manage');
		
		$Sender->Form = new Gdn_Form();
		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$ConfigurationModel->setField(array(
			'Plugins.DiscussionEvent.DisplayInSidepanel',
			'Plugins.DiscussionEvent.MaxDiscussionEvents'
		));
		$Sender->Form->setModel($ConfigurationModel);
		
		if ($Sender->Form->authenticatedPostBack()) {
			if ($Sender->Form->save() !== false) {
				$Sender->informMessage(sprite('Check', 'InformSprite').T('Your changes have been saved.'), 'Dismissable AutoDismiss HasSprite');
			}
		} else {
			$Sender->Form->setData($ConfigurationModel->Data);
		}
				
		$Sender->title(T('Discussion Event Settings'));
		$Sender->addSideMenu();
		$Sender->render($this->GetView('settings.php'));
	}
	
	public function PostController_beforeBodyInput_handler($Sender) {
		$Sender->addJsFile('discussionevent.js', self::$ApplicationFolder);
		
		if (!$Sender->Form->authenticatedPostBack()) {
			
			if ($Sender->Discussion->DiscussionEventDate) {
				$Sender->Form->setValue('DiscussionEventCheck', true);
				$Sender->Form->setValue('DiscussionEventDate', $Sender->Discussion->DiscussionEventDate);
			} else {
				$Sender->Form->setValue('DiscussionEventCheck', false);
				$Sender->Form->setValue('DiscussionEventDate',  Gdn_Format::date('Y-m-d'));
			}
		}
		
		$Year = Date('Y');
		$YearRange = $Year.'-'.($Year + 3);
		
		echo '<div class="P"><div class="DiscussionEvent">';
		echo $Sender->Form->checkBox('DiscussionEventCheck', 'Is an event?');
		echo '<div class="DiscussionEventDate"><div class="P">';
		echo $Sender->Form->label('Date', 'DiscussionEventDate'), ' ';
		echo $Sender->Form->date('DiscussionEventDate', array('YearRange' => $YearRange, 'fields' => array('day', 'month', 'year')));
		echo '</div></div></div></div>';
	}
	
	public function DiscussionModel_beforeSaveDiscussion_handler($Sender) {
		if ($Sender->EventArguments['FormPostValues']['DiscussionEventCheck']) {
			$Sender->Validation->applyRule('DiscussionEventDate', 'Required', T('Please enter an event date.'));
			$Sender->Validation->applyRule('DiscussionEventDate', 'Date', T('The event date you\'ve entered is invalid.'));
		} else {
			$Sender->EventArguments['FormPostValues']['DiscussionEventDate'] = null;
		}
	}
	
	public function DiscussionController_afterDiscussionTitle_handler($Sender, $Args) {		
		$EventDate = $Sender->EventArguments['Discussion']->DiscussionEventDate;
		self::displayEventDate($EventDate);
	}
	
	public function DiscussionsController_afterDiscussionTitle_handler($Sender) {
		$EventDate = $Sender->EventArguments['Discussion']->DiscussionEventDate;
		self::displayEventDate($EventDate);
	}
	
	public function CategoriesController_afterDiscussionTitle_handler($Sender) {
		$EventDate = $Sender->EventArguments['Discussion']->DiscussionEventDate;
		self::displayEventDate($EventDate);
	}
	
	public function structure() {
		$Structure = Gdn::structure();
		$Structure->table('Discussion')
			->column('DiscussionEventDate', 'date', true, 'index')
			->set();
	}
	
	public function setup() {
		$this->structure();
		
		// Compatibility: Update from version <= 0.2
		Gdn::sql()->update('Discussion d')
			->set('d.DiscussionEventDate', null)
			->where('d.DiscussionEventDate', '0000-00-00')
			->put();
		
		saveToConfig('Plugins.DiscussionEvent.DisplayInSidepanel', true);
		saveToConfig('Plugins.DiscussionEvent.MaxDiscussionEvents', 10);
	}
	
	public function onDisable() {
		removeFromConfig('Plugins.DiscussionEvent.DisplayInSidepanel');
		removeFromConfig('Plugins.DiscussionEvent.MaxDiscussionEvents');
	}
	
	public static function displayEventDate($EventDate) {
		if ($EventDate) {
			echo '<div class="DiscussionEventDate icon icon-calendar"> '.Gdn_Format::date($EventDate, 'html').'</div>';
		}
	}
}