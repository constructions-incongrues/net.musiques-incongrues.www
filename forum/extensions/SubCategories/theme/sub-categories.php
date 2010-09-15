<?php
// Note: This file is included from extensions/SubCategories/default.php.

/* 
   This is extrapolated to provide theming for SubCategories. Do not edit this 
   file directly, copy it into your theme folder to override it, and edit from there.
*/

$SessionPostBackKey = $Context->Session->GetVariable('SessionPostBackKey', 'string');

$SubCategoryList = '<div class="ContentInfo Top">
	<h1>
	   '.$Context->Dictionary['SubCategories'].'
	</h1>
</div>
<div id="ContentBody">
	<ol id="Categories" class="SubCategories">';
		$Category = $Context->ObjectFactory->NewObject($Context, 'Category');
		$FirstRow = 1;
		$Alternate = 0;
		$BeginSubForums = 0;
		$SubForums = 0;
		$CurrentName = '';
		
		while ($Row = $Context->Database->GetRow($SubData)) {
   			$Category->Clear();
  			$Category->GetPropertiesFromDataSet($Row);
			$Category->FormatPropertiesForDisplay();
			
			if (!isset($_GET['CategoryID'])) {
				$_GET['CategoryID'] = 0;
			}
			
			if ($Category->CategoryID == $_GET['CategoryID']) {
   				$BeginSubForums = 1;
   				$CurrentName = $Category->Name;
   			}
   			
   			if($BeginSubForums == 1) {

				if(IsSubCategory($Category->Name, $CurrentName)) {
				
					$Category->Name = SubNameTidy($Category->Name);
					
					$SubForums = 1;
					$SubCategoryList .= '	<li id="Category_'.$Category->CategoryID.'" class="Category'.($Category->Blocked?' BlockedCategory':' UnblockedCategory').($FirstRow?' FirstCategory':'').' Category_'.$Category->CategoryID.($Alternate ? ' Alternate' : '').'">
         <ul>
         <li class="CategoryName">
            <span>'.$Context->GetDefinition('Category').'</span> <a href="'.GetUrl($Context->Configuration, 'index.php', '', 'CategoryID', $Category->CategoryID).'">'.$Category->Name.'</a>
         </li>
         <li class="CategoryDescription">
            <span>'.$Context->GetDefinition('CategoryDescription').'</span> '.$Category->Description.'
         </li>
         <li class="CategoryDiscussionCount">
            <span>'.$Context->GetDefinition('Discussions').'</span> '.SubCount($Category->CategoryID, 'discussions').'
         </li>';
         $SubCategoryList .= '         <li class="CategoryDiscussionCount SubCategory">
            <span>'.$Context->GetDefinition('SubCategories').'</span> '.SubCount($Category->CategoryID, 'categories').'
         </li>';
         
					if ($Context->Session->UserID > 0) {
						$SubCategoryList .= '
               <li class="CategoryOptions">
                  <span>'.$Context->GetDefinition('Options').'</span> ';
                  
                  		if(!isset($SessionPostBackKey)) {
                  			$SessionPostBackKey = '';
                  		}
                  
                  		if ($Category->Blocked) {
							$SubCategoryList .= '<a id="BlockCategory'.$Category->CategoryID.'" onclick="ToggleCategoryBlock('."'".$Context->Configuration['WEB_ROOT']."ajax/blockcategory.php', ".$Category->CategoryID.", 0, 'BlockCategory".$Category->CategoryID."', '".$SessionPostBackKey."');\">".$Context->GetDefinition('UnblockCategory').'</a>';
						} else {
								$SubCategoryList .= '<a id="BlockCategory'.$Category->CategoryID.'" onclick="ToggleCategoryBlock('."'".$Context->Configuration['WEB_ROOT']."ajax/blockcategory.php', ".$Category->CategoryID.", 1, 'BlockCategory".$Category->CategoryID."', '".$SessionPostBackKey."');\">".$Context->GetDefinition('BlockCategory').'</a>';
						}
                  
						$SubCategoryList .= '</li>
            ';
					}
         
					$SubCategoryList .= '</ul>
   </li>';
         			$FirstRow = 0;
	     			$Alternate = FlipBool($Alternate);
      			}
	
			}
   
		}

		if($SubForums == 1) {
			echo $SubCategoryList.'</ol>
</div>
<p class="BelowSubForums">&nbsp;</p>';
		}
 	
	
	
?>