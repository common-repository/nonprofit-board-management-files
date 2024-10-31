<?php

	namespace PluginFramework\V_1_1;

	trait Helpers {
		/**
		 * Sterilize String
		 *
		 * Converts string to lower case
		 * Converts all spaces to underscores
		 *
		 * @param string $str
		 *
		 * @return string Sterilized String
		 */
		public function sterilize($str) {
			return strtolower( str_replace(' ', '_', $str ) );
		}

		/**
		 * Concatenate
		 *
		 * Sterilizes all Strings
		 * Glues strings together with _
		 *
		 * @param string|array ...$param Parameter
		 *
		 * @return string
		 */
		public function concat($param){
			$args   = func_get_args();
			$pieces = [];

			foreach($args as $arg) if(!empty($arg)) $pieces[] = $this->sterilize($arg);

			return implode("_", $pieces);
		}

		/**
		 * Generate Prefixed String
		 *
		 * Prefixes with plugin prefix
		 * Sterilizes all Strings
		 * Glues strings together with _
		 *
		 * @param string ...$param Parameters
		 * @return string
		 */
		public function pre($param) {
			$args   = func_get_args();
			$pieces = [$this->prefix];

			foreach($args as $arg) if(!empty($arg)) $pieces[] = $this->sterilize($arg);

			return  implode('_', $pieces);
		}

		/**
		 * Convert pre to title
		 *
		 * Converts a string that has been passed through pre back to a page title
		 *
		 * @param string $s
		 * @return string
		 */
		public function pre_to_title($s) {
			$a = explode('_', $s);
			array_shift($a);
			return ucwords(implode(" ", $a));
		}

		public function check_nonce($name){
			$n = $this->pre($name);
			if ( !isset( $_REQUEST['_'.$n] ) || !wp_verify_nonce( $_REQUEST['_'.$n], $n ) ){
				return false;
			}
			return true;
		}

		public function nonce($name){
			$n = $this->pre($name);
			$nonce = wp_create_nonce($n);
			return "<input type='hidden' id='_{$n}' name='_{$n}' value='{$nonce}' />";
		}

	}