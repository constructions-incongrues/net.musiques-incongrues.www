<?php
/**
 * Avatar First Letter
 *
 * @author Clément Birklé
 * @copyright 2015 SBA Concept.
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU GPL v2
 * @package avatarfirstletter
 */

$PluginInfo['avatarfirstletter'] = array(
   'Name' => 'Avatar First Letter',
   'Description' => 'Avatar First Letter.',
   'Version' => '1.2',
   'RequiredApplications' => array('Vanilla' => '2.1'),
   'RequiredTheme' => false,
   'RequiredPlugins' => false,
   'HasLocale' => false,
   'MobileFriendly' => true,
   'Author' => 'Clément Birklé',
   'AuthorEmail' => 'clement@sba-concept.ch',
   'AuthorUrl' => 'http://www.sba-concept.ch',
   'License' => 'GPLv3'
);

class AvatarFirstLetter extends Gdn_Plugin
{
	public function __construct()
	{
	}

	public function Base_Render_Before(&$Sender)
	{
		$Sender->AddCssFile('avatarfirstletter.css', 'plugins/avatarfirstletter');
	}
	
	public function Setup()
	{
	}
	
	public static function hsvToRgb($h, $s, $v)
	{
		$r = '';
		$g = '';
		$b = '';
		
		$i = floor($h * 6);
		$f = $h * 6 - $i;
		$p = $v * (1 - $s);
		$q = $v * (1 - $f * $s);
		$t = $v * (1 - (1 - $f) * $s);
		
		switch ($i % 6) {
			case 0: $r = $v; $g = $t; $b = $p; break;
			case 1: $r = $q; $g = $v; $b = $p; break;
			case 2: $r = $p; $g = $v; $b = $t; break;
			case 3: $r = $p; $g = $q; $b = $v; break;
			case 4: $r = $t; $g = $p; $b = $v; break;
			case 5: $r = $v; $g = $p; $b = $q; break;
		}
		
		return array(
			'r' => floor($r * 255),
			'g' => floor($g * 255),
			'b' => floor($b * 255),	  
		);
	}
	
	/**
	 * Convert the given string to a unique color.
	 *
	 * @param {String} string
	 * @return {String}
	 */
	public static function stringToColor($string)
	{
		$num = 0;
		
		// Convert the username into a number based on the ASCII value of each
		// character.
		for ($i = 0; $i < strlen($string); $i++) {
			$num += self::charCodeAt($string, $i);
		}
		
		// Construct a color using the remainder of that number divided by 360, and
		// some predefined saturation and value values.
		$hue = $num % 360;
		$rgb = self::hsvToRgb($hue / 360, 0.3, 0.9);
			
		return self::rgb2hex($rgb);
	}
	
	public static function rgb2hex($rgb)
	{
		return '#' . sprintf('%02x', $rgb['r']) . sprintf('%02x', $rgb['g']) . sprintf('%02x', $rgb['b']);
	}		
	
	public static function charCodeAt($str, $num)
	{
		return self::utf8_ord(self::utf8_charAt($str, $num));
	}
	
	public static function utf8_ord($ch)
	{
		$len = strlen($ch);
		if($len <= 0)return false;
		$h = ord($ch{0});
		if ($h <= 0x7F) return $h;
		if ($h < 0xC2) return false;
		if ($h <= 0xDF && $len>1) return ($h & 0x1F) <<  6 | (ord($ch{1}) & 0x3F);
		if ($h <= 0xEF && $len>2) return ($h & 0x0F) << 12 | (ord($ch{1}) & 0x3F) << 6 | (ord($ch{2}) & 0x3F);          
		if ($h <= 0xF4 && $len>3) return ($h & 0x0F) << 18 | (ord($ch{1}) & 0x3F) << 12 | (ord($ch{2}) & 0x3F) << 6 | (ord($ch{3}) & 0x3F);
		return false;
	}
	
	public static function utf8_charAt($str, $num)
	{
		return mb_substr($str, $num, 1, 'UTF-8');
	}
}



if (!function_exists('UserPhotoDefaultUrl')) {
   /**
    * Calculate the user's default photo url.
    *
    * @param array|object $user The user to examine.
    * @param array $options An array of options.
    * - Size: The size of the photo.
    * @return string Returns the vanillicon url for the user.
    */
    function userPhotoDefaultUrl($user, $options = array()) {
        $name = val('Name', $user, 'Unknown');
        $photoUrl = 'AvatarFirstLetter_'.$name;
        return $photoUrl;
    }
}



if (!function_exists('img')) {
    /**
     * Returns an img tag.
     */
    function img($Image, $Attributes = '', $WithDomain = false) {
        if ($Attributes != '') {
            $Attributes = Attribute($Attributes);
        }
        
        if (preg_match('/^(.*)AvatarFirstLetter_(.+)$/', $Image, $matches)) {
	        $name = $matches[2];
	        $firstLetter = mb_substr($name, 0, 1);
	        $rgb = AvatarFirstLetter::stringToColor($name);
			$Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVQI12NgYAAAAAMAASDVlMcAAAAASUVORK5CYII=';
	        
	        $output = '<span class="AvatarFirstLetter" style="background-color: '.$rgb.';">';
	        	$output .= '<img src="'.$Image.'"'.$Attributes.' />';
				$output .= '<span>'.$firstLetter.'</span>';
			$output .= '</span>';
			
	        return $output;
        }

        if (!IsUrl($Image)) {
            $Image = SmartAsset($Image, $WithDomain);
        }

        return '<img src="'.$Image.'"'.$Attributes.' />';
    }
}