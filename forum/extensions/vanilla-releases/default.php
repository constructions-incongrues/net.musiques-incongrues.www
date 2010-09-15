<?php
/*
Extension Name: Vanilla Releases 
Extension Url: http://vanilla-releases
Description: 100% codé avec les pieds !
Version: 0.1
Author: Tristan Rivoallan <tristan@rivoallan.net>
Author Url: http://blogmarks.net/user/mbertier/marks
*/

/*
 * TODO : warnings are not shown to user if only event controls have errors.
 * TODO : it could be more clever to subclass the Discussion / DiscussionForm classes (?)
 * TODO : cleanup events rendering code
 */

error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL);

if (!($Context->SelfUrl == 'post.php' || $Context->SelfUrl == 'index.php' || $Context->SelfUrl == 'comments.php' || $Context->SelfUrl == 'extension.php' || $Context->SelfUrl == 'categories.php' || $Context->SelfUrl == 'search.php'))
{
  return;  
}

/*
// Limit access to thoses uids
$uid = $Context->Session->UserID;
if (!($uid == 1 || $uid == 2 || $uid == 47))
{
  return;
}
*/

// Locale setup
setlocale(LC_ALL, 'fr_FR.UTF-8');
/*
// Database setup
if (!array_key_exists('VANILLARELEASES', $Configuration))
{
  $query = "SHOW COLUMNS FROM ".$Configuration['DATABASE_TABLE_PREFIX']."Releases";
  $table_exists = mysql_query($query);
  if (!$table_exists)
  {
    $success = mysql_query(sprintf("
CREATE TABLE `%sReleases` (
  `DiscussionID` int(8) NOT NULL,
  PRIMARY KEY  (`DiscussionID`)
)
", $Configuration['DATABASE_TABLE_PREFIX']));

    if ($success)
    {
      $Structure = "// Release Table Structure
\$DatabaseColumns['Releases']['DiscussionID'] = 'DiscussionID';
";
  
      AppendToConfigurationFile($Configuration['APPLICATION_PATH'].'conf/database.php', $Structure);
      AddConfigurationSetting($Context, 'VANILLARELEASES', '1');
    }
    else
    {
      die(mysql_error());
    }
  }
}
*/

// Add "events" tab
$Menu->addTab($Context->getDefinition('Releases'),
              $Context->getDefinition('Releases'),
              $Configuration['BASE_URL'] . 'releases/',
	      'class="Eyes"');

//$Head->AddScript('extensions/vanilla-releases/js/soundmanager2/script/soundmanager2-nodebug-jsmin.js');
$Head->AddScript('extensions/vanilla-releases/js/soundmanager2/script/soundmanager2.js');

// Add event related form controls
if ($Context->SelfUrl == 'post.php')
{
  $Context->AddToDelegate("DiscussionForm", "DiscussionForm_PreCommentRender", 'VanillaReleases_MetadataControls');
  $Context->AddToDelegate("DiscussionForm","PostSaveDiscussion","VanillaReleases_ProcessRelease");
}

// Code needed to display the "events" page
if(in_array(ForceIncomingString("PostBackAction", ""), array('Releases')))
{
  $Head->AddScript('extensions/vanilla-releases/js/soundmanager2/css/inlineplayer.css');
  $Head->AddScript('extensions/vanilla-releases/js/soundmanager2/script/inlineplayer.js');

  $Context->PageTitle = $Context->GetDefinition('Releases');
  $Menu->CurrentTab = 'Releases';
  $Body->CssClass = 'Discussions';
  $page = new ReleasesPage($Context);
  $Page->AddRenderControl($page, $Configuration["CONTROL_POSITION_BODY_ITEM"]);
  $Panel->addString($page->getLabelsPanel(ForceIncomingString('label', null)));
}

class ReleasesPage
{
  
  function ReleasesPage($context)
  {
    $this->Context = $context;
  }
  
  function getLabelsPanel($current_label = null)
  {
    $item_tpl = '<li><a href="?label=%s" class="%s">%s</a></li>';
    $items = array();
    $items[] = sprintf($item_tpl, '', '', 'Toutes les sorties');
    foreach ($this->getLabels() as $label)
    {
      if ($label['LabelName'])
      {
        $classname = '';
        if (ForceIncomingString('label', '') == $label['LabelName'])
        {
          $classname = 'current';
        }
        $items[] = sprintf($item_tpl, $label['LabelName'], $classname, $label['LabelName']);
      }
    }
    if (ForceIncomingString('only_mixes', '0') === '0')
    {
      $mixes_limitation =
'<h2>Mixes</h2>
<ul class="label-links">
  <li><a href="?only_mixes=1">N\'afficher que les mixes</a></li>
</ul>';
    }
    else
    {
      $mixes_limitation = 
'<h2>Mixes</h2>
<ul class="label-links">
  <li><a href="?only_mixes=0">Afficher toutes les releases</a></li>
</ul>';
    }

    return sprintf('%s<h2>%s</h2><ul class="label-links">%s</ul>', $mixes_limitation, 'Labels', implode("\n", $items));
  }

