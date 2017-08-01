<?php if (!defined('APPLICATION')) exit();
function smarty_function_mysigninout_link($Params, &$Smarty) {
   $Wrap = GetValue('wrap', $Params, 'li');  
   return Gdn_Theme::Link('signinout',
      GetValue('text', $Params, ''),
      GetValue('format', $Params, Wrap('<a href="%url" class="%class mysigninout">%text</a>', $Wrap)));
}