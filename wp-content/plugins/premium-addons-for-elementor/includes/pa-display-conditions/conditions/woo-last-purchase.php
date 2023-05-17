<?php
/**
 * Woocommerce last purchase Condition Handler.
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
 * Class Woo_Last_Purchase.
 */
class Woo_Last_Purchase extends Condition {

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
			'label'          => __( 'At or Before', 'premium-addons-for-elementor' ),
			'type'           => Controls_Manager::DATE_TIME,
			'default'        => gmdate( 'Y/m/d' ),
			'label_block'    => true,
			'picker_options' => array(
				'format'     => 'Y-m-d',
				'enableTime' => false,
			),
			'label_block'    => true,
			'condition'      => array(
				'pa_condition_key' => 'woo_last_purchase',
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
	 * @param string|bool $tz            time zone.
	 *
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $value, $compare_val, $tz ) {

		$args = array(
			'customer_id' => get_current_user_id(),
			'status'      => array( 'wc-completed' ),
			'limit'       => 1,
			'orderby'     => 'date_completed',
			'order'       => 'DESC',
		);

		$order = wc_get_orders( $args );

		$date_completed = $order && $order[0] ? date( 'Y-m-d', strtotime( $order[0]->get_Date_completed() ) ) : false;

		$condition_result = $value >= $date_completed ? true : false;

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
