<?php
/*
Extension Name: Minify
Extension Url: subjunk@gmail.com
Description: Vanilla bridge for Minify which packs JS and CSS files together. Improve client-side load by limiting request for external files.
Version: 1.0
Author: Klaus Burton
Author Url: http://www.redskiesdesign.com/

 * Minify:
 * Copyright (c) 2008 Ryan Grove <ryan@wonko.com>
 * Copyright (c) 2008 Steve Clay <steve@mrclay.org>
 *
 * Vanilla Bridge:
 * Copyright (c) 2009 Klaus Burton <http://www.redskiesdesign.com/>
 * Copyright (c) 2009 Damien Lebrun <dinoboff@gmail.com>
 */

// Make sure the fille is included in vanilla
if (!defined('IN_VANILLA')) exit();

// Disable extension if php < 5.2.1 (Minify requirement
if (version_compare(PHP_VERSION, '5.2.1') < 0) return;

if (!array_key_exists('MINIFY_PACKING_LEVEL', $Configuration)) {
	AddConfigurationSetting($Context, 'MINIFY_PACKING_LEVEL', 3);
}

$Context->AddToDelegate('Head',
      'PackAssets',
      'Minify_Head_PackAssets_Delegation');


/**
 * Handler for PackAssets Delegation
 *
 * @param Head $Head
 */
function Minify_Head_PackAssets_Delegation(&$Head) {
	Minify_Script($Head);
	Minify_StyleSheets($Head);
}


/**
 * Replace multiple JS file from $Head->_Scripts by one or more request
 * to Minify which will pack them.
 *
 * @param Head $Head
 */
function Minify_Script(&$Head) {
	$WebRoot = $Head->Context->Configuration['WEB_ROOT'];
	$PackingLevel = $Head->Context->Configuration['MINIFY_PACKING_LEVEL'];

	$Filters = Minify_GetScriptFilter($PackingLevel);
	foreach ($Filters as $FilterPair) {
		list($ToPackFilter, $ToKeepFilter) = $FilterPair;

		$ScriptsToPack = array_filter($Head->_Scripts, $ToPackFilter);
		asort($ScriptsToPack, SORT_NUMERIC);
		$ScriptsToPack = array_keys($ScriptsToPack);

		$MinScript =  Minify_MinURL($ScriptsToPack, $WebRoot);

		if ($MinScript) {
			$Head->_Scripts = array_filter($Head->_Scripts, $ToKeepFilter);
			$Head->_Scripts[$MinScript] = 400;
		}
	}

}


/**
 * Replace multiple css file targetting the same media by a request to Minify
 * which will pack them.
 *
 * @param Head $Head
 */
function Minify_StyleSheets(&$Head) {
	$WebRoot = $Head->Context->Configuration['WEB_ROOT'];
	
	$Collections = array();
	foreach ($Head->StyleSheets as $StyleSheet) {
		if (!array_key_exists($StyleSheet['Media'], $Collections)) {
			$Collections[$StyleSheet['Media']] = array();
		}
		$Collections[$StyleSheet['Media']][] = $StyleSheet['Sheet'];
	}


	$Head->StyleSheets = array();
	foreach ($Collections as $Media => $StyleSheets) {
		$MinStyleSheet = Minify_MinURL($StyleSheets, $WebRoot);
		if ($MinStyleSheet) {
			$Head->StyleSheets[] = array(
				'Media'=>$Media, 'Sheet'=>$MinStyleSheet);
		} else {
			foreach ($StyleSheets as $StyleSheet) {
				$Head->StyleSheets[] = array(
					'Media'=>$Media, 'Sheet'=>$StyleSheet);
			}
		}
	}
}


/**
 * Convert a list a path to a Minify url.
 *
 * Will fails if there is less than 2 files in the list or if the files
 * are not in $WebRoot.
 *
 * @param array $FileList
 * @param string $WebRoot
 * @return bool|string
 */
function Minify_MinURL($FileList, $WebRoot) {
	if (!is_array($FileList) || count($FileList) < 2) {
		return false;
	}

	$MinifyPrepend = '';
	if ($WebRoot !== "/") {
		$MinifyPrepend = 'b=' . trim($WebRoot, '/') . '&';
	}

	foreach ($FileList as &$Path) {
		$Path = Minify_StripQueryString($Path);
		$Path = Minify_MakeRelative($Path, $WebRoot);
		if ($Path === false) {
			return false;
		}
	}

	return $WebRoot . 'extensions/Minify/'
		. $MinifyPrepend
		. 'f=' . implode(',', $FileList);
}

/**
 * Make the Path relative
 *
 * @param string $Path
 * @param string $Base
 * @return bool|string Return false if one of the path is not in $Base
 */
function Minify_MakeRelative($Path, $Base) {
	if (!strpos($Path, $Base) === 0) {
		return false;
	}

	return substr($Path, strlen($Base));
}

/**
 * Remove query string from a path
 *
 * @param string $Path
 * @return string
 */
function Minify_StripQueryString($Path) {
	$Pos = strpos($Path, '?');

	if ($Pos === false) {
		return $Path;
	}

	return substr($Path, 0, $Pos);
}

/**
 * Return an array of pair of filter to regroup scripts that can be packed together
 *
 * The first filter in a pair is used to select the script to package. The other
 * select the other ones.
 *
 * @param int $Level of packaging. 0: no packing, 1:package global and specific
 *            scripts but separatly. 2: package global and specific scripts together,
 *            3: package all packageable script together, including libraries;
 *            4: package all files.
 *
 * @return array
 */
function Minify_GetScriptFilter($Level) {
	switch ($Level) {
		case 1: // Pack Global and page scripts in two different packages
			return array(
				array('Minify_IsScriptGlobal', 'Minify_IsNotScriptGlobal'),
				array('Minify_IsScriptForPage', 'Minify_IsNotScriptForPage')
			);

		case 2: // Pack Global and page scripts together
			return array(
				array('Minify_IsPackableAndNotLibrary', 'Minify_IsNotPackableOrIsNotLibrary')
			);

		case 3: // Pack all packable scripts
			return array(
				array('Minify_IsPackable', 'Minify_IsNotPackable')
			);

		case 4: // Pack all scripts
			return array(
				array('Minify_AllScripts','Minify_NoScript')
			);

		default: // Pack no script.
			return array();;
	}
}

/** Script filters **/


function Minify_IsScriptGlobal($Position) {
	return $Position >= 200 && $Position < 300;
}

function Minify_IsNotScriptGlobal($Position) {
	return !Minify_IsScriptForPage($Position);
}


function Minify_IsScriptForPage($Position) {
	return $Position >= 300 && $Position < 400;
}

function Minify_IsNotScriptForPage($Position) {
	return !Minify_IsScriptForPage($Position);
}


function Minify_IsPackable($Position) {
	return $Position >= 100 && $Position < 400;
}

function Minify_IsNotPackable($Position) {
	return !Minify_IsPackable($Position);
}


function Minify_IsPackableAndNotLibrary($Position) {
	return $Position >= 200 && $Position < 400;
}

function Minify_IsNotPackableOrIsNotLibrary($Position) {
	return !Minify_IsPackableAndNotLibrary($Position);
}


function Minify_AllScripts($Position) {
	return true;
}

function Minify_NoScript($Position) {
	return false;
}