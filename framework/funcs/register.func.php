<?php
namespace PluginFramework\V_1_1;

if(! function_exists(__NAMESPACE__ . '\register')) {
	function register($name, $file){
		if(empty($GLOBALS['plugin_framework_v_1_1_plugins'])) $GLOBALS['plugin_framework_v_1_1_plugins'] = array();
		$GLOBALS['plugin_framework_v_1_1_plugins'][$name] = $file;
	}
}
