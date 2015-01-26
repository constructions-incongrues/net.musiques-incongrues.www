<?php
/**
 * The Head control is used to display all of the elements in the html head of the page.
 * Applications utilizing this file: Filebrowser;
 *
 * Copyright 2003 Mark O'Sullivan
 * This file is part of Lussumo's Software Library.
 * Lussumo's Software Library is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Lussumo's Software Library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * The latest source code is available at www.vanilla1forums.com
 * Contact Mark O'Sullivan at mark [at] lussumo [dot] com
 *
 * @author Mark O'Sullivan
 * @copyright 2003 Mark O'Sullivan
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL 2
 * @package Framework
 */


/**
 * Writes out the page head.
 * @package Framework
 */
class Head extends Control {
	var $_Scripts;
	var $Scripts;       // Script collection
	var $StyleSheets;   // Stylesheet collection
	var $Strings;       // String collection
	var $BodyId;        // identifier assigned to the body tag
	var $Meta;          // An associative array of meta tags/content to be added to the head.
	var $Tags;          // Associate a script or style type to an etag.
	                    // By default Vanilla will use the last modification of the files on the server as etag unless an etag is already set.

	/**
	 * Add an external script for the page.
	 *
	 * In addition of setting the relative position of the scripts, $Position
	 * should indicate what kind of script is added:
	 *
	 * - Between 100 and 199, for general libaries like jQuery or Prototype.
	 * Extensions might replace these scripts with ones hosted on third party
	 * Content Delivery Networks.
	 *
	 * - Between 200 and 299 for global script loaded with all Vaniila pages
	 * like the ones added appg/init_vanilla.php. These scripts might be packed
	 * together.
	 *
	 * - Between 300 and 499 for script specific to a page. The ones with a
	 * position over 399 should not be packed together.
	 *
	 * - 500 or above is the default position. Extensions must not optimise the
	 * loading of these scripts.
	 *
	 * @param string $ScriptLocation Set the location of the script, relative to
	 *        the forum root.
	 * @param int $Position Set the relative position of the scripts in the list
	 *        of scripts for the page.
	 * @param string $ScriptRoot Set the bas url of the script. Use '~' for the
	 *        the forum root URL.
	 */
	function AddScript($ScriptLocation, $ScriptRoot = '~', $Position = Null) {
		if ($ScriptRoot == '~') {
			$ScriptRoot = $this->Context->Configuration['WEB_ROOT'];
		}

		$DefaultPosition = 500;
		if ($Position === Null) {
			$Position = $DefaultPosition + count($this->_Scripts);
		}
		
		if (!is_array($this->_Scripts)) {
			$this->_Scripts = array();
		}

		$ScriptPath = $ScriptLocation;
		if ($ScriptRoot != '') {
			$ScriptPath = ConcatenatePath($ScriptRoot, $ScriptLocation);
		}

		if (!array_key_exists($ScriptPath, $this->_Scripts)) {
			$this->_Scripts[$ScriptPath] = $Position;
		} else if ($this->_Scripts[$ScriptPath] >= $DefaultPosition) {
			$this->_Scripts[$ScriptPath] = $Position;
		}
	}

	function AddStyleSheet($StyleSheetLocation, $Media = '', $Position = '100', $StyleRoot = '~') {
		if ($StyleRoot == '~') $StyleRoot = $this->Context->Configuration['WEB_ROOT'];
		if (!is_array($this->StyleSheets)) $this->StyleSheets = array();
		$StylePath = $StyleSheetLocation;
		if ($StyleRoot != '') $StylePath = ConcatenatePath($StyleRoot, $StyleSheetLocation);
		$this->InsertItemAt(
			$this->StyleSheets,
			array('Sheet' => $StylePath, 'Media' => $Media),
			$Position
		);
	}

	function AddString($String) {
		if (!is_array($this->Strings)) $this->Strings = array();
		$this->Strings[] = $String;
	}

	function Clear() {
		$this->ClearStrings();
		$this->ClearStyleSheets();
		$this->ClearScripts();
		$this->Tags = array();
		$this->Meta = array();
	}

	function ClearStrings() {
		$this->Strings = array();
	}

	function ClearStyleSheets() {
		$this->StyleSheets = array();
	}

	function ClearScripts() {
		$this->_Scripts = $this->Scripts = array();
	}

	function Head(&$Context) {
		$this->Name = 'Head';
		$this->BodyId = '';
		$this->Control($Context);
		$this->Tags = array();
		$this->Meta = array();
	}

	function Render() {
		foreach ($this->Context->JSDictionary as $Key => $Value) {
			$Key = 'X-Vanilla-' . FormatStringForDisplay($Key);
			$Value = FormatStringForDisplay($Value);
			$this->Meta[$Key] = $Value;
		}

		// Can be used to replace css and script
		// e.g. Replace assets by ones from a CDN
		$this->CallDelegate('FilterAssets');

		// Sort the stylesheets
		if (is_array($this->StyleSheets)) {
			ksort($this->StyleSheets);
		}

		$this->CallDelegate('PackAssets');

		// Set $this->Scripts
		if (is_array($this->_Scripts)) {
			asort($this->_Scripts, SORT_NUMERIC);
			$this->Scripts = array_keys($this->_Scripts);
		}

		$this->CallDelegate('GetTags');
		$this->TagAssets();

		$this->CallDelegate('PreRender');
		include(ThemeFilePath($this->Context->Configuration, 'head.php'));
		include(ThemeFilePath($this->Context->Configuration, 'overall_header.php'));
		$this->CallDelegate('PostRender');
	}

	function TagAssets() {
		if (!$this->Context->Configuration['HEAD_TAG_ASSET']) {
			return;
		}

		foreach ($this->Scripts as &$Script) {
			$Script = $this->_TageAsset($Script);
		}
		reset($this->Scripts);

		foreach ($this->StyleSheets as &$Details) {
			$Details['Sheet'] = $this->_TageAsset($Details['Sheet']);
		}
		reset($this->StyleSheets);
	}

	function _TageAsset($Asset) {
		// Check that the asset start with the forum web root,
		// that it is a static file
		// and that it is not already tagged
		if (
			strpos($Asset, $this->Context->Configuration['WEB_ROOT']) !== 0 ||
			!preg_match('%^/(?:[-_.\d\w]+/)+[-_.\d\w]+\.(?:js|css)$%', $Asset)
		) {
			return $Asset;
		}

		return $Asset . '?t=' . $this->GetTag($Asset);
	}

	function GetTag($Asset) {
		if (array_key_exists($Asset, $this->Tags)) {
			return $this->Tags[$Asset];
		}

		$AssetPath = substr_replace(
			$Asset,
			$this->Context->Configuration['APPLICATION_PATH'],
			0,
			strlen($this->Context->Configuration['WEB_ROOT'])
		);

		if (file_exists($AssetPath)) {
			return filemtime($AssetPath);
		}

		return $this->Context->Configuration['HEAD_DEFAULT_ETAG'];
	}
}
?>
