<?php
/*
Extension Name: Atom Feed
Extension Url: http://lussumo.com/docs/
Description: Adds a link to an Atom feed on any applicable pages of Vanilla (discussion index, comments page, search results, etc)
Version: 1.0.1
Author: Mark O'Sullivan
Author Url: http://markosullivan.ca/

Copyright 2003 - 2005 Mark O'Sullivan
This file is part of Vanilla.
Vanilla is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
Vanilla is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
The latest source code for Vanilla is available at www.lussumo.com
Contact Mark O'Sullivan at mark [at] lussumo [dot] com

You should cut & paste these language definitions into your conf/your_language.php file
(replace "your_language" with your chosen language, of course):
*/

if (!defined('IN_VANILLA')) exit();

$Context->Dictionary['AtomFeed'] = 'Atom';
$Context->Dictionary['FailedFeedAuthenticationTitle'] = 'Failed Authentication';
$Context->Dictionary['FailedFeedAuthenticationText'] = 'Feeds for this forum require user authentication.';


$FeedType = ForceIncomingString('Feed', '');
$AuthenticateUser = 0;
if ($FeedType == 'Atom') {
   // Include the atom extension functions
   include($Configuration['EXTENSIONS_PATH'].'/Atom/functions.php');
   // Make sure that page is not redirected if the user is not signed in and this is not a public forum
   if ($Context->Session->UserID == 0 && !$Configuration['PUBLIC_BROWSING']) {
      // Temporarily make the PUBLIC_BROWSING enabled, but make sure to tell atom to validate this user
      $Configuration['PUBLIC_BROWSING'] = 1;
      $Context->Configuration['AUTHENTICATE_USER_FOR_ATOM'] = 1;
   } else {
      $Context->Configuration['AUTHENTICATE_USER_FOR_ATOM'] = 0;
   }
}

if (in_array($Context->SelfUrl, array('index.php', 'search.php', 'comments.php'))) {
   // Set up the atom feed for the foot of the page
   $p = $Context->ObjectFactory->NewObject($Context, 'Parameters');
   $p->DefineCollection($_GET);
   $p->Set('Feed', 'Atom');
   include($Configuration['EXTENSIONS_PATH'].'/Atom/global_functions.php');   
}

