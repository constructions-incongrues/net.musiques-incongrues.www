<?php
/*
Extension Name: Vanilla events
Extension Url: http://vanilla-events
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
$uid = $Context->Session->UserID;
if (in_array($Context->SelfUrl, array("account.php", "categories.php", "comments.php", "index.php", "search.php", "extension.php")))
{
   $today_events = EventsPeer::getEvents($Context, date('Y-m-d'), date('Y-m-d'));
   if ($today_events)
   {
     $event_tpl = '<a href="%s">%s (%s)</a>';
     $events_strings = array();
     foreach ($today_events as $event)
     {
       $events_string[] = sprintf($event_tpl,
         GetUrl($Context->Configuration, 'comments.php', '', 'DiscussionID', $event['DiscussionID'], '', '#Item_1', CleanupString($event['Name'].'/')),
	 $event['Name'],
	 $event['City']
);
     }
     if (strtolower(ForceIncomingString('Page', '')) != 'dons' && ! $Context->SelfUrl != 'search.php' && !in_array(ForceIncomingString("PostBackAction", ""), array('Events')))
     {
       $notice = sprintf("
       <h3 style='display:inline;'>Ce soir on sort : </h3>
       %s
       - <small><a href='/forum/events/'>Voir tous les évènements à venir</a></small>
       ", implode(', ', $events_string));
       $NoticeCollector->AddNotice($notice);
     }
   }
}

// Locale setup
setlocale(LC_ALL, 'fr_FR.UTF-8');

// JQuery date picker
if ($Context->SelfUrl == 'post.php')
{
  $Head->AddScript('extensions/vanilla-events/js/date.js');
  $Head->AddScript('extensions/vanilla-events/js/jquery.datePicker.js');
  $Head->AddScript('extensions/vanilla-events/js/behaviors.js');
  $Head->AddStyleSheet('extensions/vanilla-events/css/datePicker.css');
}

// Database setup
if (!array_key_exists('VANILLAEVENTS', $Configuration))
{
  $query = "SHOW COLUMNS FROM ".$Configuration['DATABASE_TABLE_PREFIX']."Event";
  $table_exists = mysql_query($query);
  if (!$table_exists)
  {
    $success = mysql_query(sprintf("
CREATE TABLE `%sEvent` (
  `DiscussionID` int(8) NOT NULL,
  `Date` date NOT NULL,
  `City` varchar(255) default NULL,
  `Country` varchar(255) default NULL,
  PRIMARY KEY  (`DiscussionID`)
)
", $Configuration['DATABASE_TABLE_PREFIX']));

    if ($success)
    {
      $Structure = "// Event Table Structure
\$DatabaseTables['Event'] = 'Event';
\$DatabaseColumns['Event']['DiscussionID'] = 'DiscussionID';
\$DatabaseColumns['Event']['Date'] = 'Date';
\$DatabaseColumns['Event']['City'] = 'City';
\$DatabaseColumns['Event']['Country'] = 'Country';
";

      AppendToConfigurationFile($Configuration['APPLICATION_PATH'].'conf/database.php', $Structure);
      AddConfigurationSetting($Context, 'VANILLAEVENTS', '1');
    }
    else
    {
      die(mysql_error());
    }
  }
}


// Add "events" tab
$Menu->addTab($Context->getDefinition('Events'),
              $Context->getDefinition('Events'),
              $Configuration['BASE_URL'] . 'events/',
	      'class="Eyes"');

// Add event related form controls
if ($Context->SelfUrl == 'post.php')
{
  $Context->AddToDelegate("DiscussionForm", "DiscussionForm_PreCommentRender", 'VanillaEvents_MetadataControls');
  $Context->AddToDelegate("DiscussionForm","PostSaveDiscussion","VanillaEvents_ProcessEvent");
}

// Code needed to display the "events" page
if(in_array(ForceIncomingString("PostBackAction", ""), array('Events')))
{
  $Context->PageTitle = $Context->GetDefinition('Events');
  $Menu->CurrentTab = 'Events';
  $Body->CssClass = 'Discussions';
  $page_events = new EventsPage($Context);
  $uid = $Context->Session->UserID;
  $Panel->addString($page_events->getPanelString());

  $Page->AddRenderControl($page_events, $Configuration["CONTROL_POSITION_BODY_ITEM"]);

  // Add link to podcast in website's head
  $Head->AddString(sprintf('<link rel="alternate" type="application/rss+xml" href="%s" title="Le calendrier des événements à venir" />', $Configuration['BASE_URL'].'s/feeds/events'));

}

class EventsPeer
{
  static function getEvents($context, $start_date, $end_date, $city = null)
  {
    $events = array();
    // Build selection query
    $sql = $context->ObjectFactory->NewContextObject($context, 'SqlBuilder');
    $sql->SetMainTable('Event','e');
    $sql->AddJoin('Discussion', 'd', 'DiscussionID', 'e', 'DiscussionID', 'INNER JOIN');
    $sql->addSelect('DiscussionID', 'e');
    $sql->addSelect('Date', 'e');
    $sql->addSelect('City', 'e');
    $sql->addSelect('Country', 'e');
    $sql->addSelect('Name', 'd');
    if ($city)
    {
      $sql->addWhere('e', 'City', '', mysql_escape_string($city), '=');
    }
    $sql->addWhere('e', 'Date', '', mysql_escape_string($start_date), '>=', 'and', '', 1, 1);
    $sql->addWhere('e', 'Date', '', mysql_escape_string($end_date), '<=');
    $sql->addWhere('d', 'Active', '', '1', '=');
    $sql->EndWhereGroup();
    $sql->AddOrderBy('Date', 'e', 'asc');

    // Execute query
    $db = $context->Database;
    $rs = $db->Execute($sql->GetSelect(), __CLASS__, __FUNCTION__, 'Failed to fetch events from database.');

    // Gather and return events
    if ($db->RowCount($rs) > 0)
    {
      while($db_event = $db->GetRow($rs))
      {
        $events[] = $db_event;
      }
    }

    return $events;
  }

  function getCities($context)
  {
    $cities = array();
    $sql = $context->ObjectFactory->NewContextObject($context, 'SqlBuilder');
    $sql->SetMainTable('Event','e');
    $sql->AddJoin('Discussion', 'd', 'DiscussionID', 'e', 'DiscussionID', 'INNER JOIN');
    $sql->addSelect('DiscussionID', 'e');
    $sql->addSelect('City', 'e');
    $sql->addWhere('d', 'Active', '', '1', '=');
    $sql->addWhere('e', 'Date', '', date('Y-m-d'), '>=');
    $sql->AddOrderBy('City', 'e', 'asc');

    // Execute query
    $db = $context->Database;
    $rs = $db->Execute($sql->GetSelect(), __CLASS__, __FUNCTION__, 'Failed to fetch events from database.');

    // Gather and return events
    if ($db->RowCount($rs) > 0)
    {
      while($db_city = $db->GetRow($rs))
      {
        $cities[] = ucfirst(strtolower($db_city['City']));
      }
    }

    return array_unique($cities);
  }

}

class EventsPage
{

  function EventsPage($context)
  {
    $this->Context = $context;
  }

  function getPanelString()
  {
    $cities = EventsPeer::getCities($this->Context);
    $cities_tpl = '<li><a href="%s">%s</a></li>';
    $cities_str = array();
    foreach ($cities as $city)
    {
      $cities_str[] = sprintf($cities_tpl, GetUrl($this->Context->Configuration, 'extension.php', '/', '', '', '?PostBackAction=Events&city=' . $city), $city);
    }

    $next_week = date('Y-m-d', time() + 60 * 60 * 24 * 7);
    $next_month = date('Y-m-d', time() + 60 * 60 * 24 * 30);

    $tpl = <<<EOF
<h2>Où ?</h2>
<ul class="label-links">
  <li class="nav-left-events-select">
    <a href="/forum/events">TOUTES LES VILLES</a>
    %s
  </li>
</ul>

<h2>Quand ?</h2>
<ul class="label-links">
  <li class="nav-left-events-select">
    <a href="/forum/events">N'importe quand !</a>
  </li>
  <li><a href="%s">Cette semaine</a></li>
  <li><a href="%s">Ce mois-ci</a></li>
</ul>
<h2>Mais encore ?</h2>
<ul class="label-links">
  <li><a href="%s">RSS</a></li>
</ul>
EOF;
    $tpl = sprintf(
      $tpl,
      implode("\n", $cities_str),
      GetUrl($this->Context->Configuration, 'extension.php', '/', '', '', '?PostBackAction=Events&start='.date('Y-m-d').'&end='.$next_week),
      GetUrl($this->Context->Configuration, 'extension.php', '/', '', '', '?PostBackAction=Events&start='.date('Y-m-d').'&end='.$next_month),
      $this->Context->Configuration['BASE_URL'].'s/feeds/events'
    );
    return $tpl;
  }

  function render()
  {
    $discussions = '';
    $first_iteration = true;
    $city = trim(ForceIncomingString('city', null), '/');
    $start_date = ForceIncomingString('start', date('Y-m-d'));
    $end_date = ForceIncomingString('end', date('Y-m-d', time() + 1000000000000));
    $date_parts = explode('-', $start_date);
    $start_date_tst = mktime(0, 0, 0, $date_parts[1], $date_parts[2], $date_parts[0]);
    $end_date_tst = mktime(0, 0, 0, $date_parts[1], $date_parts[2] + 90, $date_parts[0]);
    $end_date = date('Y-m-d', $end_date_tst);
    $previous_date = date('Y-m-d', mktime(0, 0, 0, $date_parts[1], $date_parts[2] - 30, $date_parts[0]));

    $i = 0;
    foreach (EventsPeer::getEvents($this->Context, $start_date, ForceIncomingString('end', date('Y-m-d', $end_date_tst)), $city) as $event)
    {
      $date_parts = explode('-', $event['Date']);
      $current_month = $date_parts[1];
      $timestamp = mktime(0, 0, 0, $date_parts[1], $date_parts[2], $date_parts[0]);
      if (!isset($previous_month))
      {
        $previous_month = $current_month;
        $first_month_separator = '<h2 class="events">'.strftime('%B %G', $timestamp).'</h2>';
      }
      if ($current_month != $previous_month)
      {
        $previous_month = $current_month;
        $month_separator = '<h2 class="events">'.strftime('%B %G', $timestamp).'</h2>';
      }
      else
      {
        $month_separator = '';
      }
      $human_date = strftime('%A %d', $timestamp);
      $href = GetUrl($this->Context->Configuration, 'comments.php', '', 'DiscussionID', $event['DiscussionID'], '', '#Item_1', CleanupString($event['Name']).'/');
      $link = sprintf('<strong class="title">%s</strong> - <a href="%s">%s</a>', $human_date, $href, $event['Name']);
      if ($first_iteration)
      {
        $discussions .= $first_month_separator;
      }
      $alternate = $i % 2 == 0 ? '' : 'modulo';
      $discussions .= $month_separator;
      $city_link = sprintf('<strong class="city">(<a href="%s">%s</a>)</strong>', GetUrl($this->Context->Configuration, 'extension.php', '/', '', '', '?PostBackAction=Events&city=' . $event['City'] . '&start='.$start_date), $event['City']);
      $discussions .= '<li class="Discussion Events '.$alternate.'"><ul><li class="DiscussionTopic">'.$link.' '.$city_link.'</li></ul></li>';
      $first_iteration = false;
      $i++;
    }

    // Create pager
    $past_url = GetUrl($this->Context->Configuration, 'extension.php', '/', '', '', '?PostBackAction=Events&start=' . $previous_date . '&city='.$city);
    $future_url = GetUrl($this->Context->Configuration, 'extension.php', '/', '', '', '?PostBackAction=Events&start=' . $end_date . '&city='.$city);

    $pager = sprintf('
<div class="PageInfo">
  <p>du %s au %s</p>&nbsp;
  <ol class="PageList debug">
    <li><a href="%s"><</a></li>
    <li class="navlink"><a href="%s">Passé</a></li> &bull;
    <li class="navlink"><a href="%s">À venir</a></li>
    <li><a href="%s">></a></li>
  </ol>
</div>', strftime('%d %B %G', $start_date_tst), strftime('%d %B %G', $end_date_tst), $past_url, $past_url, $future_url, $future_url);

    $propose_link = sprintf('<a href="%s">Proposer un évènement</a>', 'http://www.musiques-incongrues.net/forum/post/?is_event=true&CategoryID=5');

    if ($city)
    {
      $no_city_link = GetUrl($this->Context->Configuration, 'extension.php', '/', '', '', '?PostBackAction=Events&start='.$start_date);
      $top = sprintf('<h2 style="display:inline;" class="surtout">On fait quoi ce soir à %s ? <a class="ailleurs" href="%s">et ailleurs</a> </h2> ', $city, $no_city_link);
    }
    else
    {
      $top = '<h2 style="display:inline;" class="surtout">On fait quoi ce soir ?</h2>';
      $top .= sprintf('<span id="ferran"><h1>%s</h1></span>', $propose_link);
    }

    $variet = array('Michel Sardou',
                    'Michel Polnareff',
                    'Claude François',
                    'Didier Barbelivien',
                    'C Jérome',
                    'Alpha Blondy',
                    'Sacha Distel',
                    'Plastic Bertrand',
                    'Chantal Goya',
                    'Nana Mouskouri',
                    'Dr. Alban',
                    'Benny B',
                    'Aqua',
                    'Scooter');

    $rand_variet = $variet[rand(0, count($variet) - 1)];
//    $rand_variet = get_chanteur(1);

    $sentences = array('remuer ses fesses',
                       'peloter une grosse',
                       'faire la queue-leu-leu',
                       'découvrir Jean-Jacques Goldman',
                       'lire un poème',
                       'regretter d\'être venu',
                       'être sur la guest list',
                       'reluquer des fesses',
                       'porter une jupe',
                       'être nu',
                       'recompiler le noyau',
                       'passer à la télé',
                       'créer l\'évènement',
                       'se faire rempoter le bambou',
                       'croire en dieu',
                       'apprivoiser un castor',
                       'arrêter de fumer',
                       'manger son vomi',
                       'envisager le pire',
                       'louper son bus',
                        sprintf('chanter du %s', $rand_variet),
                       'manger un kebab',
                       'boire un ricard',
                       'tapiner',
                       'tirer dans le tas',
                       'hurler à la mort',
                       'faire péter une rondelle',
                       'choper une mst',
                       'voler un rein',
                       'se faire voler son portable',
                       'hurler de joie',
                       'rire du malheur des autres',
                       'voter à droite',
                       'oublier le tiers monde',
                       'passer la nuit en garde à vue',
                       'faire un strip-tease',
                       'boire un cul-sec',
                       'maudire le DJ',
                       'racketter un vieux',
                       'fumer du crack',
                       'manger ses crottes de nez',
                       'vomir son quatre heure',
                       'déguiser son être',
                       'manger des chips',
                       'perdre une dent',
                       'payer sa bière une fortune',
                       'se faire vider comme un malpropre',
                       'vomir sur scène',
                       'brailler comme un hippocampe',
                       'se mettre des trucs dans le nez',
                       'rencontrer l\'amour',
                       'planquer des bouteilles',
                       'rentrer sous la pluie',
                       'dépenser son SMIC',
                       'tout oublier');

    $rivers = array('Paris'     => 'tomber dans la Seine',
                    'Lyon'      => 'traverser une traboule',
                    'Londres'   => 'tomber dans la Tamise',
                    'Bruxelles' => 'tomber dans la fontaine du Parc Royal',
                    'Barcelone' => 'tomber dans la Méditerranée',
                    'Bordeaux'  => 'aller au Quick',
                    'Marseille' => 'dormir sur la Canebière',
                    'Nantes'    => 'rencontrer Puyo Puyo',
                    'Avignon'   => 'tomber du pont',
                    'Rotterdam' => 'étudier l\'architecture',
                    'Lille'     => 'tomber sur un chti',
                    'Angers'    => 'visiter le chateau du Roi René',
                    'Liège'     => 'tomber dans la Meuse',
                    'Cherbourg' => 'oublier son parapluie',
                    'Beauvais'  => 'aimer Beauvais tout simplement',
                    'default'   => 'tomber dans un trou');
    $city = in_array($city, array_keys($rivers)) ? $city : 'default';

    shuffle($sentences);
    $selected_sentences = array_splice($sentences, 0, 15);
    $selected_sentences[] = $rivers[$city];
    shuffle($selected_sentences);
    $top .= sprintf('<p class="legend">%s ...</p>', ucfirst(implode(', ', $selected_sentences)));
    $body = '%s%s<div id="ContentBody"><ol id="Discussions">%s</ol></div>';

    echo sprintf($body, $top, $pager, $discussions);
  }
}

/**
 * Displays additional event controls within discussion form.
 */
