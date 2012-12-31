<?php
/*
Extension Name: SubCategories
Extension Url: http://webality.co.uk/
Description: Makes categories with a deliminator prepended to it's name a sub-category
Version: 0.2.3
Author: Adam Dunkley
Author Url: http://webality.co.uk/

Version History:
	0.1.0b - First Beta
	0.1.0 - First Release: Added parent categories links, finished removal of redundant dashes.
	0.1.1 - Fixed one bug with categories beginning with only "-" and showing up as sub-categories
	        in some places and another with category counts. General tidy up. Extrapolated 
	        SubCategories definition, and added a message to the Categories List Settings page.
	        Also fixed a conflict with the Blog add-on (checks whether CategoryManager class exists
	        before trying to include it).
	0.1.2 - Added some functions to ease theming, see readme.txt
	0.2.0b - Added the ability to have unlimited levels of sub-categories. Extrapolated sub-categories
	         theme into overridable theme file (see readme.txt). Blocking sub-categories now works.
	0.2.0 - Tidy up. Sub-categories now render by attaching to a delegate, which is overridable by
	        altering configuration variables. Orphaned categories are now detected and displayed in
	        some way. Problems with sub-categories being missed off of the end of sub-category list
	        when after further nesting of categories fixed. Fixed problems with breadcrumb links in
	        titles on discussions and comments pages. Fixed statistics for multiple nested categories.
	0.2.1 - Still problems calculating sub-category and sub-discussion statistics with complex nesting
	        of attached sub categories, and with breadcrumb of orphaned categories. Altered the way
	        configuration is setup.
	0.2.2 - Fixed a few problems with the deliminator causing havoc when placed later in the name and
	        > in name, also another little tidy up of the code.\
	0.2.3 - Includes a fix which provides compatibility for extensions that overload the CategoryManager
	        class (such as my forthcoming update to MultiRoles)
*/

/*
   Nothing in this default.php should be altered, you may override Options, Definitions and Theme files in 
   the settings and themes of your specific vanilla install - see readme.txt
*/

// Definitions

$Context->SetDefinition('SubCategories', 'Sub-Categories');
$Context->SetDefinition('SubCategoryAdmin', '</p><p>To add a sub-category, you must start your
   category name with "- " (minus the quotes), placing it below the non sub-category you wish to be it\'s parent.');
$Context->Dictionary['CategoryReorderNotes'] .= $Context->Dictionary['SubCategoryAdmin'];
$Context->SetDefinition('SubCategoryDeliminator', '- ');

// Configuration

if(!array_key_exists('SUBCATEGORIES_DELEGATE', $Configuration)) {
   AddConfigurationSetting($Context, 'SUBCATEGORIES_DELEGATE', 'PreRender');
}
if(!array_key_exists('SUBCATEGORIES_DELEGATE_SCOPE', $Configuration)) {
   AddConfigurationSetting($Context, 'SUBCATEGORIES_DELEGATE_SCOPE', 'DiscussionGrid');
}   

// Classes required for this extension

if(!class_exists('CategoryManager')) {
   include($Context->Configuration['APPLICATION_PATH'] . 'library/Vanilla/Vanilla.Class.CategoryManager.php');
}


// Some generic little functions

/*
  Find the parent of the category given as either a CategoryID or CategoryName (context can be given
  as a number)
  
  @param [$CategoryID] - ID of the category who's parent we want
  @param [$CategoryName] - Name of the category who's parent we want
  @param [$Level] - Level in hierachy category is
  
  @return $Parent - The first row from the result set (parent), associative array of category information
*/
function GetParent($CategoryID = 0, $CategoryName = '', $Level = 1) {
   global $Context;
   
   $DashPattern = '^';
     
   for($i = 0; $i < $Level; $i++) {
      $DashPattern .= $Context->GetDefinition('SubCategoryDeliminator');
   }
   
   $s = $Context->ObjectFactory->NewContextObject($Context, 'SqlBuilder');
   $s->SetMainTable('Category','c');
   $s->AddSelect(array('Priority'), 'c');
	 
   if ($CategoryID != 0) {
      $s->AddWhere('c', 'CategoryID', '', $CategoryID, '=');
   } elseif ($CategoryName != '') {
      $s->AddWhere('c', 'Name', '', $CategoryName, '=');
   }
	 
   $result = $Context->Database->Select($s, 'DiscussionGrid', 'RemoveCategory', 'An error occurred while fetching category priority.');
   $Priority = $Context->Database->GetRow($result);
   $s->Clear(); 
   $CategoryManager = $Context->ObjectFactory->NewContextObject($Context, 'CategoryManager');
   $s = $CategoryManager->GetCategoryBuilder(0, 1);

   if ($Level != 0) {
      $s->AddWhere('c', 'Name', '', $DashPattern, 'NOT REGEXP');
   }
	 
   $s->AddWhere('c', 'Priority', '', $Priority['Priority'], '<');
   $s->AddOrderBy('Priority', 'c', 'desc');
   $s->AddLimit(0, 1);
   $Data = $Context->Database->Select($s, 'SubCategories', 'GetCategories', 'An error occurred while retrieving categories.');
   $Parent = $Context->Database->GetRow($Data);
	 
	 return $Parent;
}

