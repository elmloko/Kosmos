<?php
/**
 * Time Range Condition Handler.
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
 * Class Time_Range.
 */
class Time_Range extends Condition {

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
			'label'          => __( 'To', 'premium-addons-for-elementor' ),
			'type'           => Controls_Manager::DATE_TIME,
			'label_block'    => true,
			'picker_options' => array(
				'noCalendar' => true,
				'enableTime' => true,
				'dateFormat' => 'H:i',
			),
			'condition'      => array(
				'pa_condition_key' => 'time_range',
			),
		);
	}

	/**
	 * Get Value Controls Options.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @return array  controls options.
	 */
	public function add_value_control() {

		return array(
			'label'          => __( 'From', 'premium-addons-for-elementor' ),
			'type'           => Controls_Manager::DATE_TIME,
			'label_block'    => true,
			'picker_options' => array(
				'noCalendar' => true,
				'enableTime' => true,
				'dateFormat' => 'H:i',
			),
			'condition'      => array(
				'pa_condition_key' => 'time_range',
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
	 * @param string      $to             range start value.
	 * @param string      $from           range end value.
	 * @param string|bool $tz             time zone.
	 *
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $to, $from, $tz ) {

		if ( ! empty( $to ) ) {
			$to = strtotime( gmdate( 'H:i', strtotime( $to ) ) );
		}

		if ( ! empty( $from ) ) {
			$from = strtotime( gmdate( 'H:i', strtotime( $from ) ) );
		}

		$now = 'local' === $tz ? strtotime( Helper_Functions::get_local_time( 'H:i' ) ) : strtotime( Helper_Functions::get_site_server_time( 'H:i' ) );

		if ( ! empty( $from ) && ! empty( $to ) ) {
			$condition_result = ( ( $now >= $from ) && ( $now <= $to ) );
		} elseif ( empty( $from ) ) {
			$condition_result = $now <= $to;
		} else {
			$condition_result = $now >= $from;
		}

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
