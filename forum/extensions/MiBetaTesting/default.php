<?php
/*
 Extension Name: MiBetaTesting
 Extension Url: https://github.com/contructions-incongrues
 Description: Makes it possible to show / hide features depending on user permissions
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */

if( !array_key_exists('PERMISSION_ENABLE_FEATURE_SIDEBAR_FACETS', $Configuration)) {
	AddConfigurationSetting($Context, 'PERMISSION_ENABLE_FEATURE_SIDEBAR_FACETS', '0');
}