function VanillaEvents_MetadataControls(&$DiscussionForm)
{
  $html = '
      <li id="button-events">
        <label for="VanillaEvents_isevent" style="display:inline;">%s</label>
        <input type="checkbox" class="check_event" name="VanillaEvents_isevent" %s %s onclick="jQuery(\'#VanillaEvents_fieldset\').toggle();"/>
      </li>
      <fieldset id="VanillaEvents_fieldset" style="display:%s;">
      <li>
        <label for="VanillaEvents_date">%s (DD/MM/YYYY) !</label>
        <input name="VanillaEvents_date" class="datepicker event-input-date" value="%s" />
      </li>
      <li>
        <label for="VanillaEvents_city">%s</label>
        <input name="VanillaEvents_city" value="%s" class="event-input-city" />
      </li>
      <li>
        <label for="VanillaEvents_country">%s</label>
        <input name="VanillaEvents_country" value="%s" class="event-input-country"/>
      </li>
      </fieldset>
      %s
  ';

  // Today's date - formatted for display
  $fmt_today = date('d/m/Y');

  // Default form values
  $form_isevent = ForceIncomingString('VanillaEvents_isevent', true) === 'on' ? 'checked' : '';
  $form_disable = '';
  $form_date = $fmt_today;
  $form_city = 'Paris';
  $form_country = 'France';
  $form_hidden_isevent = '';
  $fieldset_visibility = 'none';
  if (isset($_GET['is_event']) && $_GET['is_event'] == 'true')
  {
    $form_isevent = 'checked';
//    $form_hidden_isevent = true;
    $fieldset_visibility = 'visible';
  }

  // Check if an event is already related to this discussion
  if ($DiscussionForm->Discussion->DiscussionID)
  {
    // Build selection query
    $sql = $DiscussionForm->Context->ObjectFactory->NewContextObject($DiscussionForm->Context, 'SqlBuilder');
    $sql->SetMainTable('Event','e');
    $sql->addSelect('DiscussionID', 'e');
    $sql->addSelect('Date', 'e', 'Date', 'DATE_FORMAT', '"%d/%m/%Y"');
    $sql->addSelect('City', 'e');
    $sql->addSelect('Country', 'e');
    $sql->addWhere('e', 'DiscussionID', '', $DiscussionForm->Discussion->DiscussionID, '=');

    // Execute query
    $db = $DiscussionForm->Context->Database;
    $rs = $db->Execute($sql->GetSelect(), $DiscussionForm, __FUNCTION__, 'Failed to fetch event from database.');
    if ($db->RowCount($rs) > 0)
    {
      $db_event = $db->GetRow($rs);
      $form_date = $db_event['Date'];
      $form_city = $db_event['City'];
      $form_country = $db_event['Country'];
      $form_disable = 'disabled';
      $form_isevent = 'checked';
      $form_hidden_isevent = '<input type="hidden" name="VanillaEvents_isevent" value="on" />';
      $fieldset_visibility = 'visible';
    }
  }

  // If for has been submitted, override values with POST data
  if (isset($_POST['FormPostBackKey']))
  {
    $form_date = ForceIncomingString('VanillaEvents_date', '');
    $form_city = ForceIncomingString('VanillaEvents_city', '');
    $form_country = ForceIncomingString('VanillaEvents_country', '');
  }

  // Template population and rendering
  echo sprintf($html,
               $DiscussionForm->Context->getDefinition("C'est un évènement ?"),
               FormatStringForDisplay($form_isevent), $form_disable, $fieldset_visibility,
               $DiscussionForm->Context->getDefinition('Date'),
               FormatStringForDisplay($form_date),
               $DiscussionForm->Context->getDefinition('City'),
               FormatStringForDisplay($form_city),
               $DiscussionForm->Context->getDefinition('Country'),
               FormatStringForDisplay($form_country),
               $form_hidden_isevent
  );
}

