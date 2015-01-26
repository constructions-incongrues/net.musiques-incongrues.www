<?php
/*
Extension Name: Guest Welcome Message
Extension Url: http://lussumo.com/docs/
Description: Adds a welcome message to the panel if the person viewing the forum doesn't have an active session.
Version: 3.0
Author: Mark O'Sullivan
Author Url: http://markosullivan.ca/
*/

$Context->Dictionary["GuestWelcome"] = "<strong>Bienvenue sur le forum des musiques incongrues</strong>
   <p>Ce que vous allez trouver ici :</p>
   <ul>
     <li>Des <a href='/forum/discussions'>discussions</a> plus ou moins inspirées (car nous sommes bien sur un forum)</li>
     <li><a href='/forum/events'>Un calendrier d'évènements</a>, pour vous aider à occuper vos soirées</li>
     <li>La liste exhaustive de <a href='/forum/releases/'>toutes les sorties musicales annoncées sur ce forum</a> depuis sa création</li>
     <li>Une <a href='/forum/radio-random.php'>radio</a> automatique et surprenante</li>
     <li>Une <a href='/forum/oeil'>pinacothèque</a> collaborative</li>
   </ul>
   
   <p>
     Cerise sur le gâteau, vous pouvez très facilement apporter votre contribution à tout ça.
     Pour ce faire, le mieux est encore de vous <a href='".GetUrl($Configuration, "people.php")."'>connecter</a> 
     ou de vous <a href='".GetUrl($Configuration, "people.php", "", "", "", "", "PostBackAction=ApplyForm")."'>inscrire</a> :)
   </p>
   
   <p>
     Enfin, vous pouvez nous contacter directement à l'adresse email : <a href=\"#\">contact (CHEZ) musiques-incongrues (POINT) net</a>
   </p>
   ";

if (in_array($Context->SelfUrl, array("account.php", "categories.php", "comments.php", "index.php", "search.php", "extension.php")) && $Context->Session->UserID == 0) {
   $NoticeCollector->AddNotice($Context->GetDefinition('GuestWelcome'));
}
?>