  function getLabels()
  {
    $labels = array();
    $sql = $this->Context->ObjectFactory->NewContextObject($this->Context, 'SqlBuilder');
    $sql->SetMainTable('Releases','r');
    $sql->AddJoin('Discussion', 'd', 'DiscussionID', 'r', 'DiscussionID', 'INNER JOIN');
    $sql->addSelect('LabelName', 'r', 'LabelName', 'DISTINCT');
    $sql->addWhere('d', 'Active', '', '1', '=');
    // TODO : also add a "OR r.IsMix IS NULL clause
    //$sql->addWhere('r', 'IsMix', '', '1', '!=');
    $sql->AddOrderBy('LabelName', 'r', 'asc');
    // Execute query
    $db = $this->Context->Database;
    $rs = $db->Execute($sql->GetSelect(), $this, __FUNCTION__, 'Failed to fetch releases from database.');
var_dump($sql->GetSelect());

    // Gather and return events
    if ($db->RowCount($rs) > 0)
    {
      while($db_label = $db->GetRow($rs))
      {
        $labels[] = $db_label;
      }
    }

    return $labels;
  }

  function getMixProviders()
  {
    $providers = array();
    $sql = $this->Context->ObjectFactory->NewContextObject($this->Context, 'SqlBuilder');
    $sql->SetMainTable('Releases','r');
    $sql->AddJoin('Discussion', 'd', 'DiscussionID', 'r', 'DiscussionID', 'INNER JOIN');
    $sql->addSelect('LabelName', 'r', 'LabelName', 'DISTINCT');
    $sql->addWhere('d', 'Active', '', '1', '=');
    $sql->addWhere('r', 'IsMix', '', '1', '=');
    $sql->AddOrderBy('LabelName', 'r', 'asc');

    // Execute query
    $db = $this->Context->Database;
    $rs = $db->Execute($sql->GetSelect(), $this, __FUNCTION__, 'Failed to fetch mixes providers from database.');

    // Gather and return events
    if ($db->RowCount($rs) > 0)
    {
      while($db_provider = $db->GetRow($rs))
      {
        $providers[] = $db_provider;
      }
    }

    return $providers;
  }

  function getReleases($label = null, $only_mixes = false)
  {
    $releases = array();
    
    // Build selection query
    $sql = $this->Context->ObjectFactory->NewContextObject($this->Context, 'SqlBuilder');
    $sql->SetMainTable('Releases','r');
    $sql->AddJoin('Discussion', 'd', 'DiscussionID', 'r', 'DiscussionID', 'INNER JOIN');
    $sql->addSelect('DiscussionID', 'r');
    $sql->addSelect('LabelName', 'r');
    $sql->addSelect('DownloadLink', 'r');
    $sql->addSelect('Name', 'd');
    $sql->addWhere('d', 'Active', '', '1', '=');
    if ($label)
    {
      $sql->addWhere('r', 'LabelName', '', mysql_real_escape_string($label), '=');
    }
    if ($only_mixes)
    {
      $sql->addWhere('r', 'IsMix', '', 1, '=');
    }
    $sql->AddOrderBy('DateCreated', 'd', 'desc');
    
    // Execute query
    $db = $this->Context->Database;
    $rs = $db->Execute($sql->GetSelect(), $this, __FUNCTION__, 'Failed to fetch releases from database.');

    // Gather and return events
    if ($db->RowCount($rs) > 0)
    {      
      while($db_release = $db->GetRow($rs))
      {
        $releases[] = $db_release;
      }
    }
    
    return $releases;
  }
  
