<?php
/**
 * Day Condition Handler.
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
 * Class Day
 */
class Day extends Condition {

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
			'type'        => Controls_Manager::SELECT2,
			'default'     => array( 'sunday' ),
			'label_block' => true,
			'options'     => array(
				'sunday'    => __( 'Sunday', 'premium-addons-for-elementor' ),
				'monday'    => __( 'Monday', 'premium-addons-for-elementor' ),
				'tuesday'   => __( 'Tuesday', 'premium-addons-for-elementor' ),
				'wednesday' => __( 'Wednesday', 'premium-addons-for-elementor' ),
				'thursday'  => __( 'Thursday', 'premium-addons-for-elementor' ),
				'friday'    => __( 'Friday', 'premium-addons-for-elementor' ),
				'saturday'  => __( 'Saturday', 'premium-addons-for-elementor' ),
			),
			'multiple'    => true,
			'condition'   => array(
				'pa_condition_key' => 'day',
			),
		);
	}

	/**
	 * Compare Condition Value.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @param array       $settings      element settings.
	 * @param string      $operator      condition operator.
	 * @param string      $value         condition value.
	 * @param string      $compare_val   compare value.
	 * @param string|bool $tz        time zone.
	 *
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $value, $compare_val, $tz ) {

		$today = 'local' === $tz ? Helper_Functions::get_local_time( 'l' ) : Helper_Functions::get_site_server_time( 'l' );

		$condition_result = ! empty( $value ) && in_array( strtolower( $today ), $value, true ) ? true : false;

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