/*
   Tidy the deliminator from the front of the category's name
   
   @param $CategoryName - The category name we are cleaning up
   
   @return $TidyName - The clean name
*/
function SubNameTidy($CategoryName) {
    global $Context;

	$TidyName = ltrim($CategoryName, $Context->GetDefinition('SubCategoryDeliminator'));
	
	return $TidyName;

}

/*
   Grab the level indicated by the amount of deliminators at the front of the categories name
   
   @param $CategoryName - The category name we are finding the level of
   
   @return $Level - The level of the category
*/
function CategoryLevel($CategoryName) {
   global $Context;

   $Offset = substr_count(SubNameTidy($CategoryName), $Context->GetDefinition('SubCategoryDeliminator'));
   
   $Level = substr_count($CategoryName, $Context->GetDefinition('SubCategoryDeliminator')) - $Offset;
   
   return $Level;

}

/*
   Is the category given as CategoryName a sub-category, and, if a current active category given as
   CurrentName is given, is it a sub-category of the active category (in context). Also, should we be 
   zealous in our assessment - do we want to grab all categories, or only direct children.

   @param $CategoryName - The category name we want to check as being a sub-category
   @param [$CurrentName] - The name of the category currently being viewed (to provide context)
   @param [$Zealous] - Should we return regardless of being direct child of $CurrentName
   
   @return $SubCategory - Bool: true if a sub-category (of current category if not zealous), 
                          otherwise false
*/
function IsSubCategory($CategoryName, $CurrentName = '', $Zealous = false) {

   // Fix inconsistency with handling of >
   $CurrentName = str_replace('&gt;', '>', $CurrentName);

   $Level = CategoryLevel($CategoryName);
   
   if($Zealous != false && $Level > 1) {
      return true;
   }

   if($CurrentName != '' && $Level > 0) {
      $Parent = GetParent(0, $CategoryName, $Level);

      
      if($Parent['Name'] == $CurrentName) {
      	return true;
      } else {
         return false;
      }
      
   } else {
   
      if($Level > 0) {
         return true;
      } else {
         return false;
      }
      
   }

   if($Level == 1) {
      return true;
   } else {
      return false;
   }

}

// Theme functions

/*
   Count the amount of categories or discussions attached to a category (even within sub categories)

   @param $CategoryID - The category ID who's sub-categories need a count
   @param $Return - "discussions" returns a count of discussions, "categories" returns a count of 
                    categories
   
   @return $Count - The amount discussions/categories including in sub-categories
*/
function SubCount($CategoryID, $Return = 'discussions') {
		global $Context;
		
		$CategoryManager = $Context->ObjectFactory->NewContextObject($Context, 'CategoryManager');
		$Data = $CategoryManager->GetCategories(1);
		
		$Category = $Context->ObjectFactory->NewObject($Context, 'Category');
		$BeginSubForums = 0;
		$SubForums = 0;
		$CurrentName = '';
		
		$Count = 0;
		
		$Level = false;
		
		while ($Row = $Context->Database->GetRow($Data)) {
   			$Category->Clear();
  			$Category->GetPropertiesFromDataSet($Row);
			$Category->FormatPropertiesForDisplay();
			
			if ($Category->CategoryID == $CategoryID) {
   				$BeginSubForums = 1;
   				$CurrentName = $Category->Name;
   				$Level = CategoryLevel($CurrentName);
   				if($Return == 'discussions') {
   					$Count += $Category->DiscussionCount;
   				}
   			}
   			
   			if($BeginSubForums == 1) {
              
               if($Category->Name != $CurrentName) {			  
   				   
   				  if(CategoryLevel($Category->Name) <= $Level) {
				      break;
				   }

   				   if($Return == 'discussions') {
   				      $Count += $Category->DiscussionCount;
   				   } elseif($Return == 'categories') {
   				      $Count++;
   				   }
   				   
      		   }
      		  
      	    }
   			
		}
		
	return $Count;

}

// Legacy - for backward compatibility in theming only

function SubDiscussionCount($CategoryID) {
   return SubCount($CategoryID, 'discussions');
}

function SubCategoryCount($CategoryID) {
   return SubCount($CategoryID, 'categories');
}

// Render various elements

// Render the sub categories to the page
function RenderSubCategories(&$CategoryList) {
  global $Context;

  $CategoryManager = $Context->ObjectFactory->NewContextObject($Context, 'CategoryManager');
  $SubData = $CategoryManager->GetCategories(1);

  // If a custom theme exists, let's include it
  if(file_exists(ThemeFilePath($Context->Configuration, 'sub-categories.php'))) {
	 include(ThemeFilePath($Context->Configuration, 'sub-categories.php'));
  } else {
  
	 // Otherwise, grab it from the extension folder
	 if(file_exists($Context->Configuration['EXTENSIONS_PATH'].'SubCategories/theme/sub-categories.php')) {
		include($Context->Configuration['EXTENSIONS_PATH'].'SubCategories/theme/sub-categories.php');
	 }
		
  }
		
}
$Context->AddToDelegate($Context->Configuration['SUBCATEGORIES_DELEGATE_SCOPE'],
  $Context->Configuration['SUBCATEGORIES_DELEGATE'],
  'RenderSubCategories');

