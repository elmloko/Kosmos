<?php
/**
 * Condition blueprint for all available conditions.
 */

namespace PremiumAddons\Includes\PA_Display_Conditions\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Abstract Class Condition.
 */
abstract class Condition {

	/**
	 * Get Controls Options.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @return array|void  controls options
	 */
	public function get_control_options() {}

	/**
	 * Compare Condition Value.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @param array       $settings element settings.
	 * @param string      $operator condition operator.
	 * @param string      $value    condition value.
	 * @param string|bool $tz        time zone.
	 *
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $value, $compare_val, $tz ) {}

	/**
	 * Compare Condition Value.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @param array       $settings element settings.
	 * @param string      $operator condition operator.
	 * @param string      $value    condition value.
	 * @param string|bool $tz        time zone.
	 * @param string      $method        location detect method.
	 *
	 * @return bool|void
	 */
	public function compare_location( $settings, $operator, $value, $compare_val, $tz, $method ) {}

}
