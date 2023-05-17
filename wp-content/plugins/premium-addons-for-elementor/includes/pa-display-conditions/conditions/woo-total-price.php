<?php
/**
 * Woocommerce Total Amount in Cart Condition Handler.
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
 * Class Woo_Total_Price.
 */
class Woo_Total_Price extends Condition {

	/**
	 * Get Value Controls Options.
	 *
	 * @access public
	 * @since 4.7.3
	 *
	 * @return array  controls options.
	 */
	public function get_control_options() {

		return array(
			'label'       => __( 'Equal or Higher than', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'description' => __( 'Set the minimum amount in the cart to be checked.', 'premium-addons-for-elementor' ),
			'min'         => 0,
			'condition'   => array(
				'pa_condition_key' => 'woo_total_price',
			),
		);

	}

	/**
	 * Get Value Controls Options.
	 *
	 * @access public
	 * @since 4.7.3
	 *
	 * @return array  controls options.
	 */
	public function add_value_control() {

		return array(
			'label'       => __( 'Source', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::SELECT,
			'options'     => array(
				'subtotal' => __( 'Subtotal Amount', 'premium-addons-for-elementor' ),
				'total'    => __( 'Total Amount', 'premium-addons-for-elementor' ),
			),
			'default'     => 'subtotal',
			'label_block' => true,
			'condition'   => array(
				'pa_condition_key' => 'woo_total_price',
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

		$cart = WC()->cart;

		if ( $cart->is_empty() ) {
			return false;
		}

		if ( 'total' === $value ) {
			$cart_total = $cart->total;
		} else {
			$cart_total = $cart->get_displayed_subtotal();
		}

		$condition_result = (int) $compare_val <= $cart_total ? true : false;

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
