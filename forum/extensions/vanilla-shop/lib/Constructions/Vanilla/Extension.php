<?php
class Constructions_Vanilla_Extension
{
	private $tabs = array();

	public function addTab(Constructions_Vanilla_Extension_aTab $tab, $post_back_action)
	{
		$this->bodies[$post_back_action] = $body;
	}

	public function dispatch(Context $context, Page $page, array $configuration)
	{
		$post_back_action = ForceIncomingString('PostBackAction', '');
		if (isset($this->tabs[$post_back_action]))
		{
			$tab = $this->tabs[$post_back_action];
			$context->PageTitle = $tab->getPageTitle();
			$menu->CurrentTab = $tab->getName();
			$page->AddRenderControl($tab, $configuration["CONTROL_POSITION_BODY_ITEM"]);
		}
	}
}
