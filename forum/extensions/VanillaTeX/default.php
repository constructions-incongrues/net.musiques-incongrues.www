<?php
/*
Extension Name: VanillaTeX
Extension Url: N/A
Description: Allows using latex in Vanilla forum.
Version: 1.0
Author: Rafal Rawicki
Author Url: http://rafal.slashgeek.net
*/

/*
* Copyright 2003 - 2005 Mark OSullivan
* This file is part of Vanilla.
* Vanilla is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Vanilla is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code for Vanilla is available at www.lussumo.com
* Contact Mark OSullivan at mark [at] lussumo [dot] com
*/


function GetTeXImage($str){
	$texvcpath = "./extensions/VanillaTeX/texvc/texvc";
	$tmpdir = "./extensions/VanillaTeX/tmp";
	$imgdir = "./extensions/VanillaTeX/img";
	$command = "$texvcpath $tmpdir $imgdir \"" . htmlspecialchars_decode(strip_tags($str[1])) . "\" utf-8";
	$result = `$command`;
	if(!empty($result)){
		preg_match("/^.(.{32})/", $result, $result);

		if(strlen($result[1]) != 32){
			return "<span style=\"font-style: italic\">Formula rendering error. Correct the formula and try again.</span>";
		}

		return "<img class=\"tex\" src=\"extensions/VanillaTeX/img/$result[1].png\" alt=\"LaTeX image\" />";
	} else {
		return "<span style=\"font-style: italic\">Formula rendering error. Correct the formula and try again.</span>";
	}
}

class VanillaTeX extends StringFormatter
{

   function VanillaTeX()
   {
   #   $filename = "./extensions/VanillaTeX/config.txt";
   }

   function Parse($String, $Object, $FormatPurpose)
   {
      if ($FormatPurpose == FORMAT_STRING_FOR_DISPLAY)
         return preg_replace_callback("/\[math\](.*?)\[\/math\]/", "GetTeXImage", $String);
      else
         return $String;
   }

}

if(isset($Head)) $Head->AddStyleSheet('extensions/VanillaTeX/tex.css');

if (in_array($Context->SelfUrl, array("comments.php","search.php")))
{
   // Instantiate the formatter and add it to the context object's string manipulator
   $VanillaTeX = $Context->ObjectFactory->NewObject($Context, "VanillaTeX");
   $Context->StringManipulator->AddGlobalManipulator("VanillaTeX", $VanillaTeX);
}
?>
