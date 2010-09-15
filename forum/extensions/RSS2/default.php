<?php
/*
Extension Name: RSS2 Feed
Extension Url: http://lussumo.com/docs/
Description: Adds a link to an RSS2 feed on any applicable pages of Vanilla (discussion index, comments page, search results, etc)
Version: 1.0.2
Author: Mark O'Sullivan
Author Url: http://markosullivan.ca/

Copyright 2003 - 2006 Mark O'Sullivan
This file is part of Vanilla.
Vanilla is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
Vanilla is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
The latest source code for Vanilla is available at www.lussumo.com
Contact Mark O'Sullivan at mark [at] lussumo [dot] com

You should cut & paste these language definitions into your
conf/your_language.php file (replace "your_language" with your chosen language,
of course):
*/
if (!defined('IN_VANILLA')) exit();

$Context->Dictionary["RSS2Feed"] = "RSS2";
$Context->Dictionary["FailedFeedAuthenticationTitle"] = "Failed Authentication";
$Context->Dictionary["FailedFeedAuthenticationText"] = "Feeds for this forum require user authentication.";


$FeedType = ForceIncomingString("Feed", "");
$AuthenticateUser = 0;
if ($FeedType == "RSS2") {
   // Include the RSS2 extension functions
   include($Configuration["EXTENSIONS_PATH"]."/RSS2/functions.php");
   // Make sure that page is not redirected if the user is not signed in and this is not a public forum
   if ($Context->Session->UserID == 0 && !$Configuration["PUBLIC_BROWSING"]) {
      // Temporarily make the PUBLIC_BROWSING enabled, but make sure to tell RSS2 to validate this user
      $Configuration["PUBLIC_BROWSING"] = 1;
      $Context->Configuration["AUTHENTICATE_USER_FOR_RSS2"] = 1;
   } else {
      $Context->Configuration["AUTHENTICATE_USER_FOR_RSS2"] = 0;
   }
}

if (in_array($Context->SelfUrl, array("index.php", "search.php", "comments.php"))) {
   // Set up the RSS2 feed for the foot of the page
   $p = $Context->ObjectFactory->NewObject($Context, "Parameters");
   $p->DefineCollection($_GET);
   $p->Set("Feed", "RSS2");
   include($Configuration["EXTENSIONS_PATH"]."/RSS2/global_functions.php");
}

