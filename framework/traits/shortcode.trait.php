<?php
	namespace PluginFramework\V_1_1;
	trait Shortcode {

		public $shortcode_prefix = false;
		protected $shortcodes = [];

		/**
		 * Set Shortcode Prefix
		 *
		 * Set the prefix that comes before all shortcode names.
		 *
		 * Set to blank to remove the prefix
		 *
		 * Note: Don't add the underscore, we do that for you
		 *
		 * Note: We will clean the string for you: Lowercase it and replace spaces with underscores
		 *
		 * @param string $prefix Prefix
		 */
		public function setShortcodePrefix($prefix, $force = false) {
			if($this->shortcode_prefix === false || $force) $this->shortcode_prefix = $this->sterilize($prefix);
		}

		public function getShortcodePrefix() {
			return $this->shortcode_prefix == "" ? "" : $this->sterilize($this->shortcode_prefix) . '_';
		}

		/**
		 * Generate Shortcode Name
		 *
		 * @param string ...$param Name Chunks
		 * @return string
		 */
		public function shortcode_pre($param){
			$args = func_get_args();
			$pieces = [];

			foreach($args as $arg) if(!empty($arg)) $pieces[] = $this->sterilize($arg);

			return $this->getShortcodePrefix() . implode('_', $pieces);
		}

		/**
		 * Add Shortcode
		 *
		 * A method must exist at "shortcode_{name}"
		 *
		 * @param string $name Shortcode Name - Method "shortcode_{$name}" must exist
		 * @param callback $func Function
		 */
		public function addShortcode($name, $func) {
			$this->shortcodes[$name] = $func;
		}

		/**
		 * Add Shortcodes
		 *
		 * A method must exist at "shortcode_{name}"
		 *
		 * @param string[] $names Shortcode Names
		 */
		public function addShortcodes($names = []) {
			foreach($names as $name => $func) $this->addShortcode($name, $func);
		}


		/**
		 * Current Include Shortcode Attributes
		 *
		 * @author Mike Flynn
		 * @since 4.0.0
		 * @var array Shortcode Parameter Default Values
		 */
		protected $attributes = [];

		/**
		 * Default Include Shortcode Attributes
		 *
		 * @author Mike Flynn
		 * @since 4.0.0
		 * @var array Shortcode Parameter Default Values
		 */
		protected $default_attributes = [];

		protected function setAttributes($shortcode, $attributes = []) {
			$this->push($this->shortcode_pre($shortcode, 'atts'), $attributes);
		}

		protected function getAttributes($shortcode) {
			return $this->attributes[$shortcode];
		}

		protected function setDefaultAttributes($shortcode, $attributes = []) {
			$this->default_attributes[$shortcode] = $attributes;
		}

		protected function loadAttributes($shortcode){
			if(!isset($this->default_attributes[$shortcode]) || empty($this->default_attributes[$shortcode])){
				if(isset($this->{'shortcode_attributes_'.$shortcode})){
					$this->setDefaultAttributes($shortcode, $this->{'shortcode_attributes_'.$shortcode});
				}
			}
			$this->attributes[$shortcode] = $this->pull(
				$this->concat($shortcode, 'atts'),
				! empty($this->default_attributes[$shortcode]) ? $this->default_attributes[$shortcode] : []
			);
		}

		protected function atts($shortcode, $a = []) {
			return shortcode_atts( $this->getAttributes($shortcode) ?: [], $a, $this->shortcode_pre($shortcode));
		}

		/**
		 * Initializes Shortcodes
		 */
		protected function init_shortcodes(){

			$shortcode_methods = preg_grep('/^shortcode_/', get_class_methods($this));

			// Method method
			foreach($shortcode_methods as $method) {
				$name = $this->shortcode_pre( str_replace( 'shortcode_', '', $method ) );
				add_shortcode( $name, [ &$this, $method ] );
				$this->loadAttributes($name);
			}

		}

	}