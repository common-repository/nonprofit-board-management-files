<?php
	namespace PluginFramework\V_1_1;
	trait PluginInfo {

		/**
		 * Plugin Name
		 * @var string
		 */
		protected $name = "Plugin Framework";

		/**
		 * Plugin Version
		 * @var string
		 */
		protected $version = '1.0.0';

		/**
		 * Plugin Root
		 *
		 * Used for determining file locations
		 *
		 * @var string
		 */
		protected $root = '';

		/**
		 * Plugin File
		 *
		 * Used for plugins_url
		 *
		 * @var string
		 */
		protected $file = '';

		/**
		 * Plugin Prefix
		 *
		 * This string will be applied in a variety of locations to namespace stuff for this plugin
		 *
		 * @var string
		 */
		public $prefix = 'plugin_framework';

		/**
		 * Set Plugin Name
		 *
		 * Set the plugin name
		 * Optionally set the plugin prefix, shortcode prefix, etc...
		 *
		 * @param string $name Plugin Name
		 * @param bool $cascade Update other fields?
		 */
		public function setName( $name, $cascade = true ) {
			$this->name = $name;
			if ( $cascade ) {
				$this->setPrefix( $name, true );
			}
		}

		public function getName() {
			return $this->name;
		}

		/**
		 * Set Plugin Prefix
		 *
		 * @param string $prefix Prefix
		 * @param bool $cascade Update other fields?
		 */
		public function setPrefix( $prefix, $cascade = true ) {
			$this->prefix = $this->sterilize( $prefix );
			if ( $cascade ) {
				$this->setShortcodePrefix( $prefix );
			}
		}

		/**
		 * Get Plugin Prefix
		 * @return string Prefix
		 */
		public function getPrefix() {
			return $this->prefix . "_";
		}

		/**
		 * Set Plugin Root
		 *
		 * send in the value of __FILE__ from the plugin root
		 *
		 * @param string $root Root File
		 *
		 * @return boolean Success
		 */
		public function setRoot( $root ) {
			if ( is_file( $root ) ) {
				$this->file = $root;
				$this->root = dirname( $root );
			} else if ( is_dir( $root ) ) {
				$this->root = $root;
			} else {
				$this->root = dirname( __FILE__ );
				return false;
			}
			return true;
		}

		/**
		 * Get Plugin Root
		 *
		 * @return string Root
		 */
		public function getRoot() {
			return $this->root;
		}

		/**
		 * Set File
		 *
		 * @param string $file File
		 * @param boolean $setRoot Extrapolate root as well?
		 *
		 * @return boolean Success
		 */
		public function setFile($file, $setRoot = false){
			if ( is_file( $file ) ) {
				$this->file = $file;
				if($setRoot) $this->root = dirname($file);
			}
			else if(is_dir($file) && $setRoot) $this->root($file);
			else return false;
			return true;
		}

		/**
		 * Get File
		 * @return string Plugin Main File
		 */
		public function getFile() {
			return $this->file;
		}

		/**
		 * Set Plugin Version
		 *
		 * Set the version number of this plugin.  It is applied to all script and style calls to help them clear the cache
		 *
		 * @param $version
		 */
		public function setVersion( $version ) {
			$this->version = $version;
		}

		/**
		 * Get Plugin Version
		 * @return string Version
		 */
		public function getVersion() {
			return $this->version;
		}
	}