if ($Context->SelfUrl == 'index.php') {
   // Add the atom link to the foot
   $FeedUrl = GetFeedUriForAtom($Configuration, $p);
   $FeedUrl = 'http://feeds.feedburner.com/musiques-incongrues';
   $FeedText = $Context->GetDefinition('Feeds');
   /*$Panel->AddList($FeedText, 100);
   $Panel->AddListItem($FeedText,
      $Context->GetDefinition('AtomFeed'),
      $FeedUrl);*/
   $Head->AddString('<link rel="alternate" type="application/atom+xml" href="'.$FeedUrl.'" title="'.$Context->GetDefinition('AtomFeed').'" />');

   // Add the discussion indexes atom feed
   if ($FeedType == 'Atom') {

      $Context->AddToDelegate('DiscussionManager',
         'PostGetDiscussionBuilder',
         'DiscussionManager_GetFirstCommentForAtom');
      
      // Attach to the Constructor Delegate of the DiscussionGrid control
      function DiscussionGrid_InjectAtomFeed($DiscussionGrid) {
         if ($DiscussionGrid->Context->WarningCollector->Count() == 0) {
            // Authenticate the user
            AuthenticateUserForAtom($DiscussionGrid->Context);
            
            // Loop through the data
            $Feed = '';
            $Properties = array();
            while ($DataSet = $DiscussionGrid->Context->Database->GetRow($DiscussionGrid->DiscussionData)) {
               $Properties['Title'] = FormatHtmlStringInline(ForceString($DataSet['Name'], ''));
               $Properties['Link'] = GetUrl($DiscussionGrid->Context->Configuration, 'comments.php', '', 'DiscussionID', ForceInt($DataSet['DiscussionID'], 0));
               $Properties['Published'] = FixDateForAtom(@$DataSet['DateCreated']);
               $Properties['Updated'] = FixDateForAtom(@$DataSet['DateLastActive']);
               $Properties['AuthorName'] = FormatHtmlStringInline(ForceString($DataSet['AuthUsername'], ''));
               $Properties['AuthorUrl'] = GetUrl($DiscussionGrid->Context->Configuration, 'account.php', '', 'u', ForceInt($DataSet['AuthUserID'], 0));
               
               // Format the comment according to the defined formatter for that comment
               $FormatType = ForceString(@$DataSet['FormatType'], $DiscussionGrid->Context->Configuration['DEFAULT_FORMAT_TYPE']);
               $Properties['Content'] = $DiscussionGrid->Context->FormatString(@$DataSet['Body'], $DiscussionGrid, $FormatType, FORMAT_STRING_FOR_DISPLAY);
               $Properties['Summary'] = FormatStringForAtomSummary(@$DataSet['Body']);
               
               $Feed .= ReturnFeedItemForAtom($Properties);
            }
            
            $Feed = ReturnWrappedFeedForAtom($DiscussionGrid->Context, $Feed);
            
            // Set the content type to xml
            header("Content-type: text/xml\n");
            
            // Dump the feed
            echo($Feed);
      
            // When all finished, unload the context object
            $DiscussionGrid->Context->Unload();
            
            // And now stop processing the page
            die();
         }
      }
      
      $Context->AddToDelegate('DiscussionGrid',
         'Constructor',
         'DiscussionGrid_InjectAtomFeed');
   }
} elseif ($Context->SelfUrl == 'search.php') {
   // Add the atom link to the foot
   $SearchType = ForceIncomingString('Type', '');
   $SearchID = ForceIncomingInt('SearchID', 0);
   if ($SearchType == '' && $SearchID > 0) {
      $SearchManager = $Context->ObjectFactory->NewContextObject($Context, 'SearchManager');
      $Search = $SearchManager->GetSearchById($SearchID);
      if ($Search) $SearchType = $Search->Type;
   }
   if ($SearchType == 'Topics' || $SearchType == 'Comments') {
      $FeedUrl = GetFeedUriForAtom($Configuration, $p);
      $FeedText = $Context->GetDefinition('Feeds');
      /*
      $Panel->AddList($FeedText, 100);
      $Panel->AddListItem($FeedText,
         $Context->GetDefinition('AtomFeed'),
         $FeedUrl);*/
      $Head->AddString('<link rel="alternate" type="application/atom+xml" href="'.$FeedUrl.'" title="'.$Context->GetDefinition('AtomFeed').'" />');
   }
   
   // Handle searches
   if ($FeedType == 'Atom') {      
      // Topic Search Results
      if ($SearchType == 'Topics') {   
         // Make sure that the first comment is also grabbed from the search
         $Context->AddToDelegate('DiscussionManager',
            'PostGetDiscussionBuilder',
            'DiscussionManager_GetFirstCommentForAtom');
         
         // Attach to the PostLoadData Delegate of the SearchForm control
         function SearchForm_InjectAtomFeedToTopicSearch($SearchForm) {
            if ($SearchForm->Context->WarningCollector->Count() == 0) {   
               // Authenticate the user
               AuthenticateUserForAtom($SearchForm->Context);
               
               // Loop through the data
               $Counter = 0;
               $Feed = '';
               $Properties = array();
               while ($DataSet = $SearchForm->Context->Database->GetRow($SearchForm->Data)) {
                  if ($Counter < $SearchForm->Context->Configuration['SEARCH_RESULTS_PER_PAGE']) {
                     $Properties['Title'] = FormatHtmlStringInline(ForceString($DataSet['Name'], ''));
                     $Properties['Link'] = GetUrl($SearchForm->Context->Configuration, 'comments.php', '', 'DiscussionID', ForceInt($DataSet['DiscussionID'], 0));
                     $Properties['Published'] = FixDateForAtom(@$DataSet['DateCreated']);
                     $Properties['Updated'] = FixDateForAtom(@$DataSet['DateLastActive']);
                     $Properties['AuthorName'] = FormatHtmlStringInline(ForceString($DataSet['AuthUsername'], ''));
                     $Properties['AuthorUrl'] = GetUrl($SearchForm->Context->Configuration, 'account.php', '', 'u', ForceInt($DataSet['AuthUserID'], 0));
                     
                     // Format the comment according to the defined formatter for that comment
                     $FormatType = ForceString(@$DataSet['FormatType'], $SearchForm->Context->Configuration['DEFAULT_FORMAT_TYPE']);
                     $Properties['Content'] = $SearchForm->Context->FormatString(@$DataSet['Body'], $SearchForm, $FormatType, FORMAT_STRING_FOR_DISPLAY);
                     $Properties['Summary'] = FormatStringForAtomSummary(@$DataSet['Body']);
                     
                     $Feed .= ReturnFeedItemForAtom($Properties);
                  }
                  $Counter++;
               }
               
               $Feed = ReturnWrappedFeedForAtom($SearchForm->Context, $Feed);
               
               // Set the content type to xml
               header("Content-type: text/xml\n");
               
               // Dump the feed
               echo($Feed);
         
               // When all finished, unload the context object
               $SearchForm->Context->Unload();
               
               // And now stop processing the page
               die();
            }
         }
         
         $Context->AddToDelegate('SearchForm',
            'PostLoadData',
            'SearchForm_InjectAtomFeedToTopicSearch');
      } elseif ($SearchType == 'Comments') {
         
         // Attach to the PostLoadData Delegate of the SearchForm control
         function SearchForm_InjectAtomFeedToCommentSearch($SearchForm) {
            if ($SearchForm->Context->WarningCollector->Count() == 0) {
               
               // Authenticate the user
               AuthenticateUserForAtom($SearchForm->Context);
               
               // Loop through the data
               $Counter = 0;
               $Feed = '';
               $Properties = array();
               $Comment = $SearchForm->Context->ObjectFactory->NewContextObject($SearchForm->Context, 'Comment');
               while ($Row = $SearchForm->Context->Database->GetRow($SearchForm->Data)) {
                  $Comment->Clear();
                  $Comment->GetPropertiesFromDataSet($Row, $SearchForm->Context->Session->UserID);
                  $Comment->FormatPropertiesForDisplay();
                     
                  if ($Counter < $SearchForm->Context->Configuration['SEARCH_RESULTS_PER_PAGE']) {
                     $Properties['Title'] = $Comment->Discussion;
                     $Properties['Link'] = GetUrl($SearchForm->Context->Configuration, 'comments.php', '', 'DiscussionID', $Comment->DiscussionID, '', 'Focus='.$Comment->CommentID.'#Comment_'.$Comment->CommentID);
                     $Properties['Published'] = FixDateForAtom(@$Row['DateCreated']);
                     $Properties['Updated'] = FixDateForAtom(@$Row['DateEdited']);
                     $Properties['AuthorName'] = $Comment->AuthUsername;
                     $Properties['AuthorUrl'] = GetUrl($SearchForm->Context->Configuration, 'account.php', '', 'u', $Comment->AuthUserID);
                     
                     // Format the comment according to the defined formatter for that comment
                     $Properties['Content'] = $Comment->Body;
                     $Properties['Summary'] = FormatStringForAtomSummary(@$Row['Body']);
                     
                     $Feed .= ReturnFeedItemForAtom($Properties);
                  }
                  $Counter++;
               }
               
               $Feed = ReturnWrappedFeedForAtom($SearchForm->Context, $Feed);
               
               // Set the content type to xml
               header("Content-type: text/xml\n");
               
               // Dump the feed
               echo($Feed);
         
               // When all finished, unload the context object
               $SearchForm->Context->Unload();
               
               // And now stop processing the page
               die();
            }
         }
         
         $Context->AddToDelegate('SearchForm',
            'PostLoadData',
            'SearchForm_InjectAtomFeedToCommentSearch');
         
      }
   }
} elseif ($Context->SelfUrl == 'comments.php') {
   $FeedUrl = GetFeedUriForAtom($Configuration, $p);
   $FeedText = $Context->GetDefinition('Feeds');
   /*$Panel->AddList($FeedText, 100);
   $Panel->AddListItem($FeedText,
      $Context->GetDefinition('AtomFeed'),
      $FeedUrl);*/

   $Head->AddString('<link rel="alternate" type="application/atom+xml" href="'.$FeedUrl.'" title="'.$Context->GetDefinition('AtomFeed').'" />');
   
   // Handle searches
   if ($FeedType == 'Atom') {      
      // Attach to the Constructor Delegate of the CommentGrid control
      function CommentGrid_InjectAtomFeed($CommentGrid) {
         if ($CommentGrid->Context->WarningCollector->Count() == 0) {
            // Authenticate the user
            AuthenticateUserForAtom($CommentGrid->Context);
            
            // Make sure the page title is defined
            $CommentGrid->Context->PageTitle = $CommentGrid->Discussion->Name;
            
            // Loop through the data
            $Feed = '';
            $Properties = array();
            $Comment = $CommentGrid->Context->ObjectFactory->NewContextObject($CommentGrid->Context, 'Comment');
            while ($Row = $CommentGrid->Context->Database->GetRow($CommentGrid->CommentData)) {
               $Comment->Clear();
               $Comment->GetPropertiesFromDataSet($Row, $CommentGrid->Context->Session->UserID);
               $Comment->FormatPropertiesForDisplay();
                  
               $Properties['Title'] = $CommentGrid->Discussion->Name;
               $Properties['Link'] = GetUrl($CommentGrid->Context->Configuration, 'comments.php', '', 'DiscussionID', $Comment->DiscussionID, '', 'Focus='.$Comment->CommentID.'#Comment_'.$Comment->CommentID);
               $Properties['Published'] = FixDateForAtom(@$Row['DateCreated']);
               $Properties['Updated'] = FixDateForAtom(@$Row['DateEdited']);
               $Properties['AuthorName'] = $Comment->AuthUsername;
               $Properties['AuthorUrl'] = GetUrl($CommentGrid->Context->Configuration, 'account.php', '', 'u', $Comment->AuthUserID);
               
               // Format the comment according to the defined formatter for that comment
               $Properties['Content'] = $Comment->Body;
               $Properties['Summary'] = FormatStringForAtomSummary(@$Row['Body']);
               
               $Feed .= ReturnFeedItemForAtom($Properties);
            }
            
            $Feed = ReturnWrappedFeedForAtom($CommentGrid->Context, $Feed);
            
            // Set the content type to xml
            header("Content-type: text/xml\n");
            
            // Dump the feed
            echo($Feed);
      
            // When all finished, unload the context object
            $CommentGrid->Context->Unload();
            
            // And now stop processing the page
            die();
         }
      }
      
      $Context->AddToDelegate('CommentGrid',
         'Constructor',
         'CommentGrid_InjectAtomFeed');
      
   }
}
?>
