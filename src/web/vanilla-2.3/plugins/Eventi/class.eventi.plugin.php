<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2008-2013 Vanilla Forums Inc.
This file is part of Garden.
Garden is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
Garden is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Garden.  If not, see <http://www.gnu.org/licenses/>.
Contact Vanilla Forums Inc. at support [at] vanillaforums [dot] com
*/

// Define the plugin:
$PluginInfo['Eventi'] = array(
   'Name' => 'Eventi',
   'Description' => 'This plugin hooks every possible event and outputs a little chunk of signed HTML inline.',
   'Version' => '2.0.0',
   'RequiredApplications' => array('Vanilla' => '2.1'),
   'Author' => "Dane MacMillan",
   'AuthorEmail' => 'dane@vanillaforums.com',
   'AuthorUrl' => 'http://vanillaforums.org/profile/dane',
   'License' => 'GNU GPL2',
   'MobileFriendly' => true
);

/** 
 * Changelog:
 * 
 * @version 1.0.0 - 1.0.1 
 * @author Tim Gunter <tim@vanillaforums.com>
 * 
 * - Original functionality and UI.
 * 
 * @version 2.0.0
 * @author Dane MacMillan <dane@vanillaforums.com>
 * @date November 3, 2013
 * 
 * - Removed JavaScript dependency, so CSS tooltip will appear without JS.
 * - Removed images. 
 * - Complete rewrite of HTML, CSS, and JavaScript.
 * - Completely redesigned UI.
 * - Cleaned up PHP to more easily view markup struture.
 * - JavaScript viewport edge detection (either beyond right or bottom edge of 
 *   viewport), so tooltip always in visible viewport, by pushing it to the left 
 *   and/or up.
 * - Event name text auto-selected upon every tooltip display, so can now 
 *   easily copy name without manual selection, as well every hover if lost 
 *   selection. 
 * - Numbered arguments list.
 * - Long event names will produce ellipsis, though still fully selectable.
 * - Filtered out PHP events that break JavaScript and asynchronous calls, 
 *   so Eventi can now always be enabled and the site will not break anymore.
 * - Can now move mouse over tooltip without fighting for focus position, so 
 *   any text in the tooltip can now be selected.
 * - JavaScript delegates Eventi attached events, so newly added eventi elements 
 *   still fire their events, despite being added asynchronously or post-load.
 * - Control panel: option to show or hide Eventi flags on the fly, by checking 
 *   the option to the bottom-right of the page, and is remembered between 
 *   refreshes. The reason for this functionality is to prevent the dev from 
 *   flipping back and forth between the dashboard to enable/disable the plugin. 
 *   Now the plugin can just be left enabled, and hidden or shown when needed, 
 *   easily from any page. This is now possible due to Ajax fix mentioned above.
 * - Added minor delay to tooltip, so that when moving mouse over flags quickly, 
 *   they do not immediately display.
 * - Arrays are now printed to HTML title attributes, so hover over any 
 *   argument in the list which states it is an array to reveal the contents. 
 *   Not all array information can be seen on large dumps, but it doesn't need 
 *   to be all available. It's just a hint.
 * - Strings are now passed through htmlentities and sliced, as some may contain 
 *   HTML, which affects the output of the tooltip. 
 * - Unique fragment identifiers added for every event flag, to easily link 
 *   to flags in the page by appending the URL with #eventi-MD5String, and if 
 *   on page with flag targeted, it will be disabled on hover of itself.
 */

class EventiPlugin extends Gdn_Plugin {

   public function Base_Render_Before($Sender) {
      $Sender->AddCssFile('eventi.css', 'plugins/Eventi');
      $Sender->AddJsFile('eventi.js', 'plugins/Eventi');
   }
      
