<?php
	namespace PluginFramework\V_1_1;
	trait Security {
		protected $security_level = 'manage_options';

		/**
		 * Can user access page?
		 *
		 * Checks if user is at or above the security_level
		 *
		 * Dies on failure
		 */
		public function can() {
			if ( !current_user_can( $this->security_level ) )  $this->error('forbidden');
		}

		/**
		 * Set Security Level
		 *
		 * @param string $level
		 */
		public function setSecurityLevel($level = 'manage_options'){
			$this->security_level = $level;
		}

		/**
		 * Get Security Level
		 *
		 * @return string
		 */
		public function getSecurityLevel() {
			return $this->security_level;
		}

	}