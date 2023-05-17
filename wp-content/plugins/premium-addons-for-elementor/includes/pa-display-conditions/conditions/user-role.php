<?php
/**
 * User Role Condition Handler.
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
 * Class User_Role
 */
class User_Role extends Condition {

	/**
	 * Get Controls Options.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @return array|void  controls options
	 */
	public function get_control_options() {
		global $wp_roles;

		return array(
			'label'       => __( 'Value', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::SELECT2,
			'label_block' => true,
			'default'     => array(),
			'options'     => $wp_roles->get_names(),
			'multiple'    => true,
			'condition'   => array(
				'pa_condition_key' => 'user_role',
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

		if ( ! is_user_logged_in() || empty( $value ) ) {
			return false;
		}

		$value = ! is_array( $value ) ? (array) $value : $value; // temp: to make sure it's an array.

		$user = wp_get_current_user();

		$condition_result = ! empty( array_intersect( $value, $user->roles ) ) ? true : false;

		return Helper_Functions::get_final_result( $condition_result, $operator );

	}

}
