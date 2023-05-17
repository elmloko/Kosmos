<?php
/**
 * Device Condition Handler.
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
 * Class Device
 */
class Device extends Condition {

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
			'default'     => 'desktop',
			'label_block' => true,
			'options'     => array(
				'desktop' => __( 'Desktop', 'premium-addons-for-elementor' ),
				'tablet'  => __( 'Tablet', 'premium-addons-for-elementor' ),
				'mobile'  => __( 'Mobile', 'premium-addons-for-elementor' ),
			),
			'multiple'    => true,
			'condition'   => array(
				'pa_condition_key' => 'device',
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

		require_once PREMIUM_ADDONS_PATH . 'includes/pa-display-conditions/mobile-detector.php';

		$detect = new \PA_Mobile_Detect();

		$device = 'desktop';
		if ( $detect->isTablet() ) {
			$device = 'tablet';
		} elseif ( $detect->isMobile() ) {
			$device = 'mobile';
		}

		if ( is_array( $value ) ) {
			$condition_result = ! empty( $value ) && ( in_array( $device, $value, true ) ) ? true : false;
		} else {
			$condition_result = ! empty( $value ) && ( $device === $value ) ? true : false;
		}

		return Helper_Functions::get_final_result( $condition_result, $operator );

	}

}
