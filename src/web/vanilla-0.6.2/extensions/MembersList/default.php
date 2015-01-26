<?php
/*
Extension Name: MembersList
Extension Url: http://www.vanillaforums.org/addon/355/memberslist/
Description: Adds a 'Members' tab to the top bar and creates a members page which lists all members and their details with sorting and paging ability. Don't forget to assign every roles for changing users permission such as view the list and view the secret data. 
Version: 1.4
Author: SubJunk
Author Url: http://www.redskiesdesign.com

- the idea and core from Joe Clare's Members Page extension.
- integration of paging and sorting : http://www.frequency-decoder.com/2006/09/16/unobtrusive-table-sort-script-revisited
- dont forget to change user permissions from member roles page. 
- some new columns, numbers, real name, total visit, total message etc.

*/


/* sort and pagination js codes */
$Head->AddScript($Context->Configuration['EXTENSIONS_PATH'].'MembersList/library/tablesort.js');
$Head->AddScript($Context->Configuration['EXTENSIONS_PATH'].'MembersList/library/paginate.js');


/*Language definitions & settings*/
include($Context->Configuration['EXTENSIONS_PATH'].'MembersList/conf/language.php');
include($Context->Configuration['EXTENSIONS_PATH'].'MembersList/conf/settings.php');

/*****************************************************************************/
/************************** MODIFICATION CODE BELOW **************************/
/************** DO NOT EDIT UNLESS YOU KNOW WHAT YOU ARE DOING! **************/
/*****************************************************************************/

// Add a link to the members page at the top bar 
if(isset($Menu) && $Context->Session->UserID > 0 && $Context->Session->User->Permission('PERMISSION_VIEW_MEMBER')) {
    $Menu->AddTab($Context->GetDefinition('Members'), $Context->GetDefinition('Members'), GetUrl($Configuration, 'extension.php', '', '', '', '', 'PostBackAction=Members'), $Attributes = '', $Position = '50', $ForcePosition = '50');
}


