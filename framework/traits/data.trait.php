<?php

	namespace PluginFramework\V_1_1;
	trait Data {
		protected $data = [];

		protected function load($i, $default) {
			$this->data[$i] = get_option($this->pre($i), $default);
		}

		protected function set($i, $data){
			$this->data[$i] = $data;
		}

		protected function save($i = false){
			if($i !== false) update_option($this->pre($i), $this->data[$i]);
			else $this->save_all();
		}

		protected function save_all() {
			foreach($this->data as $i => $d) update_option($this->pre($i), $d);
		}

		/* ********************* */
		/* Data Access Functions */
		/* ********************* */

		/**
		 * Get Data
		 *
		 * @param string $i Index
		 * @param mixed $default Default Value
		 *
		 * @return mixed Data
		 */
		public function pull($i, $default = []){
			if(empty($this->data[$i])) $this->load($i, $default);
			return $this->data[$i];
		}

		/**
		 * Set Data
		 *
		 * @param string $i Index
		 * @param mixed $data Data
		 */
		public function push($i, $data) {
			$this->set($i, $data);
			$this->save($i);
		}

		public function Data ($key, $data = false){
			if(! $data) return $this->pull($key, false);
			else return $this->push($key, $data);
		}

	}