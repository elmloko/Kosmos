<?php

namespace Saltus\WP\Plugin\Saltus\InteractiveMaps\Plugin\Utils;

use Saltus\WP\Plugin\Saltus\InteractiveMaps\Plugin\MapList;

/**
 * Manage Assets like scripts and styles.
 */
class MapListCurrent {

	public $value;

	/**
	 * The plugin's instance.
	 *
	 * @var Core
	 */
	public $core;

	/**
	 * Define Assets
	 */
	public function __construct( $core ) {

		$this->core = $core;
		$this->register_shortcode();

	}

	public function register_shortcode() {
		add_shortcode( 'map-title-current', array( $this, 'render_map_current_title' ) );
	}

	public function render_map_current_title( $atts ) {

		$title = $this->get_current();

		if ( $title !== '' ) {
			/* translators: %s is the name of the current map displaying */
			$html = sprintf( __( '%s Map Preview', 'interactive-geo-maps' ), $title );
		} else {
			$html = __( 'World Map Preview', 'interactive-geo-maps' );
		}

		return sprintf( '<h2>%s</h2>', $html );

	}

	public function get_current() {

		$value       = isset( $_GET['map'] ) ? sanitize_text_field( $_GET['map'] ) : '';
		$this->value = $value;

		$map_list = new MapList();
		$options  = json_decode( $map_list->maps, true );

		$label = '';

		if ( is_array( $options ) && ! empty( $options ) ) {

			foreach ( $options as $option_key => $option ) {

				if ( is_array( $option ) && ! empty( $option ) ) {

					foreach ( $option as $sub_key => $sub_value ) {

						if ( $value === $sub_key ) {
							$label = $sub_value;
							return $label;
						}
					}
				}
			}
		}

		return $label;
	}
}
