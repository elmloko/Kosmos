<?php
/**
 * Shortcode Condition Handler.
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
 * Class Shortcode.
 */
class Shortcode extends Condition {

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
			'type'        => Controls_Manager::TEXTAREA,
			'options'     => array(),
			'label_block' => true,
			'condition'   => array(
				'pa_condition_key' => 'shortcode',
			),
		);

	}

	/**
	 * Get Value Controls Options.
	 *
	 * @access public
	 * @since 4.9.4
	 *
	 * @return array  controls options.
	 */
	public function add_value_control() {

		return array(
			'label'       => __( 'Shortcode', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'label_block' => true,
			'description' => __( 'Insert the shortcode you want to check its result value.', 'premium-addons-for-elementor' ),
			'condition'   => array(
				'pa_condition_key' => 'shortcode',
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
	 * @param string|void $compare_val     compare value.
	 * @param string      $shortcode           shortcode.
	 * @param string|bool $tz        time zone.
	 *
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $compare_val, $shortcode, $tz ) {

		if ( empty( $shortcode ) ) {
			return false;
		}

		$return_value = do_shortcode( shortcode_unautop( $shortcode ) );

		$condition_result = $return_value == $compare_val ? true : false;

		return Helper_Functions::get_final_result( $condition_result, $operator );

	}

}
