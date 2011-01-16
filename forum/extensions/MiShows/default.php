<?php
/*
 Extension Name: MiShows
 Extension Url: https://github.com/contructions-incongrues
 Description: Displays list of shows forums associated with the project.
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */

// Configure "Shows" page rendering
if(in_array(ForceIncomingString("PostBackAction", ""), array('Shows'))) {
	$Context->PageTitle = 'Shows - Musiques Incongrues';
	$Menu->CurrentTab = 'Shows';
	$Body->CssClass = 'Discussions';
	$Page->AddRenderControl(new MiShowsPage($Context, $Head), $Configuration["CONTROL_POSITION_BODY_ITEM"]);
	$Head->Meta['description'] = 'Les toutes dernières production de The Brain, Istota Ssaca, Le Laboratoire, Ouïedire, This Is Radioclash. Mais pas seulement !';
}

// Modify discussion grid when in a show category
$idsShows = MiShowsDatabasePeer::$shows_ids;
$requestedCategoryId = ForceIncomingInt('CategoryID', null);
if (($Context->SelfUrl == 'index.php' && in_array($requestedCategoryId, $idsShows)) || ForceIncomingString('PostBackAction', null) == 'Shows') {

	// Show show header
	$Context->AddToDelegate('DiscussionGrid', 'PreRender', 'MiShow_RenderGridHeader');
	
	// Update sidebar
	if (isset($Panel)) {

		// Fetch latest sticky for show
		$categoriesForStickies = array($requestedCategoryId);
		if (ForceIncomingString('PostBackAction', null) == 'Shows') {
			$categoriesForStickies = $idsShows;
		}
		
		$dbStickies = MiShowsDatabasePeer::getStickies($categoriesForStickies, $Context);

		if (count($dbStickies)) {
			$Panel->AddString('<h2>Dernièrement</h2>');
		}
		
		foreach ($dbStickies as $dbSticky) {
		
			// Grab associated release
			$dbRelease = MiShowsDatabasePeer::getRelease($dbSticky['DiscussionID'], $Context);
		
			$tplStickies = <<<EOT
	<div class="emissions-global-box">
		
		<p class="emissions-box-name-show"><a href="%s" title="%s">%s</a></p>
		
		<p class="emissions-box-artwork">
			<a href="%s" title="%s">
				<img src="%s" class="emissions-box-cover" />
			</a>
		</p>
EOT;
			// Add download link, if appropriate
			if ($dbRelease['DownloadLink']) {
				$tplStickies .= '<p class="emissions-box-player"><a href="%s" title="Écouter l\'émission">Écouter l\'émission</a></p>';
			}
			
			$Panel->addString(sprintf(
				$tplStickies.'</div>', 
				GetUrl($Context->Configuration, 'comments.php', '', 'DiscussionID', $dbSticky['DiscussionID'], '', '#Item_1', CleanupString($dbSticky['Name']).'/'),
				$dbSticky['Name'], 
				truncate_text($dbSticky['Name'], 28),
				GetUrl($Context->Configuration, 'comments.php', '', 'DiscussionID', $dbSticky['DiscussionID'], '', '#Item_1', CleanupString($dbSticky['Name']).'/'),
				$dbSticky['Name'], 
				getFirstImageUrl($dbSticky['DiscussionID']),
				$dbRelease['DownloadLink']
			));
		}
		
		// Show video from first sticky in sidebar
		if (count($dbStickies) && ForceIncomingString('PostBackAction', null) != 'Shows') {
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
		$show = MiShowsDatabasePeer::getShows(array(ForceIncomingInt('CategoryID', null)), $Context);
		if (count($show)) {
			$Panel->addString(utf8_encode($show[0]['SidebarHtml']));
		}
	}
}

// Add extension stylesheet
$Head->AddStyleSheet('extensions/MiShows/css/style.css');

class MiShowsPage
{
	private $context;

	public function __construct(Context $context, Head $head)
	{ 
		// Store context
		$this->context = $context;
	}

	public function render()
	{
		// Fetch shows
		$shows = MiShowsDatabasePeer::getShows(MiShowsDatabasePeer::$shows_ids, $this->context);
		$strShows = $this->renderShows($shows);
		
		// Fetch parent category
		$parentCategory = MiShowsDatabasePeer::getCategories(array(MiShowsDatabasePeer::$parent_id), $this->context);
		
		$html = <<<EOT
<div id="ContentBody" class="releases">
	<h2 class="top-title-category-label" id="5">Émissions</h2> 
	<p  class="top-title-category-label-legend">%s</p>
	<ol id="Discussions">
		<li class="Discussion Release">
			%s
		</li>
	</ol>
</div>		
EOT;

		echo sprintf($html, $parentCategory[0]['Description'], $strShows);
	}

	/**
	 * @param array $shows
	 * 
	 * TODO : use CategoryUri to generate pretty urls. Must also amend .htaccess
	 */
	private function renderShows(array $shows)
	{
		$tplShow = <<<EOT
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
		$strShows = array();
		foreach ($shows as $show) {
			$strShows[] = sprintf(
				$tplShow, 
				$show['ImageUrl'],
				$this->context->Configuration['WEB_ROOT'],
				$show['CategoryID'],
				$show['Name'],
				$this->context->Configuration['WEB_ROOT'], 
				$show['CategoryID'], 
				$show['Description'], 
				$show['WebsiteUrl'],
				$show['WebsiteUrl']
			);
		}

		return implode("\n", $strShows);
	}
}

class MiShowsDatabasePeer
{
	public static $shows_ids = array(2, 10, 12, 20, 21);
	public static $parent_id = 18;
	
	public static function getShows(array $ids, Context $context)
	{
		// Build selection query
		$sql = $context->ObjectFactory->NewContextObject($context, 'SqlBuilder');
		$sql->SetMainTable('Show','s');
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
		$rs = $db->Execute($sql->GetSelect(), $context, __FUNCTION__, 'Failed to fetch shows from database.');

		// Gather and return shows
		$shows = array();
		if ($db->RowCount($rs) > 0)
		{
			while($db_show = $db->GetRow($rs))
			{
				$db_show['Name'] = substr($db_show['Name'], 2);
				$shows[] = $db_show;
			}
		}

		return $shows;
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

function MiShow_RenderGridHeader(DiscussionGrid $grid)
{
	// Fetch current show
	$show = MiShowsDatabasePeer::getShows(array(ForceIncomingInt('CategoryID', null)), $grid->Context);
	$show = $show[0];
	
	// Render template
	/**
	 *
	 */
	$tplShow = <<<EOT
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
		$tplShow, 
		$show['ImageUrl'],
		$grid->Context->Configuration['WEB_ROOT'],
		$show['CategoryID'],
		$show['Name'],
		$show['Description'], 
		$show['WebsiteUrl'],
		$show['WebsiteUrl']
	);
}