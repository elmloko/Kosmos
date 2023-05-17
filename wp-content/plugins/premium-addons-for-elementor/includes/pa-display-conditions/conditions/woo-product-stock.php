<?php
/**
 * Woocommerce Product Stock Condition Handler.
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
 * Class Woo_Product_Stock.
 */
class Woo_Product_Stock extends Condition {

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
			'description' => __( 'Set the minimum quantity in stock to be checked. 0 means out of stock', 'premium-addons-for-elementor' ),
			'min'         => 0,
			'condition'   => array(
				'pa_condition_key' => 'woo_product_stock',
			),
		);
	}


	/**
	 * Compare Condition Value.
	 *
	 * @access public
	 * @since 4.7.2
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

		$product_id = get_queried_object_id();

		if ( ! $product_id ) {
			return true;
		}

		$type = get_post_type();

		if ( 'product' !== $type ) {
			return true;
		}

		$product = wc_get_product( $product_id );

		$product_quantity = $product->get_stock_quantity();

		if ( 0 === $value ) {
			// Check if product is in stock or backorder is allowed.
			$product_quantity = $product->is_in_stock() || $product->backorders_allowed();

			$condition_result = $value == $product_quantity ? true : false;
		} else {
			$condition_result = $value <= $product_quantity ? true : false;
		}

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
