<?php
class Constructions_Extension
{
        private $tabs = array();
	private $allowed_users = array();
	private $templates_root;

        public function addTab(Constructions_Tab $tab, $post_back_action)
        {
                $this->tabs[$post_back_action] = $tab;
        }

        public function handleRequest(Context $context, Page $page, array $configuration)
        {
		if (in_array($context->Session->UserID, $this->allowed_users))
		{
			$post_back_action = ForceIncomingString('PostBackAction', '');
			if (isset($this->tabs[$post_back_action]))
			{
				$tab = $this->tabs[$post_back_action];
				$tab->execute();
				$page->AddRenderControl($tab, $configuration["CONTROL_POSITION_BODY_ITEM"]);
			}
		}
        }

	public function setAllowedUsers(array $user_ids)
	{
		$this->allowed_users = $user_ids;
	}

	public function setTemplatesRoot($root_dir)
	{
		$this->templates_root = $root_dir;
	}
}
