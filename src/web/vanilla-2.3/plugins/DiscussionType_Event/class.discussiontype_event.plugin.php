<?php if (!defined('APPLICATION')) exit;

$PluginInfo['DiscussionType_Event'] = array(
    'Name'            => "Discussion Type: Event",
    'Version'         => '1.0.0',
    'Author'          => "Constructions Incongrues",
    'AuthorEmail'     => 'contact@constructions-incongrues.net',
    'License'         => 'GPLv3',
	'DiscussionTypes' => ['Name' => 'Event']
);

/**
 * Discussion Type : Event Plugin
 */
class DiscussionTypeEventPlugin extends Gdn_Plugin {
	/**
	 * This will run when you "Enable" the plugin
	 *
	 * @return bool
	 */
	public function setup() {
		// Update database
		$this->structure();

		return true;
	}

	/**
	 * Migration.
	 *
	 * @return void.
	 */
	public function structure() {
		Gdn::structure()->table( 'Event' )
		   ->primaryKey( 'EventID' )
		   ->column( 'DiscussionID', 'int', false, 'unique' )
		   ->column( 'StartDate', 'date', false )
		   ->column( 'EndDate', 'date', true )
		   ->column( 'Country', 'varchar(255)', false )
		   ->column( 'City', 'varchar(255)', false )
		   ->column( 'Venue', 'varchar(255)', true )
		   ->column( 'Url', 'varchar(255)', true )
		   ->set( false, false );
	}

	/**
	 * Check permission and validate event input.
	 *
	 * @param DiscussionModel $sender DiscussionModel.
	 * @param mixed $args EventArguments.
	 *
	 * @return void.
	 */
	public function DiscussionModel_BeforeSaveDiscussion_Handler(DiscussionModel $sender, $args)
	{
		// Setup event field validation if appropriate
		if ( isset( $args['FormPostValues']['DiscussionType_Event_IsEvent'] ) ) {
			$sender->Validation->applyRule(
				'DiscussionType_Event_StartDate',
				'Required',
				Gdn::translate( 'DiscussionType.Event.StartDate.Required', 'Event : start date is required' )
			);
			$sender->Validation->applyRule(
				'DiscussionType_Event_StartDate',
				'Date',
				Gdn::translate( 'DiscussionType.Event.StartDate.Date', 'Event : start date must be a date' )
			);
			$sender->Validation->applyRule(
				'DiscussionType_Event_EndDate',
				'Date',
				Gdn::translate( 'DiscussionType.Event.EndDate.Date', 'Event : end date must be a date' )
			);
			$sender->Validation->applyRule(
				'DiscussionType_Event_City',
				'Required',
				Gdn::translate( 'DiscussionType.Event.City.Required', 'Event : city is required' )
			);
			$sender->Validation->applyRule(
				'DiscussionType_Event_Country',
				'Required',
				Gdn::translate( 'DiscussionType.Event.Country.Required', 'Event : country is required' )
			);
			$sender->Validation->applyRule(
				'DiscussionType_Event_Url',
				'Url',
				Gdn::translate( 'DiscussionType.Event.Url.Url', 'Event : Url must be valid' )
			);
		}
	}
}
