<?php
	namespace PluginFramework\V_1_1;
	trait Styles {

		protected $styles_dir     = 'styles/';
		protected $styles         = [];
		protected $admin_styles   = [];

		/**
		 * Set Style Directory
		 *
		 * Set the root directory for the css files
		 *
		 * @param string $dir Directory (Examples: "styles", "inc/css")
		 */
		public function setStyleDir($dir) {
			$this->styles_dir = $dir . '/';
		}
		/**
		 * Add Style
		 *
		 * @param string $name Script Name
		 * @param string $file File Name
		 * @param string[] $deps Dependancies
		 */
		public function addStyle($name, $file, $deps = []){
			$this->styles[] = [$name, $file, $deps];
		}
		/**
		 * Add Admin Style
		 *
		 * @param string $name Script Name
		 * @param string $file File Name
		 * @param string[] $deps Dependancies
		 */
		public function addAdminStyle($name, $file, $deps = []){
			$this->admin_styles[] = [$name, $file, $deps];
		}

		/**
		 * Build Style Url
		 *
		 * @param string $file File relative to project root
		 *
		 * @return string Complete URL
		 */
		protected function styleUrl($file) {
			return plugins_url( $this->styles_dir . $file, $this->getFile() );
		}

		public function styles_hook_init() {
			foreach ( $this->styles as list( $name, $file, $deps ) ) {
				wp_register_style( $this->pre( $name ), $this->styleUrl( $file ), $deps, $this->getVersion(), 'all' );
			}
		}
		public function styles_hook_admin_enqueue_scripts() {
			foreach ( $this->admin_styles as list( $name, $file, $deps ) ) {
				wp_enqueue_style( $this->pre( 'admin', $name ) );
			}
		}
		public function styles_hook_wp_enqueue_scripts() {
			foreach ( $this->styles as list( $name, $file, $deps ) ) {
				wp_enqueue_style( $this->pre( $name ) );
			}
		}
		public function styles_hook_admin_init() {
			foreach ( $this->admin_styles as list( $name, $file, $deps ) ) {
				wp_register_style( $this->pre( 'admin', $name ), $this->styleUrl( $file ), $deps, $this->getVersion(), 'all' );
			}
		}

	}