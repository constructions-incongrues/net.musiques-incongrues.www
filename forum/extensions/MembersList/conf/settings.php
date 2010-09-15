<?php

// Adds the cell to the list for defined parameters 1 is to view 0 is hidden.
$eMembersConfig['c_avatar'] = '0'; // avatar
$eMembersConfig['c_name'] = '0'; // name & lastname
$eMembersConfig['c_email'] = '1'; // email
$eMembersConfig['c_visit'] = '1'; // visit count
$eMembersConfig['c_posts'] = '1'; // posts count
$eMembersConfig['c_registered'] = '1'; // registered date
$eMembersConfig['c_role'] = '1'; // role description

/* excluded role from the list, 
you can write role id's by separating with comas. 
ex:  $eMembersConfig['x_role'] = '2,4,5'; 
default is excluding banned members. 
*/
$eMembersConfig['x_role'] = '2'; 


// shows how many members showed in one page
$eMembersConfig['paginate'] = '15';
// shows how many page numbers show on the paging navigation. 
$eMembersConfig['maxPage'] = '10';





/* Adds necessary options to the member roles definition page */

$Context->SetDefinition('PERMISSION_VIEW_MEMBER', $Context->GetDefinition('permission_view'));
$Context->Configuration['PERMISSION_VIEW_MEMBER'] = '0';

$Context->SetDefinition('PERMISSION_VIEW_MEMBER_SECRET_DATA',$Context->GetDefinition('permission_view_secret') );
$Context->Configuration['PERMISSION_VIEW_MEMBER_SECRET_DATA'] = '0';

?>
