<?php
namespace NonprofitBoardManagement\Files;
class Plugin extends \PluginFramework\V_1_1\Core {

	protected $requirements_met;

	function __construct($name, $ver, $file) {

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if(\is_plugin_active('nonprofit-board-management/nonprofit-board-management.php')) $this->requirements_met = true;
		else $this->requirements_met = false;

		$this->setShortcodePrefix("");
		$this->setSecurityLevel('edit_board_events');

		$this->addAdminScript('uploader', 'uploader.min.js', ['jquery'] );
		$this->addAdminScript('admin', 'admin.min.js', ['jquery'] );

		$this->init($name, $ver, $file);
	}

	function getMeta($id, $meta = []) {
		$raw = get_post_custom( $id );

		$meta['room']           = ( isset( $raw['_room'] ) ) ? $raw['_room'][0] : false;

		$meta['agenda']         = ( isset( $raw['_agenda'] ) ) ? $raw['_agenda'][0] : false;
		$meta['minutes']        = ( isset( $raw['_minutes'] ) ) ? $raw['_minutes'][0] : false;

		$meta['agenda_url']     = $meta['agenda'] ? wp_get_attachment_url( $meta['agenda'] ) : false;
		$meta['minutes_url']    = $meta['minutes'] ? wp_get_attachment_url( $meta['minutes'] ) : false;

		return $meta;

	}

	function files_hook_admin_enqueue_scripts(){
		if ( ! did_action( 'wp_enqueue_media' ) ) wp_enqueue_media();
	}

	function image_uploader_field( $name, $value, $url) {

		return $this->render('uploader', [
			"empty" => $value == '',
			"name"  => $name,
			"value" => $value,
			"url"   => $url
		]);
	}

	function files_hook_winbm_after_event_meta_fields($board_event) {

		$meta = $this->getMeta($board_event->ID, []);

		echo $this->render('form-row', ["name" => "room",       "label" => "Location Label",    "value" => $meta['room'] ]);
		echo $this->render('form-row', ["name" => "agenda",     "label" => "Agenda (pdf)",      "value" => $meta['agenda'],     "input" => $this->image_uploader_field('agenda', $meta['agenda'], $meta['agenda_url']) ]);
		echo $this->render('form-row', ["name" => "minutes",    "label" => "Minutes (pdf)",     "value" => $meta['minutes'],    "input" => $this->image_uploader_field('minutes', $meta['minutes'], $meta['minutes_url']) ]);
	}


	function files_hook_save_post_board_events($id, $board_event = false) {

		if( wp_is_post_autosave( $id ) || wp_is_post_revision( $id ) ) {
			return false;
		}

		if( !current_user_can( 'edit_board_event', $id ) ){
			return false;
		}

		if ( !isset( $_REQUEST['_event_details_nonce'] ) || !wp_verify_nonce( $_REQUEST['_event_details_nonce'], 'event_details_nonce' ) ){
			return false;
		}

		// Save Data

		if( isset($_REQUEST['minutes'] ) )  update_post_meta( $id, '_minutes',  sanitize_text_field( $_REQUEST['minutes'] ) );
		if( isset($_REQUEST['agenda'] ) )   update_post_meta( $id, '_agenda',   sanitize_text_field( $_REQUEST['agenda'] ) );
		if( isset($_REQUEST['room'] ) )     update_post_meta( $id, '_room',     sanitize_text_field( $_REQUEST['room'] ) );
	}

	public $shortcode_attributes_meetings = [];

	function shortcode_meetings($attributes, $content){
		$args = array(
			'post_type'         => 'board_events',
			'posts_per_page'    => 1000,
			'meta_key'          => '_start_date_time',
			'orderby'           => 'meta_value_num',
			'order'             => 'DESC'
		);
		$upcoming_events = get_posts( $args );

		if(!$this->requirements_met) return $this->render('nomeetings', []);

		$p = dirname( plugin_dir_path(__FILE__) ) . "/nonprofit-board-management/includes/class-board-events.php";

		if(! class_exists("WI_Board_Events") && file_exists($p)) include_once($p);

		if(! class_exists("WI_Board_Events")) return $this->render('nomeetings', []);

		//If no upcoming events show the user a message.
		if( empty( $upcoming_events ) ) return $this->render('nomeetings', []);
		$o = ['meetings' => []];

		foreach( $upcoming_events as $event ){
			$e = $this->getMeta($event->ID, \WI_Board_Events::retrieve_board_event_meta( $event->ID ));

			$e['map']       = 'https://maps.google.com/maps?q=' . str_replace( ' ', '+', $e['street'] . ' ' . $e['area'] );
			$e['id']        = $event->ID;
			$e['title']     = $event->post_title;
			$e['date']      = \WI_Board_Events::format_event_times($e['start_date_time'], '', true );
			$e['location']  = html_entity_decode(\WI_Board_Events::get_event_location($e, false ));
			$o['meetings'][]  = $e;
		}

		return $this->render('meetings', $o);

	}

}