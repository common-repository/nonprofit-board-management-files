<?php
	namespace PluginFramework\V_1_1;
	trait View {

		/**
		 * View
		 *
		 * An instance of the Mustache Engine
		 *
		 * @var \Mustache_Engine
		 */
		public $view;

		/**
		 * View Directory
		 *
		 * The location of the Mustache Tempates root Directory
		 *
		 * @var string
		 */
		protected $view_dir = 'views/';

		/**
		 * Initialize View
		 */
		protected function init_view() {
			$this->view = new \Mustache_Engine( [ 'loader' => new \Mustache_Loader_FilesystemLoader( $this->getRoot() .'/' . $this->view_dir ) ] );
		}

		/**
		 * Set View Directory
		 *
		 * Set the root directory for the mustache template engine
		 *
		 * @param string $dir Directory
		 */
		public function setViewDir( $dir ) {
			$this->view_dir = $dir;
		}

		/**
		 * Render Template
		 * @param string $template Template name (ex: filename.mustache would be filename)
		 * @param array $view Array of values to pass to template
		 *
		 * @return string
		 */
		public function render($template, $view = []) {

			$defaults = [
				'prefix' => $this->getPrefix(),
				'plugin_name' => $this->getName(),
				'plugin_version' => $this->getVersion(),
				'plugin_root' => dirname($this->getRoot())
			];

			return $this->view->render($template, array_merge($defaults, (array) $view));


		}

	}
