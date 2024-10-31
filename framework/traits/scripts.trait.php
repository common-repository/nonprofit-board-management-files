<?php
	namespace PluginFramework\V_1_1;
	trait Scripts {
		protected $scripts_dir    = 'scripts/';
		protected $scripts        = [];
		protected $admin_scripts  = [];


		/**
		 * Set Script Directory
		 *
		 * Set the root directory for the css files
		 *
		 * @param string $dir Directory (Examples: "styles", "inc/css")
		 */
		public function setScriptDir($dir) {
			$this->scripts_dir = $dir . '/';
		}


		/**
		 * Add Script
		 *
		 * @param string $name Script Name
		 * @param string $file File Name
		 * @param string[] $deps Dependancies
		 */
		public function addScript($name, $file, $deps = []){
			$this->scripts[] = [$name, $file, $deps];
		}

		/**
		 * Build Script Url
		 *
		 * @param string $file File relative to project root
		 *
		 * @return string Complete URL
		 */
		protected function scriptUrl($file) {
			return plugins_url( $this->scripts_dir . $file, $this->getFile() );
		}

		/**
		 * Add Admin Script
		 *
		 * @param string $name Script Name
		 * @param string $file File Name
		 * @param array $deps Dependancies
		 */
		public function addAdminScript($name, $file, $deps = []){
			$this->admin_scripts[] = [$name, $file, $deps];
		}

		public function scripts_hook_init() {
			foreach($this->scripts as list($name, $file, $deps)) {
				wp_register_script( $this->pre($name), $this->scriptUrl($file), $deps, $this->getVersion(), true );
			}
		}

		public function scripts_hook_admin_init(){
			foreach($this->admin_scripts as list($name, $file, $deps)) {
				wp_register_script( $this->pre('admin', $name), $this->scriptUrl($file), $deps, $this->getVersion(), true );
			}
		}


		public function scripts_hook_wp_enqueue_scripts() {
			foreach ( $this->scripts as list( $name, $file, $deps ) ) {
				wp_enqueue_script( $this->pre( $name ) );
			}
		}

		public function scripts_hook_admin_enqueue_scripts() {
			foreach ( $this->admin_scripts as list( $name, $file, $deps ) ) {
				wp_enqueue_script( $this->pre( 'admin', $name ) );
			}
		}
	}