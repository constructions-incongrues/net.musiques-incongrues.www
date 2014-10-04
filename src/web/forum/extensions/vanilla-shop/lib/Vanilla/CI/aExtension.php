<?php
class Vanilla_CI_aExtension
{
	/**
	 * @var Context
	 */
	public $context;

	/**
	 * @var array
	 */
	public $script_matches;

	public function __construct(Context $context)
	{
		$this->context = $context;
	}

	public function dispatch()
	{
		// Only render extension when appropriate
	}

	public function setupDelegates(array $delegates_specs)
	{
		foreach ($delegates_specs as $class_name => $delegate_spec)
		{
			$this->context->AddToDelegate($class_name, "DiscussionForm_PreCommentRender", array($this, $delegate_spec[0]));
		}
	}

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
}