/**
 * Triggers event creation / update if user requested it.
 */
function VanillaEvents_ProcessEvent(&$DiscussionForm)
{
  // Gather data
  if(ForceIncomingBool('VanillaEvents_isevent', true) === true)
  {
    $event_country = ForceIncomingString('VanillaEvents_country', '');
    $event_city = ForceIncomingString('VanillaEvents_city', '');

    // Transform date for MySQL
    $human_date = ForceIncomingString('VanillaEvents_date', '');
    $date_parts = explode('/', $human_date);
    $mysql_date_parts = @array($date_parts[2], $date_parts[1], $date_parts[0]);
    $event_date = implode('-', $mysql_date_parts);

    // Save event to backend
    VanillaEvents_SaveEvent($DiscussionForm, $event_date, $event_city, $event_country);
  }
}

/**
 * Validates and save data to backend.
 */
function VanillaEvents_SaveEvent(&$DiscussionForm, $date, $city, $country)
{
  // Validate event data
  $date_is_valid = false;
  $city_is_valid = true;
  $country_is_valid = true;

  // Date is mandatory
  if (!empty($date))
  {
    $date_is_valid = true;
  }
  else
  {
    $DiscussionForm->Context->WarningCollector->add('You must enter a value for the date input.');
  }

  // Date must be correctly formatted
  if (preg_match('/\d{4}-\d{2}-\d{2}/', $date))
  {
    $date_is_valid = true;
  }
  else
  {
    $date_is_valid = false;
    $DiscussionForm->Context->WarningCollector->add('Date must be formatted as DD/MM/YYYY.');
  }

  // Save event to backend
  if ($date_is_valid && $city_is_valid && $country_is_valid)
  {
    // Only create / update event if discussion has been saved to database
    if ($DiscussionForm->DelegateParameters['ResultDiscussion']->DiscussionID)
    {
      // REPLACE event into database
      $sql = $DiscussionForm->Context->ObjectFactory->NewContextObject($DiscussionForm->Context, 'SqlBuilder');
      $sql->SetMainTable('Event','e');
      $sql->AddFieldNameValue('DiscussionID', mysql_real_escape_string($DiscussionForm->DelegateParameters['ResultDiscussion']->DiscussionID));
      $sql->AddFieldNameValue('City', mysql_real_escape_string($city));
      $sql->AddFieldNameValue('Country', mysql_real_escape_string($country));
      $sql->AddFieldNameValue('Date', mysql_real_escape_string($date));
      $sql_query = str_replace('UPDATE', 'REPLACE', $sql->GetUpdate()); // SqlBuilder does not have a GetReplace method
      $DiscussionForm->Context->Database->Execute($sql_query, $DiscussionForm, __FUNCTION__, 'Failed to save event into database.');
    }
  }
  else
  {
    $discussion_url = GetUrl($DiscussionForm->Context->Configuration, 'post.php', '/', 'CommentID', $DiscussionForm->DelegateParameters['ResultDiscussion']->Comment->CommentID);
    die(sprintf('Event was not saved : date is not properly formatted. <a href="%s">Click here</a> to fix that !', $discussion_url));
  }
}

?>
