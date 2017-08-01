<?php

/**
 */
class DiscussionType_EventModel extends Gdn_Model
{
	protected static $instance;

	/**
	 * Class constructor. Defines the related database table name.
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function __construct() {
		parent::__construct('Event');
	}

	/**
	 * The singleton instance of this object.
	 *
	 * @return DiscussionType_EventModel
	 */
	public static function instance() {
		if (!isset(self::$instance)) {
			self::$instance = new DiscussionType_EventModel();
		}
		return self::$instance;
	}

	/**
	 * Inserts or updates event.
	 *
	 * @param array $FormPostValues
	 */
	public function save(array $FormPostValues)
	{
		// Set database schema
		$this->defineSchema();

		// Define input validation rules
		$this->Validation->applyRule('StartDate', 'Required');
		$this->Validation->applyRule('StartDate', 'Date');
		$this->Validation->applyRule('EndDate', 'Date');
		$this->Validation->applyRule('City', 'Required');
		$this->Validation->applyRule('Country', 'Required');
		$this->Validation->applyRule('Url', 'Url');
		$this->Validation->applyRule('DiscussionID', 'Required');

		// Remove prefix from posted values names
		// TODO : this seems a bit weird, i must have misunderstood something
		array_walk($FormPostValues, function (&$item, $key) use (&$CleanPostValues) {
			$key = str_replace('DiscussionType_Event_', '', $key);
			if ($item) {
				$CleanPostValues[$key] = $item;
			}
		});

		// Validate the form posted values
		if ($this->validate($CleanPostValues)) {
			$Fields = $this->Validation->SchemaValidationFields();

			// Insert or update event
			$this->SQL->replace($this->Name, $Fields, ['DiscussionID' => $Fields['DiscussionID']]);
		}
	}
}