  function render()
  {
    $discussions = '';

    $i = 0;
    $label_name = ForceIncomingString('label', null); 
    $releases = $this->getReleases($label_name, ForceIncomingString('only_mixes', false));
    foreach ($releases as $release)
    {
      $href = GetUrl($this->Context->Configuration, 'comments.php', '', 'DiscussionID', $release['DiscussionID'], '', '#Item_1', CleanupString($release['Name']).'/');
      $link = sprintf('<a href="%s" title="Discuter de %s">%s</a>', $href, $release['Name'], $release['Name']);
      $alternate = $i % 2 == 0 ? '' : 'modulo';
      $download_string = '';
      $label_string = '';
      $listen_string = '';
      if (isset($release['DownloadLink']) && $release['DownloadLink'])
      {
        $download_text = 'Download';
	$download_link_title = sprintf('Télécharger %s', $release['Name']);
	$download_string = sprintf('<span class="release-download">(<a class="inline-exclude" href="%s" title="%s">%s</a>)</span>', $release['DownloadLink'], $download_link_title, $download_text);

	if (pathinfo($release['DownloadLink'], PATHINFO_EXTENSION) == 'mp3')
	{
	  $listen_text = 'Play';
	  $link_class = 'sm2_link';
	  $listen_link_title = sprintf('Écouter %s', $release['Name']);
	  $listen_string = sprintf('<span class="release-download">(<a href="%s" class="%s" title="%s">%s</a>)</span>', $release['DownloadLink'], $link_class, $listen_link_title, $listen_text);
	}
      }
      if (isset($release['LabelName']) && $release['LabelName'])
      {
	$label_string = sprintf('<span class="release-label">(<a href="?label=%s" title="Voir toutes les releases du label %s">%s</a>)</span>%s', $release['LabelName'], $release['LabelName'], $release['LabelName'], $download_string ? ' ' : '');
      }
      $discussions .= sprintf('<li class="Discussion Release %s"><ul><li class="DiscussionTopic">%s %s %s %s</li></ul></li>', $alternate, $link, $label_string, $listen_string, $download_string);
      $i++;
    }
  
    // Top
    $title = sprintf('%d releases', count($releases));
    if ($label_name)
    {
      $title = sprintf('%d releases chez %s', count($releases), filter_var($label_name, FILTER_SANITIZE_STRING));
    }
    $propose_link = sprintf('<a href="%s">Proposer une release</a>', '/forum/post/?is_release=true');
    $top = '<h2 style="display:inline;" class="surtout">On écoute quoi aujourd\'hui ?</h2>';
    $top .= sprintf('<span id="ferran"><h1>%s</h1></span>', $propose_link);
    $top .= sprintf('<p class="legend">%s, ...</p>', get_chanteurs(25));
    $top .= '<hr  />';
    $top .= sprintf('<h2 class="release-count">%s : </h2><h2 id="legend-colors"> <strong>Légende : </strong> <span class="legend-label">Label</span> - <span class="legend-mix">Écouter</span> - <span class="legend-download">Télécharger</span></h2>', $title);
   
    // Body
    $body = '%s<div id="ContentBody" class="releases"><ol id="Discussions">%s</ol></div>';
    
    echo sprintf($body, $top, $discussions);
  }
}

function get_chanteurs($count = -1)
{
  $chanteurs_array = explode(', ', file_get_contents(dirname(__FILE__).'/chanson.txt'));
  shuffle($chanteurs_array);
  if ($count > 0)
  {
    $chunks = array_chunk($chanteurs_array, $count);
    $chanteurs_array = $chunks[0];
  }
  return implode(', ', $chanteurs_array);
}

/**
 * Displays additional event controls within discussion form.
 */
