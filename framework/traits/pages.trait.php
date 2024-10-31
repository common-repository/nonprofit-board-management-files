<?php
	namespace PluginFramework\V_1_1;
	trait Pages {
		protected $home_page      = 'index';
		protected $menu           = false;
		protected $menu_title     = 'Plugin Framework';
		/**
		 * Set Home Page
		 *
		 * Which page is home.  Enter as title (ex: Manage Plugin)
		 *
		 * There must me a matching method (ex: page_manage_plugin)
		 *
		 * @param $page
		 */
		public function setHomePage($page){
			$this->home_page = $page;
		}

		public function setMenu($menu){
			$this->menu = $menu;
		}

		public function getMenu(){
			return $this->menu ?: $this->pre('page', $this->home_page);
		}


		/**
		 * Set Menu Title
		 * @param $title
		 */
		public function setMenuTitle($title){
			$this->menu_title = $title;
		}

		protected function all_pages(){
			$shortcode_methods = preg_grep('/^page_/', get_class_methods($this));

			$menu_init = false;

			$home_method = $this->concat('page', $this->home_page);

			if($this->menu != false){
				$menu_init = true;
			}
			else if(method_exists($this, $home_method)){
				add_menu_page($this->pre_to_title($home_method), $this->menu_title, $this->security_level, $this->getMenu(), [&$this, $home_method]);
				$menu_init = true;
			}

			// Method method
			foreach($shortcode_methods as $method) {

				$title = $this->pre_to_title($method);

				if($this->concat('page', $this->home_page) == $method) continue;

				if($menu_init) add_submenu_page( $this->getMenu(), $title, $title, $this->security_level, $this->pre($method), [&$this, $method ] );
				else {
					add_menu_page($title, $this->menu_title, $this->security_level, $this->getMenu(), [&$this, $method]);
					$menu_init = true;
				}
			}
		}


		public function pages_hook_admin_menu () {
			$this->all_pages();
		}


	}