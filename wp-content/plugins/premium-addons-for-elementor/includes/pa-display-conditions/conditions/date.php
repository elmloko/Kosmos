<?php
/**
 * Date Condition Handler.
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
 * Class Date.
 */
class Date extends Condition {

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
			'label'          => __( 'Value', 'premium-addons-for-elementor' ),
			'type'           => Controls_Manager::DATE_TIME,
			'default'        => gmdate( 'Y/m/d' ),
			'label_block'    => true,
			'picker_options' => array(
				'format'     => 'd-m-Y',
				'enableTime' => false,
			),
			'condition'      => array(
				'pa_condition_key' => 'date',
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

		$value = strtotime( $value );

		$today = 'local' === $tz ? strtotime( Helper_Functions::get_local_time( 'd-m-Y' ) ) : strtotime( Helper_Functions::get_site_server_time( 'd-m-Y' ) );

		$condition_result = ! empty( $value ) && $today === $value ? true : false;

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
