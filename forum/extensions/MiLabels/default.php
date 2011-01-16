<?php
/*
 Extension Name: MiLabels
 Extension Url: https://github.com/contructions-incongrues
 Description: Displays list of labels forums associated with the project.
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */

// Configure "Labels" page rendering
if(in_array(ForceIncomingString("PostBackAction", ""), array('Labels'))) {
	$Context->PageTitle = 'Labels - Musiques Incongrues';
	$Menu->CurrentTab = 'Labels';
	$Body->CssClass = 'Discussions';
	$Page->AddRenderControl(new MiLabelsPage($Context, $Head), $Configuration["CONTROL_POSITION_BODY_ITEM"]);
}

// Modify discussion grid when in a label category
$idsLabels = array(MiLabelsDatabasePeer::LABEL_DHR, MiLabelsDatabasePeer::LABEL_EGOTWISTER);
if ($Context->SelfUrl == 'index.php' && in_array(ForceIncomingInt('CategoryID', null), $idsLabels)) {
	// Show label header
	$Context->AddToDelegate('DiscussionGrid', 'PreRender', 'MiLabel_RenderGridHeader');
	
	// Update sidebar
	if (isset($Panel)) {

		// Fetch latest sticky for show
		$dbStickies = MiLabelsDatabasePeer::getStickies(ForceIncomingInt('CategoryID', null), $Context);
		$dbSticky = $dbStickies[0];
		
		// Grab associated release
		$dbRelease = MiLabelsDatabasePeer::getRelease($dbSticky['DiscussionID'], $Context);
		
		if (count($dbStickies)) {
			$tplStickies = <<<EOT
	<h2>Dernièrement</h2>
	
	<p class="label-box-name-show">
		<a href="%s" title="%s">%s</a>
	</p>
	
	<p class="label-box-artwork">
		<a href="%s" title="%s">
			<img src="%s" />
		</a>
	</p>
EOT;
			// Add download link, if appropriate
			if ($dbRelease['DownloadLink']) {
				$tplStickies .= '<p class="label-donwload-release"><a href="%s" title="Télécharger">Télécharger la release</a></p>';
			}
			
			$Panel->addString(sprintf(
				$tplStickies, 
				GetUrl($Context->Configuration, 'comments.php', '', 'DiscussionID', $dbSticky['DiscussionID'], '', '#Item_1', CleanupString($dbSticky['Name']).'/'),
				$dbSticky['Name'], 
				$dbSticky['Name'],
				GetUrl($Context->Configuration, 'comments.php', '', 'DiscussionID', $dbSticky['DiscussionID'], '', '#Item_1', CleanupString($dbSticky['Name']).'/'),
				$dbSticky['Name'],
				getFirstImageUrl($dbSticky['DiscussionID']),
				$dbRelease['DownloadLink']
			));
		}
		
		// Fetch current label
		$label = MiLabelsDatabasePeer::getLabels(array(ForceIncomingInt('CategoryID', null)), $Context);
		$label = $label[0];
		$Panel->addString(utf8_encode($label['SidebarHtml']));
	}
}

// Add extension stylesheet
$Head->AddStyleSheet('extensions/MiLabels/css/style.css');

class MiLabelsPage
{
	private $context;

	public function __construct(Context $context, Head $head)
	{ 
		// Store context
		$this->context = $context;
	}

	public function render()
	{
		// Fetch labels
		$labels = MiLabelsDatabasePeer::getLabels(array(MiLabelsDatabasePeer::LABEL_DHR, MiLabelsDatabasePeer::LABEL_EGOTWISTER), $this->context);
		$strLabels = $this->renderLabels($labels);
		
		// Fetch parent category
		$parentCategory = MiLabelsDatabasePeer::getCategories(array(MiLabelsDatabasePeer::$parent_id), $this->context);
			
		$html = <<<EOT
<div id="ContentBody" class="releases">
	<h2 class="top-title-category-label" id="5">Labels</h2> 
	<p class="top-title-category-label-legend">%s</p>
	<ol id="Discussions">
		<li class="Discussion Release">
			%s
		</li>
	</ol>
</div>		
EOT;

		echo sprintf($html, $parentCategory[0]['Description'], $strLabels);
	}

	/**
	 * @param array $labels
	 * 
	 * TODO : use CategoryUri to generate pretty urls. Must also amend .htaccess
	 */
	private function renderLabels(array $labels)
	{
		$tplLabel = <<<EOT
			<dl class="subCategory">
				<span class="subcategory-pictures">
					<img width="150px;" height="90px;" src="%s" />
				</span>
				<dd class="category-infos">
					<a href="%s%s/">%s</a> | 
					<span class="category-label-read">
						<a href="%s%s/">Accéder au forum</a>
					</span>
 					<br />
				</dd>
				<dt class="category-legend">
					%s
				</dt>
				<br />
				<span class="category-label-url">
					<a href="%s">%s</a>
				</span>
			</dl>
EOT;

		// Render
		$strLabels = array();
		foreach ($labels as $label) {
			$strLabels[] = sprintf(
				$tplLabel, 
				$label['ImageUrl'],
				$this->context->Configuration['WEB_ROOT'],
				$label['CategoryID'],
				$label['Name'],
				$this->context->Configuration['WEB_ROOT'], 
				$label['CategoryID'], 
				$label['Description'], 
				$label['WebsiteUrl'],
				$label['WebsiteUrl']
			);
		}

		return implode("\n", $strLabels);
	}
}

