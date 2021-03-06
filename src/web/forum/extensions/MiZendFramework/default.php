<?php
/*
 Extension Name: MiZendFramework
 Extension Url: https://github.com/contructions-incongrues
 Description: Configures Zend Framework for later user
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */

// Setup Zend framework autoloading
set_include_path(dirname(__FILE__).'/../../../../../vendor/zendframework/zendframework1/library/'.PATH_SEPARATOR.get_include_path());
require_once(dirname(__FILE__).'/../../../../../vendor/zendframework/zendframework1/library/Zend/Loader/Autoloader.php');
Zend_Loader_Autoloader::getInstance();
