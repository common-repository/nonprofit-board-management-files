<?php
namespace PluginFramework\V_1_1;

if(empty($GLOBALS['plugin_framework_v_1_1_php_settings'])) $GLOBALS['plugin_framework_v_1_1_php_settings'] = array('allow_url_fopen');

if(!function_exists(__NAMESPACE__ . '\php_settings')){
	function php_settings(){

		// Keeping in case I need to use this for a different setting

		// Attempt to tell server to allow url fopen
		// ini_set('allow_url_fopen', 1);

		// if(ini_get('allow_url_fopen') != 1) return false; // Failed to change setting
		return true;

	}
}