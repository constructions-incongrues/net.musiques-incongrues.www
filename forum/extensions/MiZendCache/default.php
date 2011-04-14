<?php
/*
 Extension Name: MiZendCache
 Extension Url: https://github.com/contructions-incongrues
 Description: Configures Zend Cache
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */

// Setup Zend framework autoloading
set_include_path(dirname(__FILE__).'/../../../lib/vendor/ZendFramework-1.11.5-minimal/library/'.PATH_SEPARATOR.get_include_path());
require_once(dirname(__FILE__).'/../../../lib/vendor/ZendFramework-1.11.5-minimal/library/Zend/Loader/Autoloader.php');
Zend_Loader_Autoloader::getInstance();

// Setup cache manager
$cacheManager = new Zend_Cache_Manager();

// Miner calls cache
$cacheManager->setCacheTemplate('functions', 
	array(
		'frontend' => array('name' => 'Function', 'options' => array('cache_id_prefix' => 'MiZendCache_Miner')), 
		'backend'  => array('name' => 'File', 'options' => array('cache_dir' => $Context->Configuration['VANILLA_MINER_CACHEDIR'], 'file_name_prefix' => 'mi_miner_cache'))
	)
);

// Store cache manager in Vanilla context
$Context->ZendCacheManager = $cacheManager;