class MiLabelsDatabasePeer
{
	const LABEL_DHR = 3;
	const LABEL_EGOTWISTER = 9;
	public static $parent_id = 16;
	
	public static function getLabels(array $ids, Context $context)
	{
		// Build selection query
		$sql = $context->ObjectFactory->NewContextObject($context, 'SqlBuilder');
		$sql->SetMainTable('Label','l');
		$sql->AddJoin('Category', 'c', 'CategoryID', 'l', 'CategoryID', 'INNER JOIN');
		$sql->addSelect('CategoryID', 'l');
		$sql->addSelect('Name', 'c');
		$sql->addSelect('Description', 'c');
		$sql->addSelect('WebsiteUrl', 'l');
		$sql->addSelect('ImageUrl', 'l');
		$sql->addSelect('CategoryUri', 'l');
		$sql->addSelect('SidebarHtml', 'l');
		$sql->AddOrderBy('Name', 'c', 'ASC');
		foreach ($ids as $id) {
			$sql->AddWhere('c', 'CategoryID', '', $id, '=', 'OR');
		}
		
		// Execute query
		$db = $context->Database;
		$rs = $db->Execute($sql->GetSelect(), $context, __FUNCTION__, 'Failed to fetch labels from database.');

		// Gather and return events
		$labels = array();
		if ($db->RowCount($rs) > 0)
		{
			while($db_label = $db->GetRow($rs))
			{
				$db_label['Name'] = substr($db_label['Name'], 2);
				$labels[] = $db_label;
			}
		}

		return $labels;
	}
	
	public static function getCategories(array $ids, Context $context)
	{
		// Build selection query
		$sql = $context->ObjectFactory->NewContextObject($context, 'SqlBuilder');
		$sql->SetMainTable('Category','c');
		$sql->addSelect('CategoryID', 'c');
		$sql->addSelect('Name', 'c');
		$sql->addSelect('Description', 'c');
		$sql->AddOrderBy('Name', 'c', 'ASC');
		foreach ($ids as $id) {
			$sql->AddWhere('c', 'CategoryID', '', $id, '=', 'OR');
		}

		// Execute query
		$db = $context->Database;
		$rs = $db->Execute($sql->GetSelect(), $context, __FUNCTION__, 'Failed to fetch categories from database.');

		// Gather and return categories
		$categories = array();
		if ($db->RowCount($rs) > 0)
		{
			while($db_category = $db->GetRow($rs))
			{
				$categories[] = $db_category;
			}
		}

		return $categories;
	}	
	
	public static function getRelease($discussionId, Context $context)
	{
		// Build selection query
		$sql = $context->ObjectFactory->NewContextObject($context, 'SqlBuilder');
		$sql->SetMainTable('Releases','r');
		$sql->addSelect('DownloadLink', 'r');
		$sql->AddWhere('r', 'DiscussionID', '', $discussionId, '=');
		
		// Execute query
		$db = $context->Database;
		$rs = $db->Execute($sql->GetSelect(), $context, __FUNCTION__, 'Failed to fetch release from database.');

		return $db->GetRow($rs);
	}

	public static function getStickies($categoryId, Context $context)
	{
		// Build selection query
		$sql = $context->ObjectFactory->NewContextObject($context, 'SqlBuilder');
		$sql->SetMainTable('Discussion','d');
		$sql->addSelect('Name', 'd');
		$sql->addSelect('DiscussionID', 'd');
		$sql->AddOrderBy('DateLastActive', 'd', 'DESC');
		$sql->AddWhere('d', 'CategoryID', '', $categoryId, '=');
		$sql->AddWhere('d', 'Sticky', '',  1, '=', 'AND');

		// Execute query
		$db = $context->Database;
		$rs = $db->Execute($sql->GetSelect(), $context, __FUNCTION__, 'Failed to fetch stickies from database.');

		// Gather and return stickies
		$stickies = array();
		if ($db->RowCount($rs) > 0)
		{
			while($db_sticky = $db->GetRow($rs))
			{
				$stickies[] = $db_sticky;
			}
		}

		return $stickies;
	}
}

function MiLabel_RenderGridHeader(DiscussionGrid $grid)
{
	// Fetch current label
	$label = MiLabelsDatabasePeer::getLabels(array(ForceIncomingInt('CategoryID', null)), $grid->Context);
	$label = $label[0];
	
	// Render template
	$tplLabel = <<<EOT
<ol id="Discussions"> 
	<li class="Discussion Release"> 
		<dl class="subCategory"> 
			<span class="subcategory-pictures"> 
				<img width="150px;" height="90px;" src="%s" /> 
			</span> 
			<dd class="category-infos"> 
				<a href="%s%s">%s</a> 
			</dd> 
			<dt class="category-legend"> 
				%s
			</dt> 
			<br /> 
			<span class="category-label-url"> 
				<a href="%s">%s</a> 
			</span> 
		</dl> 
	</li>
	<hr style="height: 8px;" />
</ol>
EOT;

	echo sprintf(
		$tplLabel, 
		$label['ImageUrl'],
		$grid->Context->Configuration['WEB_ROOT'],
		$label['CategoryID'],
		$label['Name'],
		$label['Description'], 
		$label['WebsiteUrl'],
		$label['WebsiteUrl']
	);
}