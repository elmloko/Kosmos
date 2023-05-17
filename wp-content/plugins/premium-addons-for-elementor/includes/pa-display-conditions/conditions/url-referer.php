<?php
/**
 * Url_Referer Condition Handler.
 */

namespace PremiumAddons\Includes\PA_Display_Conditions\Conditions;

// Elementor Classes.
use Elementor\Controls_Manager;

// PA Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Url_Referer.
 */
class Url_Referer extends Condition {

	/**
	 * Get Controls Options.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @return array|void  controls options
	 */
	public function get_control_options() {

		return array(
			'label'       => __( 'Value', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::TEXTAREA,
			'label_block' => true,
			'placeholder' => 'param1=value1
param2=value2',
			'description' => __( 'Enter each refer parameter on a separate line as pairs of param=value', 'premium-addons-for-elementor' ),
			'condition'   => array(
				'pa_condition_key' => 'url_referer',
			),
		);
	}


	/**
	 * Compare Condition Value.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @param array       $settings       element settings.
	 * @param string      $operator       condition operator.
	 * @param string      $value          condition value.
	 * @param string      $compare_val    compare value.
	 * @param string|bool $tz        time zone.
	 *
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $value, $compare_val, $tz ) {

		if ( ! isset( $_SERVER['REQUEST_URI'] ) || empty( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}

		$url = wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );

		if ( ! $url || ! isset( $url['query'] ) || empty( $url['query'] ) ) {
			return false;
		}

		$query_params = explode( '&', $url['query'] );

		$value = explode( "\n", sanitize_textarea_field( $value ) );

		foreach ( $value as $index => $param ) {

			$is_strict = strpos( $param, '=' );
			if ( ! $is_strict ) {

				$ref = isset( $_GET[ $param ] ) ? sanitize_text_field( wp_unslash( $_GET[ $param ] ) ) : '';

				$value[ $index ] = $value[ $index ] . '=' . rawurlencode( $ref );
			}
		}

		$condition_result = ! empty( array_intersect( $value, $query_params ) ) ? true : false;

		return Helper_Functions::get_final_result( $condition_result, $operator );

	}

}
