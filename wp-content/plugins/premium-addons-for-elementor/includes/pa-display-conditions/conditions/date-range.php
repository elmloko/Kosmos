<?php
/**
 * Date Range Condition Handler.
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
 * Class Date_Range.
 */
class Date_Range extends Condition {

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
				'mode'       => 'range',
			),
			'condition'      => array(
				'pa_condition_key' => 'date_range',
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

		$range_date = explode( ' to ', $value );

		if ( ! is_array( $range_date ) || 2 !== count( $range_date ) ) {
			return;
		}

		$start = strtotime( $range_date[0] );
		$end   = strtotime( $range_date[1] );

		$today = 'local' === $tz ? strtotime( Helper_Functions::get_local_time( 'd-m-Y' ) ) : strtotime( Helper_Functions::get_site_server_time( 'd-m-Y' ) );

		$condition_result = ( ( $today >= $start ) && ( $today <= $end ) );

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