function VanillaReleases_MetadataControls(&$DiscussionForm)
{
  $html = '
      <li id="button-releases">
        <label for="VanillaReleases_isrelease" style="display: inline;">%s</label>
        <input type="checkbox" class="check_release" name="VanillaReleases_isrelease"  onclick="jQuery(\'#VanillaReleases_fieldset\').toggle();" %s %s "/>
	</li>
	<fieldset id="VanillaReleases_fieldset" style="display:%s;">
        <li>
	<label for="VanillaReleases_downloadlink">Lien vers la release (zip, rar, mp3, ogg, etc - un lien direct quoi !)</label>
	<input type="text" name="VanillaReleases_downloadlink" size="60" value="%s" class="release-input-url"/>
        </li>
        <li>
	<label for="VanillaReleases_label">Label (pour les release), ou artiste (pour les mixes)</label>
	<input type="text" name="VanillaReleases_label" value="%s" class="release-input-label"/>
        </li>
        <li>
          <label for="VanillaReleases_ismix" style="display: inline;">C\'est un mix</label>
          <input type="checkbox" name="VanillaReleases_ismix_checkbox" %s />
	</li>
	</fieldset>
      %s
      %s
  ';
  // Today's date - formatted for display
  $fmt_today = date('d/m/Y');

  // Default form values
  $form_isrelease = ForceIncomingString('VanillaReleases_isrelease', true) === 'on' ? 'checked' : '';
  $form_ismix = '';
  $form_disable = '';
  $form_hidden_isrelease = '';
  $form_hidden_ismix = '';
  $fieldset_display = ForceIncomingString('VanillaReleases_isrelease', true) === 'on' ? 'block' : 'none';
  $download_link = '';
  $label_name = '';

  if (isset($_GET['is_release']) && $_GET['is_release'] == 'true')
  {
    $form_isrelease = 'checked';
  }

  // Check if a release is already related to this discussion
  if ($DiscussionForm->Discussion->DiscussionID)
  {
    // Build selection query
    $sql = $DiscussionForm->Context->ObjectFactory->NewContextObject($DiscussionForm->Context, 'SqlBuilder');
    $sql->SetMainTable('Releases','r');
    $sql->addSelect('DiscussionID', 'r');
    $sql->addSelect('LabelName', 'r');
    $sql->addSelect('DownloadLink', 'r');
    $sql->addSelect('IsMix', 'r');
    $sql->addWhere('r', 'DiscussionID', '', $DiscussionForm->Discussion->DiscussionID, '=');

    // Execute query
    $db = $DiscussionForm->Context->Database;
    $rs = $db->Execute($sql->GetSelect(), $DiscussionForm, __FUNCTION__, 'Failed to fetch release from database.');
    if ($db->RowCount($rs) > 0)
    {      
      $db_release = $db->GetRow($rs);
      $form_disable = 'disabled';
      $form_isrelease = 'checked';
	if ($db_release['IsMix'])
	{
		$form_ismix = 'checked="checked"';
		$form_hidden_ismix = '<input type="hidden" name="VanillaReleases_ismix" value="1" />';
	}
	else
	{
		$form_hidden_ismix = '<input type="hidden" name="VanillaReleases_ismix" value="0" />';
	}
      $fieldset_display = 'block';
      $form_hidden_isrelease = '<input type="hidden" name="VanillaReleases_isrelease" value="on" />';
      $label_name = $db_release['LabelName'];
      $download_link = $db_release['DownloadLink'];
    }
  }

  
  // Template population and rendering
  echo sprintf($html,
               $DiscussionForm->Context->getDefinition("C'est une release / un mix ?"),
               FormatStringForDisplay($form_isrelease), $form_disable,
  	       $fieldset_display,
		$download_link,
		$label_name,
		$form_ismix,
               $form_hidden_isrelease,
               $form_hidden_ismix
	       );
}

/**
 * Triggers event creation / update if user requested it.
 */
function VanillaReleases_ProcessRelease(&$DiscussionForm)
{

	$is_mix = 0;
	if (ForceIncomingString('VanillaReleases_ismix_checkbox', false) === 'on')
	{
		$is_mix = 1;
	}

  // Gather data
  if(ForceIncomingBool('VanillaReleases_isrelease', true) === true)
  {
    // Save event to backend
    VanillaReleases_SaveRelease($DiscussionForm, ForceIncomingString('VanillaReleases_label', ''), ForceIncomingString('VanillaReleases_downloadlink', ''), $is_mix);
  }
}

/**
 * Validates and save data to backend.
 */
function VanillaReleases_SaveRelease(&$DiscussionForm, $label_name, $download_link, $is_mix)
{
  // Save event to backend
  // Only create / update event if discussion has been saved to database
  if ($DiscussionForm->DelegateParameters['ResultDiscussion']->DiscussionID)
  {
    // REPLACE event into database
    $sql = $DiscussionForm->Context->ObjectFactory->NewContextObject($DiscussionForm->Context, 'SqlBuilder');
    $sql->SetMainTable('Releases','r');
    $sql->AddFieldNameValue('DiscussionID', mysql_real_escape_string($DiscussionForm->DelegateParameters['ResultDiscussion']->DiscussionID));
    $sql->AddFieldNameValue('LabelName', mysql_real_escape_string($label_name));
    $sql->AddFieldNameValue('DownloadLink', mysql_real_escape_string($download_link));
    $sql->AddFieldNameValue('IsMix', mysql_real_escape_string($is_mix));
    $sql_query = str_replace('UPDATE', 'REPLACE', $sql->GetUpdate()); // SqlBuilder does not have a GetReplace method
    $DiscussionForm->Context->Database->Execute($sql_query, $DiscussionForm, __FUNCTION__, 'Failed to save release into database.');
  }
}