if(in_array($Context->SelfUrl, array('extension.php'))   && $Context->Session->UserID > 0 || in_array($Context->SelfUrl, array('extension.php')) &&  $Context->Session->User->Permission('PERMISSION_VIEW_MEMBER')) {
	
// Determine the members page content by querying the database.
class MemberList {
	 
    function CreateMemberList() {
    	
        global $Context;
        global $eMembersConfig;
        $rakam = '0';
        $sql = $Context->ObjectFactory->NewContextObject($Context, 'SqlBuilder');
        $sql->SetMainTable('User','u');
        $sql->AddSelect(array('UserID', 'RoleID', 'FirstName', 'LastName', 'Name', 'Email', 'UtilizeEmail', 'CountDiscussions', 'CountComments', 'DateFirstVisit', 'ShowName', 'Icon', 'CountVisit'), 'u');
		
		// checks if the roleid excluded... you can set it from settings.php
		
	 for($xr=0;$xr<strlen($eMembersConfig['x_role']);$xr++){
	 	$sql->AddWhere('u','RoleID','',$eMembersConfig['x_role'][$xr],'!=');
	 	
	 	}
		


        $result = $Context->Database->Select($sql, 'MembersList', 'MembersListTable', 'An error occurred while listing the member information.');
        
        
        
        
        $toreturn = '<table border="0" id="memberscontainer">';
        
        // adding pagination and sorting function depending on js file. 
        $toreturn .='<tr><td><table id="membertable" cellpadding="0" cellspacing="0" border="0" class="sortable-onload-3r rowstyle-alt no-arrow paginate-'.$eMembersConfig['paginate'].' max-pages-'.$eMembersConfig['maxPage'].'">';
        
        $toreturn .='<thead><tr class="top">';
        
        //numbering title
        $toreturn .='<th class="sortable-numeric">#</th>';        
        
        //avatar title
         if($eMembersConfig['c_avatar'] == 1) {
        $toreturn .='<th>'.$Context->GetDefinition('AvatarDefinition').'</th>';      
         }
		
			
        //username title
        $toreturn .='<th class="sortable">'.$Context->GetDefinition('Username').'</th>';
      
       //name lastname 
       if($eMembersConfig['c_name'] == 1){
        $toreturn .= '<th class="sortable">'.$Context->GetDefinition('NameTag').'</th>';
       }
        //total message title
         if($eMembersConfig['c_email'] == 1){
        $toreturn .= '<th class="sortable">'.$Context->GetDefinition('Email').'</th>';
         }
          if($eMembersConfig['c_posts'] == 1){
        $toreturn .= '<th class="sortable-numeric">'.$Context->GetDefinition('Posts').'</th>';
          }
       //total visit title
        if($eMembersConfig['c_visit'] == 1){
        $toreturn .= '<th class="sortable-numeric">'.$Context->Dictionary['Visit'].'</th>';
        }
        // date registered title
        if($eMembersConfig['c_registered'] == 1) {
            $toreturn .= '<th>'.$Context->Dictionary['Registered'].'</th>';
        }
        
        // role description title
         if($eMembersConfig['c_role'] == 1){
        $toreturn .= '<th class="sortable">'.$Context->Dictionary['Role'].'</th></tr></thead><tbody>';
         }
         
        // first value for zebra striping, odd/even row color
        $bgcolor='odd';
               
        while($rows = $Context->Database->GetRow($result)) {
        	
        	// counting
        	$rakam = $rakam+1;
        	// changes the background color for odd/even rows
        	$bgcolor=='odd' ? $bgcolor='' : $bgcolor='odd';
     
        	// total messages
        	$fullposts = $rows['CountDiscussions'] + $rows['CountComments'];
            
//------------------------------------------------------------------------------------------o
// if the user allow members to see his/her email address or
// if the user role is allowed to see secret data...
//------------------------------------------------------------------------------------------o

            if($rows['UtilizeEmail'] == 1 || $Context->Session->User->Permission('PERMISSION_VIEW_MEMBER_SECRET_DATA')) {
                $checkedemail = '<a href="mailto:'.$rows['Email'].'">'.$rows['Email'].'</a>';
            } else {
                $checkedemail = $Context->Dictionary['Gizli'];
            }
            // roles
            $getrole = $Context->ObjectFactory->NewContextObject($Context, 'SqlBuilder');
            $getrole->SetMainTable('Role','r');
            $getrole->AddSelect(array('Name'), 'r');
            $getrole->AddWhere('r', 'RoleID', '', $rows['RoleID'], '=');
            $getrole = $Context->Database->Select($getrole, 'MembersListRoles', 'MembersListTableRoles', 'An error occurred while grabbing the member roles.');

            while($grRows = $Context->Database->GetRow($getrole)) {
                $role = $grRows['Name'];
            }
   
            $toreturn .= '<tr class="'.$bgcolor.'"><td>'.$rakam.'</td>';
            
 // Avatar           
            
            if($eMembersConfig['c_avatar'] == 1) {
                $toreturn .= '<td class="icon"><img src="'.$rows['Icon'].'" class="icon" /></td>';
            }
            
            $toreturn .= '<td><a href="'.GetUrl($Context->Configuration, 'account.php', '', 'u', $rows['UserID']).'">'.$rows['Name'].'</a></td>';
            
            
//------------------------------------------------------------------------------------------o
// if the user allow members to see his/her name and lastname or
// if the user role is allowed to see secret data...
//------------------------------------------------------------------------------------------o
  if($eMembersConfig['c_name'] == 1){     
     if($rows['ShowName'] == 1 || $Context->Session->User->Permission('PERMISSION_VIEW_MEMBER_SECRET_DATA')){
     	
     	if($rows['FirstName'] != '' || $rows['LastName'] != '') {
                $toreturn .= '<td>'.$rows['FirstName'].' '.$rows['LastName'].'</td>';
            }
            elseif($rows['FirstName'] == '' && $rows['LastName'] == '') {
                $toreturn .= '<td>'.$Context->Dictionary['notAvail'].'</td>';
            }

        }else{
        
 				$toreturn .= '<td>'.$Context->Dictionary['Gizli'].'</td>';
 	}    	
}
// emails
 if($eMembersConfig['c_email'] == 1){
           $toreturn .= '<td>'.$checkedemail.'</td>';
 }
//total message count
 if($eMembersConfig['c_posts'] == 1){
           $toreturn .= '<td  class="smallcell">'.$fullposts.'</td>';
 }
// total visit
 if($eMembersConfig['c_visit'] == 1){
            $toreturn .= '<td class="smallcell">'.$rows['CountVisit'].'</td>';
 }
 // registered
if($eMembersConfig['c_registered'] == 1) {

                $toreturn .= '<td class="medcell">'.$rows['DateFirstVisit'].'</td>';
            }
 
 
// role
 if($eMembersConfig['c_role'] == 1){
            $toreturn .= '<td class="medcell">'.$role.'</td>';
 }
 
 $toreturn .= '</tr>';
        
}
        $toreturn .= '</tr></table>';
   
        return  $toreturn;
        
        }
    function Render() {
        echo $this->CreateMemberList();
    }
}

// Add the page and stylesheets and echo it all out
if(in_array(ForceIncomingString("PostBackAction", ""), array('Members'))) {
$Head->AddStyleSheet('extensions/MembersList/style.css');
$Context->PageTitle = $Context->GetDefinition('Members');
$Menu->CurrentTab = 'Members';
$Body->CssClass = 'Discussions';
$MemberList = $Context->ObjectFactory->NewContextObject($Context, 'MemberList');
$Page->AddRenderControl($MemberList, $Configuration["CONTROL_POSITION_BODY_ITEM"]);
}

}


?>