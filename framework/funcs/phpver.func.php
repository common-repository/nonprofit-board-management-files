<?php
namespace PluginFramework\V_1_1;
if(!function_exists(__NAMESPACE__ . '\phpver')){
	function phpver($ver = false) {
		if($ver) {
			if(version_compare($ver, $GLOBALS['plugin_framework_v_1_1_phpver'], '>')) $GLOBALS['plugin_framework_v_1_1_phpver'] = $ver;
		}
		return $GLOBALS['plugin_framework_v_1_1_phpver'];
	}
}