<?php
namespace PluginFramework\V_1_1;
/**
 * Trait MetaBox
 * @package PluginFramework\V_1_1
 *
 * This has not been tested.
 *
 * To add a metabox:
 *
 * A method must exist at "metabox_{name}" or "{post_type}_metabox_{name}"
 * A method must exist at "metabox_{name}" or "{post_type}_metabox_save_{name}"
 * An array should exist at "metabox_props_{name}" or "{post_type}_metabox_props_{name}"
 */
trait MetaBox {

	public $metabox_prefix = false;
	protected $metaboxes = [];
	protected $metabox_props = [];

	/**
	 * Set MetaBox Prefix
	 *
	 * Set the prefix that comes before all metabox names.
	 *
	 * Set to blank to remove the prefix
	 *
	 * Note: Don't add the underscore, we do that for you
	 *
	 * Note: We will clean the string for you: Lowercase it and replace spaces with underscores
	 *
	 * @param string $prefix Prefix
	 */
	public function setMetaBoxPrefix($prefix, $force = false) {
		if( $this->metabox_prefix === false || $force) $this->metabox_prefix = $this->sterilize($prefix);
	}

	public function getMetaBoxPrefix() {
		return $this->metabox_prefix == "" ? "" : $this->sterilize($this->metabox_prefix) . '_';
	}

	/**
	 * Generate MetaBox Name
	 *
	 * @param string ...$param Name Chunks
	 * @return string
	 */
	public function metabox_pre($param){
		$args = func_get_args();
		$pieces = [];

		foreach($args as $arg) if(!empty($arg)) $pieces[] = $this->sterilize($arg);

		return $this->getMetaBoxPrefix() . implode('_', $pieces);
	}

	public function canSaveMetaBox($type, $name, $id, $post) {
		if( wp_is_post_autosave( $id ) || wp_is_post_revision( $id ) ) return false;
		if($post->type != $type) return false;
		if( !current_user_can( 'edit_'.$type, $id ) ) return false;
		return $this->check_nonce($name);
	}

	public function getMetaBoxData($post_id, $name, $post_type = 'post') {
		$raw = get_post_custom( $post_id );
		$meta = [];

		foreach($this->metabox_props[$post_type][$name] as $prop) {
			$meta[$prop]  = ( isset( $raw[$this->getMetaBoxDataName($prop, $name)] ) ) ? $raw[$this->getMetaBoxDataName($prop, $name)][0] : '';
		}

		return $meta;
	}

	public function _metaboxes_hook_save_post($id, $post) {
		$post_type = $post->post_type;
		foreach($this->metaboxes[$post_type] as $name) {
			if($this->canSaveMetaBox($post_type, $name, $id, $post)) $this->saveMetaBoxData($id, $name, $post_type);
		}
	}

	public function getMetaBoxDataName($prop, $metabox) {
		return $this->metabox_pre('prop', $metabox, $prop);
	}

	public function saveMetaBoxData($id, $name, $post_type = 'post') {
		foreach ( $this->metabox_props[ $post_type ][ $name ] as $prop ) {
			if ( isset( $_REQUEST[ $this->getMetaBoxDataName($prop, $name) ] ) ) {
				update_post_meta( $id, $this->getMetaBoxDataName($prop, $name), sanitize_text_field( $_REQUEST[ $this->getMetaBoxDataName($prop, $name) ] ) );
			}
		}
	}

	/**
	 * Initializes MetaBoxes
	 */

	protected function init_metaboxes(){

		$metabox_props_vars = preg_grep('/^(.+_)?metabox_props_/', get_class_vars($this));

		foreach($metabox_props_vars as $var) {

			if(stristr('_metabox_',$var) !== false) list($post_type, $name) = explode("_metabox_", $var);
			else {
				$post_type = 'post';
				$name = str_replace( 'metabox_', '', $var );
			}

			if(empty($this->metabox_props[$post_type])) $this->metabox_props[$post_type] = [];
			$this->metabox_props[$post_type][$name] = $this->{$var};

		}

		$metabox_methods = preg_grep('/^(.+_)?metabox_/', get_class_methods($this));

		// Method method
		foreach($metabox_methods as $method) {

			if(empty($this->metaboxes[$post_type])) $this->metaboxes[$post_type] = [];
			$this->metaboxes[$post_type][] = $name;


			if(stristr('_metabox_',$method) !== false) list($post_type, $name) = explode("_metabox_", $method);
			else {
				$post_type = 'post';
				$name = str_replace( 'metabox_', '', $method );
			}


			$name = $this->metabox_pre( $name );

			add_metabox(
				$name,
				$this->pre_to_title($name),
				[ &$this, $method ],
				$post_type,
				'normal',
				'default'
			);

		}
	}


}