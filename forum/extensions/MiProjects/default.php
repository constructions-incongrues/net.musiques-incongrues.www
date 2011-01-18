<?php
/*
 Extension Name: MiProjects
 Extension Url: https://github.com/contructions-incongrues
 Description: Displays list of projects forums associated with the project.
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */

// Configure "Shows" page rendering
$postBackAction = ForceIncomingString("PostBackAction", "");
if(in_array($postBackAction, array('Shows', 'Labels'))) {
	$Context->PageTitle = sprintf('%s - Musiques Incongrues', $postBackAction);
	$Menu->CurrentTab = $postBackAction;
	$Body->CssClass = 'Discussions';
	$Page->AddRenderControl(new MiProjectPage($Context, $Head, strtolower($postBackAction)), $Configuration["CONTROL_POSITION_BODY_ITEM"]);
}

// Modify discussion grid when in a project category
$idsProjectsCategories = array();

$idsProjectsCategories = array_merge(
	MiProjectsDatabasePeer::$categoryMappings['shows']['ids'], 
	MiProjectsDatabasePeer::$categoryMappings['labels']['ids']
);

$requestedCategoryId = ForceIncomingInt('CategoryID', null);

if (($Context->SelfUrl == 'index.php' && in_array($requestedCategoryId, $idsProjectsCategories)) || in_array($postBackAction, array('Labels', 'Shows'))) {

	$Context->AddToDelegate('DiscussionGrid', 'PreRender', 'MiProject_RenderGridHeader');
	
	// Update sidebar
	if (isset($Panel)) {

		// Fetch latest stickies for show
		$categoriesForStickies = array($requestedCategoryId);
		if (in_array($postBackAction, array('Labels', 'Shows'))) {
			$categoriesForStickies = MiProjectsDatabasePeer::$categoryMappings[strtolower($postBackAction)]['ids'];
		}
		
		$dbStickies = MiProjectsDatabasePeer::getStickies($categoriesForStickies, $Context);

		if (count($dbStickies)) {
			$Panel->AddString('<h2>Dernièrement</h2>');
		}
		
		foreach ($dbStickies as $dbSticky) {
		
			// Grab associated release
			$dbRelease = MiProjectsDatabasePeer::getRelease($dbSticky['DiscussionID'], $Context);
		
			$tplStickies = <<<EOT
	<a href="%s" title="%s">
		<img src="%s" width="200px" height="135px" class="emissions-box-cover" style="opacity: 0.5;"/>
	</a>
	<p class="emissions-box-name-show"><a href="%s" title="%s">%s</a></p>
EOT;
			// Add download link, if appropriate
			if ($dbRelease['DownloadLink']) {
				// Get download link extension
				$urlParts = explode('.', $dbRelease['DownloadLink']);
				$extension = array_pop($urlParts);
				if ($extension == 'mp3') {
					$tplStickies .= '<br /><p class="emissions-box-player"><a href="%s" title="Écouter">Écouter</a></p>';
				} else {
					$tplStickies .= '<br /><p class="emissions-box-player"><a href="%s" title="Télécharger">Télécharger</a></p>';
				}
				
			}
			
			// Setup autoloading
			require_once 'Zend/Loader/Autoloader.php';
			Zend_Loader_Autoloader::getInstance();
			
			// Instanciate and configure cache handler
			$cache = Zend_Cache::factory('Function', 'File');
			
			$Panel->addString(sprintf(
				$tplStickies.'<hr />', 
				GetUrl($Context->Configuration, 'comments.php', '', 'DiscussionID', $dbSticky['DiscussionID'], '', '#Item_1', CleanupString($dbSticky['Name']).'/'),
				$dbSticky['Name'], 
				$cache->call('getFirstImageUrl', array($dbSticky['DiscussionID'])),
				GetUrl($Context->Configuration, 'comments.php', '', 'DiscussionID', $dbSticky['DiscussionID'], '', '#Item_1', CleanupString($dbSticky['Name']).'/'),
				$dbSticky['Name'],
				truncate_text($dbSticky['Name'], 25),
				$dbRelease['DownloadLink']
			));
		}
		
		// Show video from first sticky in sidebar
		if (count($dbStickies) && !in_array($postBackAction, array('labels', 'shows'))) {
			$urlsVideos = getVideosUrls($dbStickies[0]['DiscussionID']);
			if (count($urlsVideos)) {
				$Panel->AddString('<h2>Images animées</h2>');
			}
			foreach ($urlsVideos as $urlVideo) {
				$matches = array();
				preg_match('|http://www.youtube.com/watch\?v=(.+)\??.*|', $urlVideo, $matches);
				$idVideo = $matches[1];
				if ($idVideo) {
					$panelVideo = <<<EOT
		<object width="197" height="135">
			<param name="movie" value="http://www.youtube.com/v/%s?fs=1&amp;hl=fr_FR&amp;color1=0xcc2550&amp;color2=0xe87a9f"></param>
			<param name="allowFullScreen" value="true"></param>
			<param name="allowscriptaccess" value="always"></param>
			<embed src="http://www.youtube.com/v/%s?fs=1&amp;hl=fr_FR&amp;color1=0xcc2550&amp;color2=0xe87a9f" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="197" height="135"></embed>
		</object>
		<br />
EOT;
					$Panel->AddString(sprintf($panelVideo, $idVideo, $idVideo));
				}
			}
		}
		
		// Inject custom sidebar contents
		$show = MiProjectsDatabasePeer::getProjects(array(ForceIncomingInt('CategoryID', null)), $Context);
		if (count($show)) {
			$Panel->addString(utf8_encode($show[0]['SidebarHtml']));
		}
	}
}

