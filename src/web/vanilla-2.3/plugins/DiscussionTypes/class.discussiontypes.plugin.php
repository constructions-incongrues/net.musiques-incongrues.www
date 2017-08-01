<?php if (!defined('APPLICATION')) exit;

$PluginInfo['DiscussionTypes'] = [
	'Author'             => "Constructions Incongrues",
	'AuthorEmail'        => 'contact@constructions-incongrues.net',
	'AuthorUrl'          => 'http://www.constructions-incongrues.net',
	'License'            => 'GPLv3',
	'MobileFriendly'     => true,
	'Name'               => "Discussion Types",
	'SettingsPermission' => 'Garden.Settings.Manage',
	'SettingsUrl'        => '/settings/discussiontypes',
	'Version'            => '1.0.0',
];

/**
 * Discussion Types Plugin
 */
class DiscussionTypesPlugin extends Gdn_Plugin
{
	/**
	 * This will run when you "Enable" the plugin
	 *
	 * @return bool
	 */
	public function setup()
	{
		return true;
	}

	/**
	 * Render form options for each activated discussion type.
	 *
	 * @param PostController $sender
	 * @return void.
	 */
	public function PostController_DiscussionFormOptions_Handler(PostController $sender, $args)
	{
		// Get discussion types instances related to current discussion
		$sender->Data['DiscussionTypes'] = [];
		foreach ($this->getAvailableDiscussionTypesPlugins() as $pluginSpec) {
			// Get instance
			$typeName = $pluginSpec['DiscussionTypes']['Name'];
			$typeInstance = call_user_func(
				sprintf('DiscussionType_%sModel::instance', $typeName)
			)->GetWhere(['DiscussionID' => $sender->Data['Discussion']->DiscussionID]
			)->FirstRow(DATASET_TYPE_ARRAY);

			// Make instance accessible to view
			$sender->Data['DiscussionTypes'][$typeName] = $typeInstance;
		}

		// Render form options
		echo $this->fetchTypesView('postcontroller/formoptions', $sender->Data['Discussion']->DiscussionID, $sender);
	}

	/**
	 * Displays types metadata alongside discussion metadata in discussions list.
	 *
	 * @param DiscussionsController $sender
	 * @param mixed $args
	 */
	public function DiscussionsController_DiscussionMeta_Handler(DiscussionsController $sender, $args)
	{
		echo $this->fetchTypesView('discussionscontroller/metas', $args['Discussion']->DiscussionID, $sender);
	}

	/**
	 * Displays types metadata alongside discussion metadata in discussion view.
	 *
	 * @param DiscussionController $sender
	 * @param $args
	 */
	public function DiscussionController_DiscussionInfo_Handler(DiscussionController $sender, $args)
	{
		echo $this->fetchTypesView('discussioncontroller/discussioninfo', $args['Discussion']->DiscussionID, $sender);
	}

	/**
	 * Save discussion types additional properties to database.
	 *
	 * @param DiscussionModel $sender
	 */
	public function DiscussionModel_AfterSaveDiscussion_Handler(DiscussionModel $sender)
	{
		// Extract useful values
		$FormPostValues = val('FormPostValues', $sender->EventArguments, []);
		$DiscussionID   = val('DiscussionID', $sender->EventArguments);
		$FormPostValues['DiscussionID'] = $DiscussionID;

		// Guess from which discussion types comes the input data
		$types = $this->extractDiscussionTypes($FormPostValues);

		// Save data using appropriate model classes
		foreach ($types as $type) {
			call_user_func(sprintf('DiscussionType_%sModel::instance', $type))->save($FormPostValues);
		}
	}

	/**
	 * Adds discussion type class name as CSS class if appropriate.
	 *
	 * @param DiscussionsController $sender
	 * @param $args
	 */
	public function DiscussionsController_BeforeDiscussionName_Handler(DiscussionsController $sender, $args)
	{
		foreach ($this->getAvailableDiscussionTypesPlugins() as $pluginSpec) {
			$typeName = $pluginSpec['DiscussionTypes']['Name'];
			/** @var Gdn_Model $model */
			$model = call_user_func(sprintf('DiscussionType_%sModel::instance', $typeName));
			if ($model->GetWhere(['DiscussionID' => $sender->EventArguments['Discussion']->DiscussionID])->count()) {
				$args['CssClass'] .= ' '.$pluginSpec['Index'];
			}
		}
	}

	/**
	 * Extracts distinct discussion types names a key value array similar to what you get from Vanilla forms.
	 *
	 * @param array $FormPostValues
	 *
	 * @return array List of discussion types
	 */
	private function extractDiscussionTypes(array $FormPostValues)
	{
		$types = [];
		foreach (array_keys($FormPostValues) as $key) {
			$matches = [];
			if (preg_match('/^DiscussionType_(\w+)_.+$/', $key, $matches)) {
				$types[] = $matches[1];
			}
		}

		return array_unique($types);
	}

	/**
	 * Returns the list of compatible plugins.
	 *
	 * @return array|ø
	 */
	private function getAvailableDiscussionTypesPlugins()
	{
		// TODO : it would be more elegant to use array_filter
		$available = [];
		foreach (Gdn::pluginManager()->availablePlugins() as $name => $spec) {
			if (strpos($name, 'DiscussionType_') === 0 && isset($spec['DiscussionTypes'])) {
				$available[$name] = $spec;
			}
		}

		return $available;
	}

	/**
	 * Renders a same view for all activated discussion type plugins.
	 *
	 * @param string $view
	 * @param int $discussionId
	 * @param Gdn_Pluggable $sender
	 *
	 * @return string
	 */
	private function fetchTypesView($view, $discussionId, Gdn_Pluggable $sender) {
		$out = '';
		$sender->Data['DiscussionTypes'] = [];
		foreach ($this->getAvailableDiscussionTypesPlugins() as $pluginSpec) {
			// If we are are in the context of a discussion, try to fetch the corresponding discussion type instance
			if (isset($discussionId)) {
				$typeModelClass = sprintf('%sModel', $pluginSpec['Index']);
				$typeName = $pluginSpec['DiscussionTypes']['Name'];
				$model = call_user_func(sprintf('%s::instance', $typeModelClass));
				$discussionTypeInstance = $model->GetWhere(['DiscussionID' => $discussionId]);
				if ($discussionTypeInstance->count()) {
					$sender->Data['DiscussionTypes'][$typeName] = $discussionTypeInstance->firstRow(DATASET_TYPE_ARRAY);
				} else {
					unset($sender->Data['DiscussionTypes'][$typeName]);
				}
			}

			// Render requested view
			$viewPath = sprintf( '%s/views/%s.php', $pluginSpec['PluginRoot'], $view );
			$out .= $sender->fetchView($viewPath);
		}

		return $out;
	}
}
