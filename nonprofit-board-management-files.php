<?php

/*
Plugin Name: Nonprofit Board Management Files
Plugin URI: http://flynndev.us/project/nonprofit-board-management-files
Description: Adds Agenda and Minutes to meetings, adds shortcode to display meetings with file downloads.
Version: 0.0.64
Author: flynndev, mflynn
Author URI: http://flynndev.us
License: GPL2
*/

require_once('framework/load.php');
PluginFramework\V_1_1\register("Nonprofit Board Management Files", __FILE__);

if(PluginFramework\V_1_1\check_version()) {
	require_once( 'plugin.class.php' );

	$NonprofitBoardManagementFiles = new NonprofitBoardManagement\Files\Plugin( "npbm_files", '0.0.64', __FILE__ );
}