   public function Base_All_Handler($Sender, $Args, $Key) {
      
      // These events break all asynchronous functionality on the site, as 
      // they insert themselves directly in a response, so do nothing with them.
      // Without this fix, having the plugin enabled makes it difficult to use 
      // the site properly, essentially requiring user to enable then disable it 
      // the moment they find the information they want.
      // There may be others.
      $ajaxBreakEvents = array(
          'Gdn_Controller_Finalize_Handler', 
          'Gdn_Dispatcher_Cleanup_Handler'
      );
      
      if (in_array($Key, $ajaxBreakEvents)) {
         return;
      }

      $Caller = $Sender->EventArguments['WildEventStack'];
      $CallerFile = str_replace(Gdn::Request()->GetValue('DOCUMENT_ROOT'), '', $Caller['file']);

      $Object = GetValue('object', $Caller, '');
      
      if (is_object($Object)) { 
         $Object = get_class($Object); 
      }
      
      if (strlen($Object)) {
         $Object .= "::";
      }
      
      $ArgList = array();
      
      foreach ($Caller['args'] as $Arg) {
         if (is_object($Arg)) {
            $ArgList[] = get_class($Arg);
         } elseif (is_array($Arg)) {
            $ArgList[] = 'array{'. sizeof($Arg) .'}';
         } elseif (is_string($Arg) || is_numeric($Arg)) {
            $ArgList[] = "'". $Arg ."'";
         } elseif (is_bool($Arg)) {
            $ArgList[] = "b". (string)$Arg;
         } else {
            $ArgList[] = $Arg;
         }
      }

      $htmlArgsList = '';
      // EventArguments List
      if (sizeof($Sender->EventArguments) > 1) {

         $htmlArgsList .= '
            <strong>Arguments</strong>
            <ol>';

               foreach ($Sender->EventArguments as $ArgKey => $ArgValue) {
                  if ($ArgKey == "WildEventStack") continue;

                  if (is_object($ArgValue)) {
                     $ArgValue = get_class($ArgValue);
                  } elseif (is_array($ArgValue)) {
                     // Dump the results of array to HTML title attribute
                     $ArgValue = '<span title="'. htmlentities(print_r($ArgValue, true)) .'">array{'.sizeof($ArgValue).'}</span>';
                  } elseif (is_string($ArgValue) || is_numeric($ArgValue)) {
                     // Clean up strings, as some may contain HTML, which 
                     // ruins the output in the toltip.
                     $stringClean = htmlentities($ArgValue);
                     $ArgValue = '<span title="'. $stringClean .'">\''. SliceString($stringClean, 1000) .'\'</span>';
                  } elseif (is_bool($ArgValue)) {
                     $ArgValue = "b".(string)$ArgValue;
                  }

                  $htmlArgsList .= '<li><em>'. $ArgKey .':</em> '. $ArgValue .'</li>';
               }

         $htmlArgsList .= '
            </ol>';
      }
      
      $callerFileAndLine = $CallerFile .':'. $Caller['line'];
      
      // This is used so that Eventi events can be linked to and shared, using 
      // Url fragment identifiers.
      $eventiHtmlFragmentId = md5($htmlArgsList . $callerFileAndLine);
      
      $html = '
      <div class="eventi" id="eventi-'. $eventiHtmlFragmentId .'">
         <span class="eventi-flag-hook"></span>
         <div class="eventi-tooltip">
            <input type="text" class="eventi-event-name" value="'. $Key .'" readonly="readonly" title="&#8984;/Ctrl+C to copy event name. It\'s auto-selected on every tooltip." />
            <div class="eventi-tooltip-body">
               <div class="eventi-subhead">
                  <div class="eventi-fire-event">'. $Object . $Caller['function'] .' ('. implode(',', $ArgList). ')</div>
                  <div class="eventi-file-line">'. $callerFileAndLine .'</div>
               </div>';      
            
               $html .= $htmlArgsList;
      
            $html .= '
            </div><!--/.eventi-tooltip-body-->
            <input type="text" class="eventi-share-link" readonly="readonly" value="'. Gdn::Request()->Url('', true) .'#eventi-'. $eventiHtmlFragmentId .'" title="Eventi URL fragment identifier." />
         </div><!--/.eventi-tooltip-->
      </div><!--/.eventi-->';
      
      echo $html;
   }
}
