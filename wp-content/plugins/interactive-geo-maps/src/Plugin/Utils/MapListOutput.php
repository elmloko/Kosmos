<?php

namespace Saltus\WP\Plugin\Saltus\InteractiveMaps\Plugin\Utils;

use Saltus\WP\Plugin\Saltus\InteractiveMaps\Plugin\MapList;

/**
 * Manage Assets like scripts and styles.
 */
class MapListOutput {

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
		add_shortcode( 'map-list', array( $this, 'render_map_list' ) );
	}

	public function render_map_list( $atts ) {

		$map_list = new MapList();
		$options  = json_decode( $map_list->maps, true );

		if( isset( $atts['group'] ) && isset( $options[ $atts['group'] ] ) ){
			$options = [ $atts['group'] => $options[ $atts['group'] ] ];
		}

		$html = $this->build_list( $options );

		return $html;

	}

	public function build_list( $options ) {

		global $wp;
		$html = '<div class="igm_preview_list">';

		foreach ( $options as $option_key => $option ) {

			if ( is_array( $option ) && ! empty( $option ) ) {

				$html .= '<h2 class="igm_preview_list_title">' . $option_key . '</h2>';

				// prepare array of links
				$list = array();

				foreach ( $option as $sub_key => $sub_value ) {

					$sub_value = preg_replace( '/\([^)]+\)/', '', $sub_value );
					$key       = __( 'Default', 'interactive-geo-maps' );

					if ( strpos( $sub_value, 'Low' ) !== false ) {
						$sub_label = str_replace( ' Low', '', $sub_value );
						/* translators: type of quality */
						$key = __( 'Low', 'interactive-geo-maps' );
					} elseif ( strpos( $sub_value, 'High' ) !== false ) {
						$sub_label = str_replace( ' High', '', $sub_value );
						/* translators: type of quality */
						$key = __( 'High', 'interactive-geo-maps' );
					} elseif ( strpos( $sub_value, 'Ultra' ) !== false ) {
						$sub_label = str_replace( ' Ultra', '', $sub_value );
						/* translators: type of quality */
						$key = __( 'Ultra', 'interactive-geo-maps' );

					} else {
						$sub_label = $sub_value;
					}

					$url  = add_query_arg( 'map', $sub_key, home_url( $wp->request ) );
					$link = '<a href="' . $url . '">' . $key . '</a>';

					$list[ $sub_label ][ $key ] = $link;

				}

				$html .= '<ul class="igm_preview_main_list">';

				foreach ( $list as $sub_key => $sub_value ) {

					$html .= '<li>' . $sub_key . ' <span class="igm_small_list_text">[';

					$i   = 0;
					$len = count( $sub_value );

					foreach ( $sub_value as $li_key => $li_value ) {

						if ( $i === $len - 1 ) {
							$html .= $li_value;
						} else {
							$html .= $li_value . ' | ';
						}
						$i++;
					}

					$html .= ']</span></li>';

				}

				$html .= '</ul>';

			}
		}

		$html .= '</div>';

		return $html;
	}
}
