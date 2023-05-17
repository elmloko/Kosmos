<?php
/**
 * Woocommerce Orders Condition Handler.
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
 * Class Woo_Orders.
 */
class Woo_Orders extends Condition {

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
			'label'       => __( 'Number of Items', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'min'         => 0,
			'description' => __( 'Enter 0 to check if empty. Any other value will be the minimum number of items to check.', 'premium-addons-for-elementor' ),
			'condition'   => array(
				'pa_condition_key' => 'woo_orders',
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
			'label'       => __( 'Status', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'in-cart',
			'label_block' => true,
			'options'     => array(
				'in-cart'   => __( 'In Cart', 'premium-addons-for-elementor' ),
				'purchased' => __( 'Purchased', 'premium-addons-for-elementor' ),
			),
			'condition'   => array(
				'pa_condition_key' => 'woo_orders',
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
	public function compare_value( $settings, $operator, $compare_val, $value, $tz ) {

		if ( '' === $compare_val ) {
			return true;
		}

		if ( 'in-cart' === $value ) {
			$item_count = WC()->cart->get_cart_contents_count();
		} else {

			$args = array(
				'customer_id' => get_current_user_id(),
				'status'      => array( 'wc-completed' ),
			);

			$item_count = count( wc_get_orders( $args ) );
		}

		if ( 0 === (int) $compare_val ) {
			$condition_result = (int) $compare_val === $item_count ? true : false;

		} else {
			$condition_result = (int) $compare_val <= $item_count ? true : false;
		}

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