// Add extension stylesheet
$Head->AddStyleSheet('extensions/MiProjects/css/style.css');

class MiProjectPage
{
	private $context;
	private $type;

	public function __construct(Context $context, Head $head, $type)
	{ 
		// Store context
		$this->context = $context;
		
		// Store type
		$this->type = $type;
	}

	public function render()
	{
		// Fetch projects
		$projects = MiProjectsDatabasePeer::getProjects(MiProjectsDatabasePeer::$categoryMappings[$this->type]['ids'], $this->context);
		$strProjects = $this->renderProjects($projects);
		
		// Fetch parent category
		$parentCategory = MiProjectsDatabasePeer::getCategories(array(MiProjectsDatabasePeer::$categoryMappings[$this->type]['parent']), $this->context);
		
		$html = <<<EOT
<div id="ContentBody" class="releases">
	<h2 class="top-title-category-label" id="5">%s</h2> 
	<p  class="top-title-category-label-legend">%s</p>
	<ol id="Discussions">
		<li class="Discussion Release">
			%s
		</li>
	</ol>
</div>		
EOT;

		echo sprintf($html, ucfirst($this->type), $parentCategory[0]['Description'], $strProjects);
	}

	/**
	 * @param array $projects
	 * 
	 * TODO : use CategoryUri to generate pretty urls. Must also amend .htaccess
	 */
	private function renderProjects(array $projects)
	{
		$tplProject = <<<EOT
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
		$strProjects = array();
		foreach ($projects as $project) {
			$strProjects[] = sprintf(
				$tplProject, 
				$project['ImageUrl'],
				$this->context->Configuration['WEB_ROOT'],
				$project['CategoryID'],
				$project['Name'],
				$this->context->Configuration['WEB_ROOT'], 
				$project['CategoryID'], 
				$project['Description'], 
				$project['WebsiteUrl'],
				$project['WebsiteUrl']
			);
		}

		return implode("\n", $strProjects);
	}
}

class MiProjectsDatabasePeer
{
	public static $categoryMappings = array(
		'shows' => array(
			'ids'    => array(2, 10, 12, 20, 21),
			'parent' => 18),
		'labels' => array(
			'ids'    => array(3, 9),
			'parent' => 16)
	);
	
	public static function getProjects(array $ids, Context $context)
	{
		// Build selection query
		$sql = $context->ObjectFactory->NewContextObject($context, 'SqlBuilder');
		$sql->SetMainTable('Project','s');
		$sql->AddJoin('Category', 'c', 'CategoryID', 's', 'CategoryID', 'INNER JOIN');
		$sql->addSelect('CategoryID', 's');
		$sql->addSelect('Name', 'c');
		$sql->addSelect('Description', 'c');
		$sql->addSelect('WebsiteUrl', 's');
		$sql->addSelect('ImageUrl', 's');
		$sql->addSelect('CategoryUri', 's');
		$sql->addSelect('SidebarHtml', 's');
		$sql->AddOrderBy('Name', 'c', 'ASC');
		foreach ($ids as $id) {
			$sql->AddWhere('s', 'CategoryID', '', $id, '=', 'OR');
		}
		
		// Execute query
		$db = $context->Database;
		$rs = $db->Execute($sql->GetSelect(), $context, __FUNCTION__, 'Failed to fetch projects from database.');

		// Gather and return projects
		$projects = array();
		if ($db->RowCount($rs) > 0)
		{
			while($db_project = $db->GetRow($rs))
			{
				$db_project['Name'] = substr($db_project['Name'], 2);
				$projects[] = $db_project;
			}
		}

		return $projects;
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

	public static function getStickies(array $categoryIds, Context $context)
	{
		// Build selection query
		$sql = $context->ObjectFactory->NewContextObject($context, 'SqlBuilder');
		$sql->SetMainTable('Discussion','d');
		$sql->addSelect('Name', 'd');
		$sql->addSelect('DiscussionID', 'd');
		$sql->AddOrderBy('DateLastActive', 'd', 'DESC');
		$sql->AddWhere('d', 'Sticky', '',  1, '=');
		$sql->StartWhereGroup('AND');
		foreach ($categoryIds as $categoryId) {
			$sql->AddWhere('d', 'CategoryID', '', $categoryId, '=', 'OR');
		}
		$sql->EndWhereGroup();

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
	
}

function MiProject_RenderGridHeader(DiscussionGrid $grid)
{
	// Fetch current show
	$project = MiProjectsDatabasePeer::getProjects(array(ForceIncomingInt('CategoryID', null)), $grid->Context);
	$project = $project[0];

	// Render template
	$tplProject = <<<EOT
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
		$tplProject, 
		$project['ImageUrl'],
		$grid->Context->Configuration['WEB_ROOT'],
		$project['CategoryID'],
		$project['Name'],
		$project['Description'], 
		$project['WebsiteUrl'],
		$project['WebsiteUrl']
	);
}