if (in_array($Context->SelfUrl, array('categories.php'))) {
   
   // Remove Sub Categories from the main categories page
   function CategoryList_RemoveSubCategories(&$CategoryList) {
   		global $Context;

   		$CategoryManager = $Context->ObjectFactory->NewContextObject($Context, 'CategoryManager');
		$s = $CategoryManager->GetCategoryBuilder(1);
		$s->AddWhere('c', 'Name', '', '^'.$Context->GetDefinition('SubCategoryDeliminator'), 'NOT REGEXP');
		$s->AddOrderBy('Priority', 'c', 'asc');
		$Data = $CategoryManager->Context->Database->Select($s, 'SubCategories', 'GetCategories', 'An error occurred while retrieving categories.');
		$CategoryList->Data = $Data;
   }
   
   $Context->AddToDelegate('CategoryList',
      'Constructor',
      'CategoryList_RemoveSubCategories');
      
}

// Clean up the "- " prefix from various places

if (in_array($Context->SelfUrl, array('index.php', ''))) {

   // Tidy up the DiscussionGrid for sub-forums
   function DiscussionGrid_RemoveCategoryPrefix(&$DiscussionGrid) {
      $DiscussionGrid->Context->Parent = false;
      
      if(IsSubCategory($DiscussionGrid->Context->PageTitle)) {
         $DiscussionGrid->Context->ParentCount = CategoryLevel($DiscussionGrid->Context->PageTitle);
         // Remove - from the front of the PageTitle
         $DiscussionGrid->Context->PageTitle = SubNameTidy($DiscussionGrid->Context->PageTitle);
      }
      
   }
   
   $Context->AddToDelegate('DiscussionGrid',
      'Constructor',
      'DiscussionGrid_RemoveCategoryPrefix');
      
   // Add parent category link pre-render
   function DiscussionGrid_AddParentLink(&$DiscussionGrid) {
      global $Context;
   
      if(!empty($DiscussionGrid->Context->ParentCount)) {
      
         $ParentCount = $DiscussionGrid->Context->ParentCount;
   
		  if($ParentCount > 0) {
			 $Parent = GetParent($_GET['CategoryID'], '', $ParentCount);
			 
			 for($i = 0; $i < $ParentCount; $i++) {
			 
			    if(!empty($Parent['Name'])) {
				   $DiscussionGrid->Context->PageTitle = '<a href="'.GetUrl($Context->Configuration, 'index.php', '', 'CategoryID', $Parent['CategoryID']).'">'.SubNameTidy($Parent['Name']) . '</a> > ' . $DiscussionGrid->Context->PageTitle;
				   if(CategoryLevel($Parent['Name']) == 0) {
				      break;
				   }
				}
				
				$Parent = GetParent($Parent['CategoryID'], '', $ParentCount-1);
			 }
			 
		  }
	
	   }
      
   }
   
   $Context->AddToDelegate('DiscussionGrid',
      'PreRender',
      'DiscussionGrid_AddParentLink');
      
   // Remove dash from Discussion details
   function Discussion_RemoveCategoryPrefix(&$Discussion) {      
      $Discussion->Category = SubNameTidy($Discussion->Category);
   }
   
   $Context->AddToDelegate('Discussion',
      'PostFormatPropertiesForDisplay',
      'Discussion_RemoveCategoryPrefix');
      
}

if (in_array($Context->SelfUrl, array('comments.php'))) {

   // Tidy up the CommentGrid
   function CommentGrid_RemoveCategoryPrefix(&$CommentGrid) {
      global $Context;
  
      $CategoryName = $CommentGrid->Discussion->Category;
      $CategoryID = $CommentGrid->Discussion->CategoryID;
      $CommentGrid->Discussion->Category = '';

      $ParentCount = CategoryLevel($CategoryName);

      if(!empty($ParentCount)) {
   
		  if($ParentCount > 0) {
			 $Parent = GetParent($CommentGrid->Discussion->CategoryID, '', $ParentCount);
			 
			 for($i = 0; $i < $ParentCount; $i++) {
			 
			    if(!empty($Parent['Name'])) {
			       $CommentGrid->Discussion->Category = '<a href="'.GetUrl($Context->Configuration, 'index.php', '', 'CategoryID', $Parent['CategoryID']).'">' . SubNameTidy($Parent['Name']) . '</a> > ' . $CommentGrid->Discussion->Category;
				   if(CategoryLevel($Parent['Name']) == 0) {
				      break;
				   }
				}
				
				$Parent = GetParent($Parent['CategoryID'], '', $ParentCount-1);
			 }
			 
		  }
	
	   }
	   
	   $CommentGrid->Discussion->Category .= '</a> <a href="'.GetUrl($Context->Configuration, 'index.php', '', 'CategoryID', $CategoryID).'">' . SubNameTidy($CategoryName);
	   
   }
   
   $Context->AddToDelegate('CommentGrid',
      'PreRender',
      'CommentGrid_RemoveCategoryPrefix');
}

?>