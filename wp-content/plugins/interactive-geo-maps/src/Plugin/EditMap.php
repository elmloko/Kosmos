<?php

namespace Saltus\WP\Plugin\Saltus\InteractiveMaps\Plugin;

use Saltus\WP\Plugin\Saltus\InteractiveMaps\Core;
use Saltus\WP\Plugin\Saltus\InteractiveMaps\Plugin\Map;

/**
 * Manage available click actions
 */
class EditMap {

	public $core;

	/**
	 * Define Actions
	 *
	 * @param Core $core This plugin's instance.
	 */
	public function __construct( Core $core ) {

		add_action( 'wp_ajax_map_form_data', array( $this, 'process_form_data' ) );
		$this->core = $core;

	}

	public function sanitize_meta( $meta ) {

		if ( ! is_array( $meta ) ) {
			return $meta;
		}

		foreach ( $meta as $key => $value ) {
			$meta[ $key ] = sanitize_meta( $key, $value, 'post' );
		}

		return $meta;
	}


	public function process_form_data() {

		check_ajax_referer( 'igmaps_edit', 'security' );
		$form_data = array_map( array( $this, 'sanitize_meta' ), $_POST );

		$form_data['map_info']['id']                           = sanitize_key( $form_data['post_ID'] );
		$form_data['map_info']['container']                    = 'map_' . $form_data['map_info']['id'];
		$form_data['map_info']['admin']                        = '1';
		// we need this here so that the map manager knows which field to populate
		$form_data['map_info']['regionLabelCustomCoordinates'] = 'regionLabelCustomCoordinates';

		// sanitize data
		// new map to process meta
		$map  = new Map( $this->core );
		$meta = $map->prepare_meta( $form_data['map_info'], $form_data['map_info']['id'] );

		echo wp_json_encode( $meta );
		die();
	}

}
