<?php
/*
~~~~~~~~~ DO NOT EDIT BELOW OR YOUR EXTENSION WILL NOT WORK ~~~~~~~~~~~~
Extension Name: CustomSideBarLink
Extension Url: http://daynelyons.com
Description: Add endless links to the sidebar of any page of your Forums
Version: 1.5.2
Author: Dayne Lyons
Author Url: http://daynelyons.com
~~~~~~~~~ DO NOT EDIT ABOVE OR YOUR EXTENSION WILL NOT WORK ~~~~~~~~~~~~
*/

$links = array();
bz_addlink($links, 'Da ! Heard It Records', 'http://www.daheardit-records.net');
bz_addlink($links, 'Ego Twister', 'http://www.egotwister.com');
bz_addlink($links, 'Festival Serendip', 'http://www.serendip-arts.org');
bz_addlink($links, 'Istota Ssaca', 'http://istotassaca.blogspot.com/');
bz_addlink($links, 'Le Laboratoire', 'http://lelaboratoire.be/');
bz_addlink($links, 'Mazemod', 'http://www.mazemod.org');
bz_addlink($links, 'Musique Approximative', 'http://www.musiqueapproximative.net');
bz_addlink($links, 'Ouïedire', 'http://www.ouiedire.net');
bz_addlink($links, 'Pardon My French', 'http://www.pardon-my-french.fr');
bz_addlink($links, 'Radioclash', 'http://www.thisisradioclash.org');
bz_addlink($links, 'The Brain', 'http://thebrain.lautre.net');
bz_addlink($links, 'WANT', 'http://want.benetbene.net');

/* What page (or pages) is/are your link going on? */
if(in_array($Context->SelfUrl, array('categories.php', 'search.php', 'account.php', 'comments.php', 'index.php')))
{
  // Add panel to sidebar
  $Panel->addString('<h2>Ailleurs</h2>');

  $Panel->addString('<ul class="ailleurs-links">');

  // Add links to panel
  $tpl_item = '<li><a href="%s" title="%s">%s</a></li>';
  foreach ($links as $link)
  {
    $Panel->AddString(sprintf($tpl_item, $link['href'], $link['title'], $link['title']));
  }

  $Panel->addString('</ul>');

  return;
}

// Just to help bozoo :)
function bz_addlink(&$links_array, $title, $href)
{
  $links_array[] = array('title' => $title, 'href' => $href);
}
