<?php
/**
 * Url_String Condition Handler.
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
 * Class Url_String.
 */
class Url_String extends Condition {

	/**
	 * Get Controls Options.
	 *
	 * @access public
	 * @since 4.9.4
	 *
	 * @return array|void  controls options
	 */
	public function get_control_options() {

		return array(
			'label'       => __( 'Value', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'label_block' => true,
			'description' => __( 'Enter the string you want to check if exists in the page URL.', 'premium-addons-for-elementor' ),
			'condition'   => array(
				'pa_condition_key' => 'url_string',
			),
		);
	}


	/**
	 * Compare Condition Value.
	 *
	 * @access public
	 * @since 4.9.4
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

		$url = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );

		if ( ! $url ) {
			return false;
		}

		$condition_result = false !== strpos( $url, $value ) ? true : false;

		return Helper_Functions::get_final_result( $condition_result, $operator );

	}

}
