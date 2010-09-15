<?php
class Constructions_Vanilla_aTab
{
	/**
	 * Renders extension's template.
	*/
        protected function renderTemplate($template_name, $data, $root_path)
        {
		// Fetch and render template
		require_once dirname(__FILE__).'/../../../vendors/templating/lib/sfTemplateAutoloader.php';
		sfTemplateAutoloader::register();

		$loader = new sfTemplateLoaderFilesystem($root_path.'/%name%.php');
		$engine = new sfTemplateEngine($loader);

		echo $engine->render($template_name, $data);
	}

	public function getPageTitle()
	{
		return __CLASS__;
	}

	public function getName()
	{
		return __CLASS__;
	}
}