if ($Context->SelfUrl == "index.php") {
   // Add the RSS2 link to the foot
   $FeedUrl = GetFeedUriForRSS2($Configuration, $p);
   $FeedText = $Context->GetDefinition("Feeds");
   /*$Panel->AddList($FeedText, 100);
   $Panel->AddListItem($FeedText,
      $Context->GetDefinition("RSS2Feed"),
      $FeedUrl);*/
   $Head->AddString("<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$FeedUrl."\" title=\"".$Context->GetDefinition("RSS2Feed")."\" />");

   // Add the discussion indexes RSS2 feed
   if ($FeedType == "RSS2") {

      $Context->AddToDelegate("DiscussionManager",
         "PostGetDiscussionBuilder",
         "DiscussionManager_GetFirstCommentForRSS2");
      
      // Attach to the Constructor Delegate of the DiscussionGrid control
      function DiscussionGrid_InjectRSS2Feed($DiscussionGrid) {
         if ($DiscussionGrid->Context->WarningCollector->Count() == 0) {
            // Authenticate the user
            AuthenticateUserForRSS2($DiscussionGrid->Context);
            
            // Loop through the data
            $Feed = "";
            $Properties = array();
            while ($DataSet = $DiscussionGrid->Context->Database->GetRow($DiscussionGrid->DiscussionData)) {
               $Properties["Title"] = FormatHtmlStringInline(ForceString($DataSet["Name"], ""));
               $Properties["Link"] = GetUrl($DiscussionGrid->Context->Configuration, "comments.php", "", "DiscussionID", ForceInt($DataSet["DiscussionID"], 0));
               $Properties["Published"] = FixDateForRSS2(@$DataSet["DateCreated"]);
               $Properties["Updated"] = FixDateForRSS2(@$DataSet["DateLastActive"]);
               $Properties["AuthorName"] = FormatHtmlStringInline(ForceString($DataSet["AuthUsername"], ""));
               $Properties["AuthorUrl"] = GetUrl($DiscussionGrid->Context->Configuration, "account.php", "", "u", ForceInt($DataSet["AuthUserID"], 0));
               
               // Format the comment according to the defined formatter for that comment
               $FormatType = ForceString(@$DataSet["FormatType"], $DiscussionGrid->Context->Configuration["DEFAULT_FORMAT_TYPE"]);
               $Properties["Content"] = $DiscussionGrid->Context->FormatString(@$DataSet["Body"], $DiscussionGrid, $FormatType, FORMAT_STRING_FOR_DISPLAY);
               $Properties["Summary"] = FormatStringForRSS2Summary(@$DataSet["Body"]);
               
               $Feed .= ReturnFeedItemForRSS2($Properties);
            }
            
            $Feed = ReturnWrappedFeedForRSS2($DiscussionGrid->Context, $Feed);
            
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
      
      $Context->AddToDelegate("DiscussionGrid",
         "Constructor",
         "DiscussionGrid_InjectRSS2Feed");
   }
} elseif ($Context->SelfUrl == "search.php") {
   // Add the RSS2 link to the foot
   $SearchType = ForceIncomingString("Type", "");
   $SearchID = ForceIncomingInt("SearchID", 0);
   if ($SearchType == "" && $SearchID > 0) {
      $SearchManager = $Context->ObjectFactory->NewContextObject($Context, "SearchManager");
      $Search = $SearchManager->GetSearchById($SearchID);
      if ($Search) $SearchType = $Search->Type;
   }
   if ($SearchType == "Topics" || $SearchType == "Comments") {
      $FeedUrl = GetFeedUriForRSS2($Configuration, $p);
      $FeedText = $Context->GetDefinition("Feeds");
      /*$Panel->AddList($FeedText, 100);
      $Panel->AddListItem($FeedText,
         $Context->GetDefinition("RSS2Feed"),
         $FeedUrl);*/
      $Head->AddString("<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$FeedUrl."\" title=\"".$Context->GetDefinition("RSS2Feed")."\" />");
   }
   
   // Handle searches
   if ($FeedType == "RSS2") {      
      // Topic Search Results
      if ($SearchType == "Topics") {   
         // Make sure that the first comment is also grabbed from the search
         $Context->AddToDelegate("DiscussionManager",
            "PostGetDiscussionBuilder",
            "DiscussionManager_GetFirstCommentForRSS2");
         
         // Attach to the PostLoadData Delegate of the SearchForm control
         function SearchForm_InjectRSS2FeedToTopicSearch($SearchForm) {
            if ($SearchForm->Context->WarningCollector->Count() == 0) {   
               // Authenticate the user
               AuthenticateUserForRSS2($SearchForm->Context);
               
               // Loop through the data
               $Counter = 0;
               $Feed = "";
               $Properties = array();
               while ($DataSet = $SearchForm->Context->Database->GetRow($SearchForm->Data)) {
                  if ($Counter < $SearchForm->Context->Configuration["SEARCH_RESULTS_PER_PAGE"]) {
                     $Properties["Title"] = FormatHtmlStringInline(ForceString($DataSet["Name"], ""));
                     $Properties["Link"] = GetUrl($SearchForm->Context->Configuration, "comments.php", "", "DiscussionID", ForceInt($DataSet["DiscussionID"], 0));
                     $Properties["Published"] = FixDateForRSS2(@$DataSet["DateCreated"]);
                     $Properties["Updated"] = FixDateForRSS2(@$DataSet["DateLastActive"]);
                     $Properties["AuthorName"] = FormatHtmlStringInline(ForceString($DataSet["AuthUsername"], ""));
                     $Properties["AuthorUrl"] = GetUrl($SearchForm->Context->Configuration, "account.php", "", "u", ForceInt($DataSet["AuthUserID"], 0));
                     
                     // Format the comment according to the defined formatter for that comment
                     $FormatType = ForceString(@$DataSet["FormatType"], $SearchForm->Context->Configuration["DEFAULT_FORMAT_TYPE"]);
                     $Properties["Content"] = $SearchForm->Context->FormatString(@$DataSet["Body"], $SearchForm, $FormatType, FORMAT_STRING_FOR_DISPLAY);
                     $Properties["Summary"] = FormatStringForRSS2Summary(@$DataSet["Body"]);
                     
                     $Feed .= ReturnFeedItemForRSS2($Properties);
                  }
                  $Counter++;
               }
               
               $Feed = ReturnWrappedFeedForRSS2($SearchForm->Context, $Feed);
               
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
         
         $Context->AddToDelegate("SearchForm",
            "PostLoadData",
            "SearchForm_InjectRSS2FeedToTopicSearch");
      } elseif ($SearchType == "Comments") {
         
         // Attach to the PostLoadData Delegate of the SearchForm control
         function SearchForm_InjectRSS2FeedToCommentSearch($SearchForm) {
            if ($SearchForm->Context->WarningCollector->Count() == 0) {   
               // Authenticate the user
               AuthenticateUserForRSS2($SearchForm->Context);
               
               // Loop through the data
               $Counter = 0;
               $Feed = "";
               $Properties = array();
               $Comment = $SearchForm->Context->ObjectFactory->NewContextObject($SearchForm->Context, "Comment");
               while ($Row = $SearchForm->Context->Database->GetRow($SearchForm->Data)) {
                  $Comment->Clear();
                  $Comment->GetPropertiesFromDataSet($Row, $SearchForm->Context->Session->UserID);
                  $Comment->FormatPropertiesForDisplay();
                     
                  if ($Counter < $SearchForm->Context->Configuration["SEARCH_RESULTS_PER_PAGE"]) {
                     $Properties["Title"] = $Comment->Discussion;
                     $Properties["Link"] = GetUrl($SearchForm->Context->Configuration, "comments.php", "", "DiscussionID", $Comment->DiscussionID, "", "Focus=".$Comment->CommentID."#Comment_".$Comment->CommentID);
                     $Properties["Published"] = FixDateForRSS2(@$Row["DateCreated"]);
                     $Properties["Updated"] = FixDateForRSS2(@$Row["DateEdited"]);
                     $Properties["AuthorName"] = $Comment->AuthUsername;
                     $Properties["AuthorUrl"] = GetUrl($SearchForm->Context->Configuration, "account.php", "", "u", $Comment->AuthUserID);
                     
                     // Format the comment according to the defined formatter for that comment
                     $Properties["Content"] = $Comment->Body;
                     $Properties["Summary"] = FormatStringForRSS2Summary(@$Row["Body"]);
                     
                     $Feed .= ReturnFeedItemForRSS2($Properties);
                  }
                  $Counter++;
               }
               
               $Feed = ReturnWrappedFeedForRSS2($SearchForm->Context, $Feed);
               
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
         
         $Context->AddToDelegate("SearchForm",
            "PostLoadData",
            "SearchForm_InjectRSS2FeedToCommentSearch");
         
      }
   }
} elseif ($Context->SelfUrl == "comments.php") {
   $FeedUrl = GetFeedUriForRSS2($Configuration, $p);
   //GetUrl($Configuration, "comments.php", "", "DiscussionID", ForceIncomingInt("DiscussionID", ""), "", $p->GetQueryString());
   $FeedText = $Context->GetDefinition("Feeds");
   /*$Panel->AddList($FeedText, 100);
   $Panel->AddListItem($FeedText,
      $Context->GetDefinition("RSS2Feed"),
      $FeedUrl);*/
   $Head->AddString("<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$FeedUrl."\" title=\"".$Context->GetDefinition("RSS2Feed")."\" />");
   
   // Handle searches
   if ($FeedType == "RSS2") {      
      // Attach to the Constructor Delegate of the CommentGrid control
      function CommentGrid_InjectRSS2Feed($CommentGrid) {
         if ($CommentGrid->Context->WarningCollector->Count() == 0) {   
            // Authenticate the user
            AuthenticateUserForRSS2($CommentGrid->Context);
            
            // Make sure the page title is defined
            $CommentGrid->Context->PageTitle = $CommentGrid->Discussion->Name;
            
            // Loop through the data
            $Feed = "";
            $Properties = array();
            $Comment = $CommentGrid->Context->ObjectFactory->NewContextObject($CommentGrid->Context, "Comment");
            while ($Row = $CommentGrid->Context->Database->GetRow($CommentGrid->CommentData)) {
               $Comment->Clear();
               $Comment->GetPropertiesFromDataSet($Row, $CommentGrid->Context->Session->UserID);
               $Comment->FormatPropertiesForDisplay();
                  
               $Properties["Title"] = $CommentGrid->Discussion->Name;
               $Properties["Link"] = GetUrl($CommentGrid->Context->Configuration, "comments.php", "", "DiscussionID", $Comment->DiscussionID, "", "Focus=".$Comment->CommentID."#Comment_".$Comment->CommentID);
               $Properties["Published"] = FixDateForRSS2(@$Row["DateCreated"]);
               $Properties["Updated"] = FixDateForRSS2(@$Row["DateEdited"]);
               $Properties["AuthorName"] = $Comment->AuthUsername;
               $Properties["AuthorUrl"] = GetUrl($CommentGrid->Context->Configuration, "account.php", "", "u", $Comment->AuthUserID);
               
               // Format the comment according to the defined formatter for that comment
               $Properties["Content"] = $Comment->Body;
               $Properties["Summary"] = FormatStringForRSS2Summary(@$Row["Body"]);
               
               $Feed .= ReturnFeedItemForRSS2($Properties);
            }
            
            $Feed = ReturnWrappedFeedForRSS2($CommentGrid->Context, $Feed);
            
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
      
      $Context->AddToDelegate("CommentGrid",
         "Constructor",
         "CommentGrid_InjectRSS2Feed");
      
   }
}
?>
