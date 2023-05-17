<?php
/**
 * Returning User Condition Handler.
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
 * Class Return_Visitor
 */
class Return_Visitor extends Condition {

	/**
	 * Get Controls Options.
	 *
	 * @access public
	 * @since 4.9.21
	 *
	 * @return array|void  controls options
	 */
	public function get_control_options() {

		return array(
			'label'       => __( 'Value', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::SELECT,
			'options'     => array(
				'return' => __( 'Returning User', 'premium-addons-for-elementor' ),
			),
			'default'     => 'return',
			'label_block' => true,
			'condition'   => array(
				'pa_condition_key' => 'return_visitor',
			),
		);
	}

	/**
	 * Compare Condition Value.
	 *
	 * @access public
	 * @since 4.9.21
	 *
	 * @param array       $settings      element settings.
	 * @param string      $operator      condition operator.
	 * @param string      $value         condition value.
	 * @param string      $compare_val   condition value.
	 * @param string|bool $tz        time zone.

	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $value, $compare_val, $tz ) {

		$page_id = get_the_ID();

		if ( ! $page_id ) {
			return true;
		}

		$condition_result = isset( $_COOKIE[ 'isReturningVisitor' . $page_id ] );

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
