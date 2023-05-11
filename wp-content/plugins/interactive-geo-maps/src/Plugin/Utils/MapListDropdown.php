<?php

namespace Saltus\WP\Plugin\Saltus\InteractiveMaps\Plugin\Utils;

use Saltus\WP\Plugin\Saltus\InteractiveMaps\Plugin\MapList;

/**
 * Manage Assets like scripts and styles.
 */
class MapListDropdown {

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

		$this->value = isset( $_GET['map'] ) ? sanitize_text_field( $_GET['map'] ) : '';
		$this->register_shortcode();

	}

	public function register_shortcode() {
		add_shortcode( 'map-dropdown', array( $this, 'render_map_dropdown' ) );
	}

	public function render_map_dropdown( $atts ) {

		$map_list = new MapList();
		$options  = json_decode( $map_list->maps, true );

		$html = $this->build_select( $options );

		return $html;

	}

	public function build_select( $options ) {

		$html        = '';
		$this->value = ( is_array( $this->value ) ) ? $this->value : array_filter( (array) $this->value );

		if ( is_array( $options ) && ! empty( $options ) ) {

			global $wp;

			$html .= '<select class="igm_preview_map_list_dropdown" name="" onchange="if (this.value) window.location.href=this.value">';
			$html .= '<option value="">' . __( 'Select a map to preview', 'interactive-geo-maps' ) . '</option>';

			foreach ( $options as $option_key => $option ) {

				if ( is_array( $option ) && ! empty( $option ) ) {

					$html .= '<optgroup label="' . $option_key . '">';

					foreach ( $option as $sub_key => $sub_value ) {
						$selected  = ( in_array( $sub_key, $this->value ) ) ? ' selected' : '';
						$sub_value = preg_replace( '/\([^)]+\)/', '', $sub_value );

						$url = add_query_arg( 'map', $sub_key, home_url( $wp->request ) );

						$html .= '<option value="' . $url . '" ' . $selected . '>' . $sub_value . '</option>';
					}

					$html .= '</optgroup>';

				} else {
					$selected = ( in_array( $option_key, $this->value ) ) ? ' selected' : '';
					$option   = preg_replace( '/\([^)]+\)/', '', $option );
					$url      = add_query_arg( 'map', $sub_key, home_url( $wp->request ) );
					$html    .= '<option value="' . $url . '" ' . $selected . '>' . $option . '</option>';
				}
			}

			$html .= '</select>';

		}

		return $html;
	}
}
