<?php
/**
 * Package:  WordPress Plugin Framework
 * Version:  1.1.122
 * Date:     30-07-2017
 * Copyright 2017 Mike Flynn - mflynn@flynndev.us
 */ 
 


namespace PluginFramework\V_1_1;

if(empty($GLOBALS['plugin_framework_v_1_1_phpver'])) $GLOBALS['plugin_framework_v_1_1_phpver'] = '5.4.0';

require_once("funcs/check_version.func.php");
require_once("funcs/admin_notice.func.php");
require_once("funcs/phpver.func.php");
require_once("funcs/register.func.php");

if( ! check_version() ) {
	add_action( 'admin_notices', __NAMESPACE__ . '\admin_notice_ver' );
}
else {

	require_once("funcs/php_settings.func.php");

	if ( ! php_settings() ){
		add_action( 'admin_notices', __NAMESPACE__ . '\admin_notice_settings' );
	}
	else {

		if ( ! class_exists( __NAMESPACE__ . "\Core" ) ) {
			require_once( 'mustache.php' );

			$folder = dirname( __FILE__ ) . '/' . 'traits';
			foreach ( scandir( $folder ) as $filename ) {
				$path = $folder . '/' . $filename;
				if ( is_file( $path ) ) {
					require_once( $path );
				}
			}

			require_once( 'core.class.php' );
		}
	}
}